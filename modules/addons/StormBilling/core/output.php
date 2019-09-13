<?php

require_once 'core.php';

#ATTACH STYLES
echo '

<link href="'.$ASSETS_DIR.'/css/bootstrap.css" rel="stylesheet">
<link href="'.$ASSETS_DIR.'/css/bootstrap-responsive.css" rel="stylesheet">

<link href="'.$ASSETS_DIR.'/css/template-styles.css" rel="stylesheet">

<link href="'.$ASSETS_DIR.'/css/modulesgarden.css" rel="stylesheet">

<!--FONTS-->
<link href="'.$ASSETS_DIR.'/css/font-awesome.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/jquery-ui.css">
<script src="../assets/js/jquery-ui.js"></script>

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!--[if IE 7]>
  <link rel="stylesheet" href="assets/css/font-awesome-ie7.css">
<![endif]-->';


$current_action="billing";
if (isset ($_GET['action']) || $_GET['action']!="") {
    $current_action = $_GET['action'];
}
echo '<div class="body" data-target=".body" data-spy="scroll" data-twttr-rendered="true" id="mg-wrapper">';

if ($current_action == "billing") {

    #SHOW SIDEBAR
    require_once CORE_DIR.DS.'views'.DS.'sidebar.php';

    #SHOW BODY
    require_once CORE_DIR.DS.'views'.DS.'body.php';

} elseif ($current_action == "config") {
    require_once CORE_DIR.DS.'views'.DS.'custom_config.php';
} else {
    require_once CORE_DIR.DS.'views'.DS.'prod_setup_wizard.php';
}
echo '</div>';
