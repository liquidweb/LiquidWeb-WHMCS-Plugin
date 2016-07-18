<?php
/*
echo
'
<div id="mg-sidebar" class="left">
    <div class="">';

if($SIDE_MENU)
{
    echo '<ul class="mg-sidebar-nav">';
    foreach($SIDE_MENU as $page => $menu)
    {
        if(isset($menu['submenu']))
        {
            echo '<li>';
            echo '<a href="#"><i class="icon-'.$menu['icon'].'"></i>'.$menu['title'].'</a>';
            echo '<ul class="nav-dropdown">';
            foreach($menu['submenu'] as $subpage => $submenu)
            {
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
        <a class="slogan nblue-box" href="http://www.liquidweb.com" target="_blank" alt="LiquidWeb Custom Development">
            <span>Do you want to extend your <br/> WHMCS functionality ?</span>
            <span class="lw-logo"></span>
            <small>We are here to help you, just click!</small>
        </a>
    </div>
</div><!-- end of SIDEBAR -->
';*/
?>
