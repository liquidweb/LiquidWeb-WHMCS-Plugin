<?php
define('CORE_DIR', dirname(__FILE__));
//GET ROOT DIR
$ROOT_DIR = dirname(__FILE__);
$ROOT_DIR = substr($ROOT_DIR, 0, strrpos($ROOT_DIR, DS));
$ROOT_DIR = substr($ROOT_DIR, strrpos($ROOT_DIR, DS), strlen($ROOT_DIR));

/*************** INCLUDES **********************/
//ModulesGarden
require_once CORE_DIR.DS.'class.ModulesGarden.php';
//SOME USEFUL STUFF
require_once CORE_DIR.DS.'functions.php';
//PAGINATION INTERFACE
require_once CORE_DIR.DS.'class.MG_Pagination.php';
//Languages
require_once CORE_DIR.DS.'class.MG_Language.php';
//USER FUNCTIONS
require_once StormBillingDIR.DS.'core.php';

//GET MODULE NAME
$module = ModulesGarden::getModuleClass(StormBillingDIR.DS.$ROOT_DIR);
//CREATE MODULE CONFIG
$MGC = new $module();
//DEFINE MODULE NAME
define('ADDON_NAME', $module);

//ENABLE DEBUG
if(isset($_GET['debug']) && $_GET['debug'] == 1 && $_SESSION['adminid'])
{
    $_SESSION['MODULESGARDEN_DEBUG'] = 1;
}

//DISABLE DEBUG
if(isset($_GET['debug']) && $_GET['debug'] == 0 && $_SESSION['adminid'])
{
    $_SESSION['MODULESGARDEN_DEBUG'] = 0;
}

if((isset($_SESSION['MODULESGARDEN_DEBUG']) && $_SESSION['MODULESGARDEN_DEBUG']) || $MGC->debug)
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

$customConfigOptions = getCustomConfigOptions();
define('LW_CUSTOM_TEMPLATE_SIX', $customConfigOptions['custom_template']);

//IS AJAX?
$AJAX = isset($_REQUEST['ajax']) ? 1 : 0;

//GET PAGE AND CHECK IT!
$PAGE = isset($_REQUEST['modpage']) ? $_REQUEST['modpage'] : $MGC->default_page;
$AVAILABLE_PAGES = $MGC->top_menu + $MGC->side_menu;
if(!array_key_exists($PAGE, $AVAILABLE_PAGES))
{
    die('This page does not exists!');
}
//FIND PAGE FILES
$PAGE_FILE = ($AJAX ? 'ajax.pages'.DS.$PAGE : 'pages'.DS.$PAGE).'.php';

//IS IT AJAX REQUEST? JUST INCLUDE CONTROLLER FILE AND VIEW
if($AJAX)
{
    //PAGINATION REQUEST?
    if($_REQUEST['pagination'] == 1)
    {
        $p = new MG_Pagination($_REQUEST['parent']);
        if(isset($_REQUEST['get']))
        {
            echo $p->getPagination();
            die();
        }
        elseif(isset($_REQUEST['order_by']))
        {
            $p->setOrderBy($_REQUEST['order_by'], 'ASC');
        }
        if(isset($_REQUEST['check']))
        {
            echo json_encode(array
            (
                'next'      =>  $p->isNext(),
                'prev'      =>  $p->isPrev(),
                'current'   =>  $p->getCurrentPage(),
            ));
            die();
        }
        elseif(isset($_REQUEST['reset']))
        {
            $p->resetFilter();
        }
        elseif(isset($_REQUEST['next']))
        {
            $p->next();
        }
        elseif(isset($_REQUEST['prev']))
        {
            $p->prev();
        }
        elseif(isset($_REQUEST['page']))
        {
            $p->setPage($_REQUEST['page']);
        }
        else
        {
            foreach($_REQUEST['filters'] as $field_name => $field_value)
            {
                if($field_value)
                {
                    $p->addFilter($field_name, $field_value);
                }
                else
                {
                    $p->removeFilter($field_name);
                }
                $p->setPage(0);
            }
        }
        //$p->resetFilter();
        $p->__destruct();
    }
    require_once StormBillingDIR.DS.$PAGE_FILE;
    if(file_exists(StormBillingDIR.DS.'ajax.views'.DS.$PAGE.'.php'))
    {
        include_once StormBillingDIR.DS.'ajax.views'.DS.$PAGE.'.php';
    }
    exit;
}

//NORMAL REQUEST?
//GET MENUS
$TOP_MENU = $MGC->top_menu;
$SIDE_MENU = $MGC->side_menu;

//ASSETS DIR
$ASSETS_DIR = '..'.DS.'modules'.DS.'addons'.DS.$ROOT_DIR.DS.'core'.DS.'assets';

//MODULE URL
$MODULE_URL = 'addonmodules.php?module='.$_GET['module'];

$PAGE_HEADING = null;
$PAGE_MODULE_HEADING = null;
$PAGE_SUBMODULE_HEADING = null;
//GET PAGE CONTENT
ob_start();

global $lerror;

if(!$lerror)
{
    //CONTROLLER
    if(isset($_REQUEST['modsubpage']) && file_exists(StormBillingDIR.DS.'pages'.DS.$PAGE.DS.$_REQUEST['modsubpage'].'.php'))
    {
        require StormBillingDIR.DS.'pages'.DS.$PAGE.DS.$_REQUEST['modsubpage'].'.php';
    }
    elseif(file_exists(StormBillingDIR.DS.'pages'.DS.$PAGE.DS.$PAGE.'.php'))
    {
        require StormBillingDIR.DS.'pages'.DS.$PAGE.DS.$PAGE.'.php';
    }
    else
    {
        die('Page does not exists!');
    }

    //LOCATE VIEW FILE AND LOAD IT
    if(isset($_REQUEST['modsubpage']) && file_exists(StormBillingDIR.DS.'views'.DS.$PAGE.DS.$_REQUEST['modsubpage'].'.php'))
    {
        require StormBillingDIR.DS.'views'.DS.$PAGE.DS.$_REQUEST['modsubpage'].'.php';
    }
    elseif(file_exists(StormBillingDIR.DS.'views'.DS.$PAGE.DS.$PAGE.'.php'))
    {
        require StormBillingDIR.DS.'views'.DS.$PAGE.DS.$PAGE.'.php';
    }
}
else
{
    addError($lerror);
}
$CONTENT = ob_get_clean();