<?php

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

//Database functions
$roopath = realpath(dirname(__FILE__).'/../../..');
require_once $roopath.DIRECTORY_SEPARATOR.'init.php';
require_once $roopath.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'database.php';

require_once $roopath.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'modulesgarden'.DIRECTORY_SEPARATOR.'class.ModuleInformationClient.php';
$idProduct    = (int) intval($_REQUEST['id']);
$productQuery = ModuleInformationClient::mysql_safequery('SELECT * FROM tblproducts WHERE id = ? LIMIT 1', array($idProduct));
$productRow   = mysql_fetch_assoc($productQuery);

$username   =   $productRow['configoption1'];
$password   =   $productRow['configoption2'];


if(isset($_REQUEST['stormajax']) && $_REQUEST['stormajax'] == 'load-image') {
    //ob_clean();
    $conf_id = $_REQUEST['conf_id'];

    require_once $roopath.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormImage.php';
    $image = new StormOnDemandStormImage($username, $password, 'bleed');
    $ret = $image->lists();

    if($error = $image->getError())
    {
        echo '<p style="color: red">'.$error.'</p>';
        die();
    }

    echo '<table class="datatable" style="width: 100%">
            <tr>
                <th>Template</th>
                <th>Source Hostname</th>
                <th>Time take</th>
            </tr>
          ';
    foreach($ret['items'] as $item)
    {
        if($item['deprecated'] == 1)
        {
            continue;
        }
        echo '<tr>
                <td><input class="storm-image" type="radio" name="image-id" id="image-id-'.$item['id'].'" value="'.$item['id'].'" '.($item['id'] == $conf_id ? 'checked="checked"' : '').' /> <label for="image-id-'.$item['id'].'">'.$item['template_description'].'</label></td>
                <td><label for="image-id-'.$item['id'].'">'.$item['source_hostname'].'</label></td>
                <td><label for="image-id-'.$item['id'].'">'.$item['time_taken'].'</label></td>
              </tr>';
    }
    echo '</table>';
} elseif (isset($_REQUEST['stormajax']) && $_REQUEST['stormajax'] == 'load-template') {
        //ob_clean();
        $conf_id = $_REQUEST['conf_id'];

        $hid_template = array('0');
        $q = mysql_query("SELECT * FROM `StormBilling_customconfig` where `config_name` = 'wiz_pg_4_hide_from_tmplt_list'");
        if(($res = mysql_fetch_assoc($q))) {
            $hid_template = @explode(",", $res['config_value']);
        }

        require_once $roopath.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormTemplate.php';
        $template = new StormOnDemandStormTemplate($username, $password, 'bleed');
        $ret = $template->lists();

        if($error = $template->getError()) {
            echo '<p style="color: red">'.$error.'</p>';
            die();
        }

        echo '<table class="datatable" style="width: 100%">
                <tr>
                    <th>Template</th>
                </tr>
              ';
        foreach($ret['items'] as $item) {
            foreach ($hid_template as $tempid) {
                if ($tempid != $item['id']) {
                    if($item['deprecated'] == 1) {
                        continue;
                    }

                    echo '<tr>
                            <td><input class="storm-template" type="radio" name="template-id" id="template-id-'.$item['name'].'" value="'.$item['name'].'" '.($item['name'] == $conf_id ? 'checked="checked"' : '').'/> <label for="template-id-'.$item['name'].'">'.$item['description'].'</label></td>
                        </tr>';
                }
            }
        }
        echo '</table>';
}

