jQuery(function(){

    var urlToScript = 'configproducts.php?action=edit&id={$id}';
    var backupQuota = {$backup_quota};
    var bandwidthQuota = '{$bandwidth_quota}';
    var productTemplates = {$templates};
    /* REF */
    //var $productConifg = jQuery('#tab2box').find('table.form')[1];
    //jQuery($productConifg).find('tbody').prepend('<tr><td class="fieldlabel">Product template</td><td class="fieldarea"><a id="product-template-load" href="#">Load default product templates</a><div id="conf-dialog-product-templates" title=""></div></td></tr>');
    /* END REF */


    function checkQuota(zoneValue){
        if (!zoneValue){
        //change Bandwidth Quota
        jQuery('select[name="{$inpt_bandwidth_quota_name}"]').val('').html('<option value="-1">Please, select Zone</option>').attr('disabled', 'disabled');
            //change Backup Quota
            jQuery('select[name="{$inpt_backup_quota_name}"]').val('').html('<option value="-1">Please, select Zone</option>').attr('disabled', 'disabled');
        } else{
        //change Bandwidth Quota
        jQuery('select[name="{$inpt_bandwidth_quota_name}"]').html('').removeAttr('disabled');
            jQuery('select[name="{$inpt_bandwidth_quota_name}"]').parent().append('<img src="../modules/servers/LiquidWeb/assets/images/admin/loading.gif" class="preloader" alt="loading..."/>');
            //change Backup Quota
            jQuery('select[name="{$inpt_backup_quota_name}"]').html('').removeAttr('disabled');
            jQuery('select[name="{$inpt_backup_quota_name}"]').parent().append('<img src="../modules/servers/LiquidWeb/assets/images/admin/loading.gif" class="preloader" alt="loading..."/>');
            //getConfig
            jQuery.post(urlToScript + '&stormajax=load-quota&zone=' + zoneValue, function(data){
            if (data && data !== ''){
                try{
                    var response = JSON.parse(data);
                    if (response['type'] === 'success'){

                        for (var i in response['data']['data']['backup_quota']){
                        var row = response['data']['data']['backup_quota'][i];
                            jQuery('select[name="{$inpt_backup_quota_name}"]').append('<option value="' + row['value'] + '">' + row['description'] + '</option>');
                        }

                        for (var i in response['data']['data']['bandwidth_quota']){
                        var row = response['data']['data']['bandwidth_quota'][i];
                           jQuery('select[name="{$inpt_bandwidth_quota_name}"]').append('<option value="' + row['value'] + '">' + row['description'] + '</option>');
                        } 
                    }
                    
                    //Bandwidth Quota
                    jQuery('select[name="{$inpt_bandwidth_quota_name}"]').val(bandwidthQuota).on('change', function(){
                        bandwidthQuota = jQuery(this).val();
                    });
                    jQuery('select[name="{$inpt_bandwidth_quota_name}"]').parent().find('.preloader').remove();
                    //Backup Quota
                    jQuery('select[name="{$inpt_backup_quota_name}"]').val(backupQuota).on('change', function(){
                        backupQuota = parseInt(jQuery(this).val());
                    });
                    jQuery('select[name="{$inpt_backup_quota_name}"]').parent().find('.preloader').remove();
                } catch (e){}
            }
            });
        }
    }

    function loadProductTemplates(){
        if (jQuery("#conf-dialog-product-templates").is(":data(dialog)")){
            jQuery("#conf-dialog-product-templates").dialog("destroy");
        }
        jQuery("#conf-dialog-product-templates").dialog({minWidth: 650});
            jQuery("#conf-dialog-product-templates").attr("title", 'Product templates list');
            jQuery("#conf-dialog-product-templates").html('<table class="datatable" style="width: 100%"><tr><th>Template Name</th><th>Action</th></tr></table>');
            $tableContainer = jQuery("#conf-dialog-product-templates").find('table.datatable');
            var noNameIter = 1;
            for (var i = 0; i < productTemplates['templates'].length; i++){
                var name = '';
                if (productTemplates['templates'][i]['name'] && typeof productTemplates['templates'][i]['name'] !== 'object'){
                    name = productTemplates['templates'][i]['name'];
                } else{
                    name = 'noname ' + noNameIter;
                    noNameIter++;
                }
                $tableContainer.append('<tr><td>' + name + '</td><td style="text-align:center"><a href="#" data-template-id="' + i + '" class="load-product-template">load</a></td></tr>');
            }
        $tableContainer.find('.load-product-template').each(function(){
            jQuery(this).on('click', function(){
                event.preventDefault();
                var idTemplate = parseInt(jQuery(this).attr('data-template-id'));
                if (productTemplates['templates'][idTemplate]){
                    for (var iOption in productTemplates['templates_info']){
                        var valToSet = '';
                        if (productTemplates['templates'][idTemplate][iOption] && typeof productTemplates['templates'][idTemplate][iOption] !== 'object'){
                            valToSet = productTemplates['templates'][idTemplate][iOption];
                        }
                        if (valToSet === '' && typeof productTemplates['templates_info'][iOption]['reset'] !== "undefined" && productTemplates['templates_info'][iOption]['reset'] === false){
                            continue;
                        }
                        switch (productTemplates['templates_info'][iOption]['type']){
                            case 'checkbox':
                            if (valToSet === 'yes'){
                                jQuery('input[name="' + productTemplates['templates_info'][iOption]['name'] + '"]').prop('checked', true);
                            } else{
                                jQuery('input[name="' + productTemplates['templates_info'][iOption]['name'] + '"]').prop('checked', false);
                            }
                                break;
                            case 'text' :
                            jQuery('input[name="' + productTemplates['templates_info'][iOption]['name'] + '"]').val(valToSet).change();
                                break;
                            case 'html' :
                            jQuery(productTemplates['templates_info'][iOption]['id']).html(valToSet);
                                break;
                            case 'select':
                            if (iOption === 'backup_quota'){
                                backupQuota = parseInt(valToSet);
                            } else if (iOption === 'bandwidth_quota'){
                                bandwidthQuota = valToSet;
                            } else{
                                jQuery('select[name="' + productTemplates['templates_info'][iOption]['name'] + '"]').val(valToSet).change();
                            }
                            break;
                        }
                    }
                }
                jQuery("#conf-dialog-product-templates").dialog("destroy");
            });
        });
    }

    jQuery('input[name="{$inpt_zone_name}"]').on('change', function(){
        checkQuota(jQuery(this).val());
    });

    jQuery('#product-template-load').on('click', function(){
        event.preventDefault();
        loadProductTemplates();
    });

    if (jQuery('input[name="{$inpt_zone_name}"]').val() == ''){
        checkQuota({$input_zone_default_id});
    }else{
        checkQuota(jQuery('input[name="{$inpt_zone_name}"]').val());
    }
});
