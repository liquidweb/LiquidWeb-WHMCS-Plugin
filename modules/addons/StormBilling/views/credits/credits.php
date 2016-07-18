<script type="text/javascript">
    jQuery(function(){
        jQuery(".refundCredits").click(function(){
            return confirm("Credits will be refunded on user account. Are you sure you want to continue?");
        });
    });
</script>
<?php

/**********************************************************************
 *  StormBilling Trunk (2014-02-26)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 **********************************************************************/


/**
 * @author Mariusz Miodowski <mariusz@modulesgarden.com>
 */


echo '<div style="overflow: auto">';
echo '<table class="table pagiantion" id="StormBilling_Credits">
        <thead>
            <tr>
                <th>Client Name</th>
                <th>Hosting</th>
                <th>Internal Credit</th>
                <th>Already Paid For Hosting</th>
                <th style="width: 80px" >Action</th>
            </tr>
        </thead>
        <tbody>';
        if($clients)
        {
            foreach($clients as $client)
            {
                echo '<tr>
                        <td><a href="clientssummary.php?userid='.$client['user_id'].'">'.$client['firstname'].' '.$client['lastname'].'</a></td>
                        <td><a href="clientshosting.php?userid='.$client['user_id'].'&id='.$client['hosting_id'].'">'.$client['hosting_id'].' - '.($client['domain'] ? $client['domain'] : 'no domain').'</a></td>
                        <td>'.$client['credit'].'</td>
                        <td>'.$client['paid'].'</td> 
                        <td>
                            <a href="addonmodules.php?module=StormBilling&modpage=credits&modaction=refund&hosting_id='.$client['hosting_id'].'&client_id='.$client['user_id'].'" class="btn btn-danger refundCredits">Refund</a>
                        </td>
                      </tr>';
            }
        }
        else
        {
            echo '<tr><td colspan="5" style="text-align: center"><b>Nothing to display</b></tr>';
        }
echo '
        </tbody>
    </table>';

echo '</div>';
    echo '<div class="pagination pagination-right">'.$pagination->getPagination().'</div>';