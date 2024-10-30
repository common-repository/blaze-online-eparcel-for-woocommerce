jQuery(function($){
	function performAutosave(){
		var panel = $('div.custom-options-panel');
		var params = panel.find('input, select, textarea').serialize();
		params = params + '&action=save_settings-' + panel.attr('id');
		
		$.post(
			'admin-ajax.php',
			params,
			function(response) {
				//window.location.reload(true);
			}
		);
	}
	
	// $('#screen-options-wrap div.requires-autosave').find('input, select, textarea').change(performAutosave);
	// $('#screen-options-apply').click(function() {
		// if(jQuery('#eParcel-default-settings').length > 0) {
			// performAutosave();
		// }
		// return false;
	// });
});