//Run Auto Configuration
if (isset($_REQUEST['modaction']) && ($_REQUEST['modaction'] == 'generate_configurable_options' || $_REQUEST['modaction'] == 'generate_custom_fields'))
{
    if ($_REQUEST['modaction'] == 'generate_configurable_options')
    {
        $q = mysql_safequery('SELECT * FROM tblproductconfiglinks WHERE pid = ?', array($_REQUEST['id']));
        $row = mysql_fetch_assoc($q);
        if(mysql_num_rows($q))
        {
            //echo 'Configurable Options Already Generated<br/><br/>Click <b><a href="configproductoptions.php?action=managegroup&id='.$row['gid'].'">here</a></b> to check Configurable options';

            $json = array();
            $json['status']     =   0;
            $json['message']    =   'Configurable Options Already Generated<br/><br/>Click <b><a href="configproductoptions.php?action=managegroup&id='.$row['gid'].'">here</a></b> to check Configurable options';
            //echo 'Configurable Options Already Generated<br/><br/>Click <b><a href="configproductoptions.php?action=managegroup&id='.$row['gid'].'">here</a></b> to check Configurable options';
            echo json_encode($json);
            die();
        }
    }    

    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'class.LiquidWebPrivateParentProduct.php';

    //Product Configuration
    $product = new LiquidWebPrivateParentProduct($_REQUEST['id']);

    //Load Config
    $product->loadConfig();

    //Get Parents
    $selected_parents = $product->getConfig('AvailableParents');

    if(count($selected_parents) == 0){
        $selected_parents = array($product->getConfig('Parent'));
    }

    require_once $roopath.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormPrivateParent.php';
    $private = new StormOnDemandStormPrivateParent($username, $password, 'bleed');

    $page_num = 1;
    $page_size = 250;

    $response = $private->lists($page_num, $page_size);
    if (!$response) {
        ob_clean();
        json_encode(array(
            'status'    =>  0,
            'message'   =>  $private->getError()
        ));
        die();
    }

    $parents = $response['items'];
    while($response['item_total'] > $page_num * $page_size) {
        $page_num++;
        $response = $private->lists($page_size, $page_num);
        $parents = array_merge($parents, $response['items']);
    }

    foreach($parents as $parent) {
        if(strcmp($parent['uniq_id'], $selected_parents) != 0)
        {
            continue;
        }

        $product->defaultConfigurableOptions['mygroup']['fields']['Parent']['options'][] = array
        (
            'value' =>  $parent['uniq_id'],
            'title' =>  $parent['domain']
        );
    }
    unset($selected_parents);
    unset($parent);
    unset($parents);
    unset($response);

    //Get Templates
    require_once $roopath.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormTemplate.php';
    $template = new StormOnDemandStormTemplate($username, $password, 'bleed');

    $page_num = 1;
    $page_size = 250;

    $response = $template->lists($page_size, $page_num);
    if(!$response)
    {
        ob_clean();
        echo json_encode(array(
            'status'    =>  0,
            'message'   =>  $template->getError()
        ));
        die();
    }

    $templates = $response['items'];
    while($response['item_total'] > $page_num * $page_size)
    {
        $page_num++;
        $response = $template->lists($page_size, $page_num);
        $templates = array_merge($templates, $response['items']);
    }

    foreach($templates as $tpl)
    {
        if($tpl['deprecated'])
        {
            continue;
        }

        $product->defaultConfigurableOptions['mygroup']['fields']['Template']['options'][] = array
        (
            'value' =>  $tpl['name'],
            'title' =>  $tpl['description']
        );
    }
    unset($template);
    unset($templates);
    unset($response);

    //Image
    require_once $roopath.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormImage.php';
    $image = new StormOnDemandStormImage($username, $password, 'bleed');

    $page_num = 1;
    $page_size = 250;

    $response = $image->lists($page_size, $page_num);
    if(!$response)
    {
        ob_clean();
        echo json_encode(array(
            'status'    =>  0,
            'message'   =>  $image->getError()
        ));
        die();
    }

    $images = $response['items'];
    while($response['item_total'] > $page_num * $page_size)
    {
        $page_num++;
        $response = $image->lists($page_size, $page_num);
        $images = array_merge($images, $response['items']);
    }

    foreach($images as $image)
    {
        $product->defaultConfigurableOptions['mygroup']['fields']['Image']['options'][] = array
        (
            'value' =>  $image['id'],
            'title' =>  $image['name']. ' - '.$image['source_hostname']
        );
    }

    /*
     $max_ips = $product->getConfig('Maximum IP Addresses');
     if($max_ips != null && is_numeric($max_ips)){
     for($i=1;$i<=$max_ips;$i++){
     $product->defaultConfigurableOptions['mygroup']['fields']['Maximum IP Addresses']['options'][] = array
     (
     'value' =>  $i,
     'title' =>  $i
     );
     }
     }

     $IPs_Number = $product->getConfig('Number of IPs');

     if($IPs_Number != null && is_numeric($IPs_Number)){
     for($i=1;$i<=$IPs_Number;$i++){
     $product->defaultConfigurableOptions['mygroup']['fields']['Number of IPs']['options'][] = array
     (
     'value' =>  $i,
     'title' =>  $i
     );
     }
     }

     $DailyBackupQuota = $product->getConfig('Daily Backup Quota');

     if($DailyBackupQuota != null && is_numeric($DailyBackupQuota)){
     for($i=1;$i<=$DailyBackupQuota;$i++){
     $product->defaultConfigurableOptions['mygroup']['fields']['Daily Backup Quota']['options'][] = array
     (
     'value' =>  $i,
     'title' =>  $i
     );
     }
     }
     */

    unset($images);
    unset($image);
    unset($response);

    $product->runAutoConfiguration();
}
