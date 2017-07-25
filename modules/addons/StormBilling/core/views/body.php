<?php
echo
'
    <div id="mg-content" class="right">

    	<div id="top-bar">

        	<div id="module-name">
            	<h2>'.$MGC->name.'</h2>
                <h4>'.$AVAILABLE_PAGES[$PAGE]['title'].'</h4>
            </div>';
if($TOP_MENU)
{
    echo '<ul id="top-nav">';
    foreach($TOP_MENU as $page => $menu)
    {
        if(isset($menu['show']) && !$menu['show'])
        {
            continue;
        }

        $count = 0;
        if(isset($menu['submenu']))
        {
            foreach($menu['submenu'] as &$submenu)
            {
                if(!isset($submenu['show']) || (isset($submenu['show']) && $submenu['show'] != false))
                {
                    $count++;
                }
            }
        }

        if(isset($menu['submenu']) && $count)
        {
            echo '<li class="dropdown-toggle">';
            echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" id="menu-'.$page.'"><i class="icon-'.$menu['icon'].'"></i>'.$menu['title'].'<i class="icon-caret-down"></i></a>';
            echo '<ul class="dropdown-menu" role="menu" aria-labelledby="menu-'.$page.'">';
            foreach($menu['submenu'] as $subpage => &$submenu)
            {
                if(isset($submenu) && !$submenu['show'])
                {
                    continue;
                }

                echo '<li><a href="'.$MODULE_URL.'&modpage='.$page.'&modsubpage='.$subpage.'">'.$submenu['title'].'</a></li>';
            }
            echo '</ul>';
            echo '</li>';
        }
        else
        {
            echo '<li><a href="'.$MODULE_URL.'&modpage='.$page.'"><i class="icon-'.$menu['icon'].'"></i>'.$menu['title'].'</a></li>';
        }
    }
    echo '</ul>';
}

echo '

            <!--<div class="clear"></div>-->
        <a class="slogan" href="http://www.liquidweb.com" target="_blank" alt="Liquid Web">
            <span class="lw-logo"></span>
        </a>
        </div><!-- end of TOP BAR -->

    	<div class="inner">
        <h2 class="section-heading">';

if(!$PAGE_SUBMODULE_HEADING && !isset($TOP_MENU[$PAGE]['submenu'][$_REQUEST['modsubpage']]) && ( !isset($TOP_MENU[$PAGE]['submenu'][$_REQUEST['hide']]) || $TOP_MENU[$PAGE]['submenu'][$_REQUEST['modsubpage']]['hide'] != false))
{
    echo '<i class="icon-'.$TOP_MENU[$PAGE]['icon'].'"></i>'.'<a href="addonmodules.php?module=StormBilling&modpage='.$PAGE.'">'.($PAGE_HEADING ? $PAGE_HEADING : $TOP_MENU[$PAGE]['title']).'</a>';
}
else
{
    echo '<i class="icon-'.($TOP_MENU[$PAGE]['submenu'][$_REQUEST['modsubpage']]['icon'] ? $TOP_MENU[$PAGE]['submenu'][$_REQUEST['modsubpage']]['icon'] : $TOP_MENU[$PAGE]['icon']).'"></i>'.( $PAGE_HEADING ? $PAGE_HEADING : '<a href="addonmodules.php?module=StormBilling&modpage='.$PAGE.'">'.$TOP_MENU[$PAGE]['title'].'</a>' ).' -> '.($PAGE_SUBMODULE_HEADING ? $PAGE_SUBMODULE_HEADING : $TOP_MENU[$PAGE]['submenu'][$_REQUEST['modsubpage']]['title']);
}

echo '</h2>';

$infos = getInfos();
if($infos)
{
    foreach($infos as $info)
    {
        echo '<div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                    '.$info.'
               </div>';
    }
}


$errors = getErrors();
if($errors)
{
    foreach($errors as $error)
    {
        echo '<div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                    '.$error.'
               </div>';
    }
}

echo $CONTENT;
?>
        </div><!-- end of INNER -->
        <div class="overlay hide">
        </div>
    </div><!-- end of CONTENT -->
