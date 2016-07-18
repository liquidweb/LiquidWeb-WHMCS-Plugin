jQuery(function(){
	
	var defaultdApprovedDomain = '{$metatag_approved_domain}';
	
	
	if(jQuery('select[name="fields[verification_method]"]').val() !== 'metatag'){
		jQuery('input[name="fields[metatag_approved_domain]"]').closest('tr').fadeOut(0);
                jQuery('input[name="fields[metatag_approved_domain]"]').closest('.form-group').fadeOut(0);
	}
	
	jQuery('select[name="fields[verification_method]"]').on('change', function(){
		
		var _val = jQuery(this).val();
		if(_val === 'metatag'){
			jQuery('input[name="fields[metatag_approved_domain]"]').val(defaultdApprovedDomain).closest('tr').fadeIn(0);
                        jQuery('input[name="fields[metatag_approved_domain]"]').val(defaultdApprovedDomain).closest('.form-group').fadeIn(0);
		}else{
			jQuery('input[name="fields[metatag_approved_domain]"]').closest('tr').fadeOut(0);
                        jQuery('input[name="fields[metatag_approved_domain]"]').closest('.form-group').fadeOut(0);
		}
	});
	
});