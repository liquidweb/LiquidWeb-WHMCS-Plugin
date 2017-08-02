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
    ob_clean();
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
                <td><input class="storm-image" type="radio" name="image-id" value="'.$item['id'].'" '.($item['name'] == $conf_id ? 'checked="checked"' : '').' />'.$item['template_description'].'</td>
                <td>'.$item['source_hostname'].'</td>
                <td>'.$item['time_taken'].'</td>
              </tr>';
    }
    echo '</table>';
    echo '<script type="text/javascript">
            $(function(){
                $(".storm-image").click(function(event){
                    event.preventDefault();

                    val = $(this).parent().find("input[name=\'image-id\']").val();
                    $("#load-storm-image").closest(".fieldarea").find("input").val(val);
                    $("#custom-dialog").dialog("destroy");
                    $("#load-storm-template").closest(".fieldarea").find("input").val("");
                });
            });
          </script>';
} elseif (isset($_REQUEST['stormajax']) && $_REQUEST['stormajax'] == 'load-template') {
        ob_clean();
        $conf_id = $_REQUEST['conf_id'];

        require_once $roopath.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'StormOnDemand'.DIRECTORY_SEPARATOR.'bleed'.DIRECTORY_SEPARATOR.'class.StormOnDemandStormTemplate.php';
        $template = new StormOnDemandStormTemplate($username, $password, 'bleed');
        $ret = $template->lists();

        if($error = $template->getError())
        {
            echo '<p style="color: red">'.$error.'</p>';
            die();
        }

        echo '<table class="datatable" style="width: 100%">
                <tr>
                    <th>Template</th>
                </tr>
              ';
        foreach($ret['items'] as $item)
        {
            if($item['deprecated'] == 1)
            {
                continue;
            }

            echo '<tr>
                    <td><input class="storm-template" type="radio" name="template-id" value="'.$item['name'].'" '.($item['name'] == $conf_id ? 'checked="checked"' : '').'/>'.$item['description'].'</td>
                  </tr>';
        }
        echo '</table>';
        echo '<script type="text/javascript">
                $(function(){
                    $(".storm-template").click(function(event){
                        event.preventDefault();

                        val = $(this).parent().find("input[name=\'template-id\']").val();
                        $("#load-storm-template").closest(".fieldarea").find("input").val(val).change();
                        $("#custom-dialog").dialog("destroy");
                        $("#load-storm-image").closest(".fieldarea").find("input").val("");
                    });
                });
              </script>';
}

//Run Auto Configuration
if (isset($_REQUEST['modaction']) && ($_REQUEST['modaction'] == 'generate_configurable_options' || $_REQUEST['modaction'] == 'generate_custom_fields'))
{

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
    if(!$response)
    {
        ob_clean();
        json_encode(array(
            'status'    =>  0,
            'message'   =>  $private->getError()
        ));
        die();
    }

    $parents = $response['items'];
    while($response['item_total'] > $page_num * $page_size)
    {
        $page_num++;
        $response = $private->lists($page_size, $page_num);
        $parents = array_merge($parents, $response['items']);
    }

    foreach($parents as $parent)
    {
        if(!in_array($parent['uniq_id'], $selected_parents))
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
