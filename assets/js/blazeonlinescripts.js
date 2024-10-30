$jEparcel = jQuery.noConflict();

jQuery(function($){
	/* Save Preset */
	$jEparcel('#save_preset').click(function(e) {
        var dimensions = [
            $jEparcel("input[name='linksynceparcel[height]']").val(),
            $jEparcel("input[name='linksynceparcel[width]']").val(),
            $jEparcel("input[name='linksynceparcel[length]']").val()
        ];
        if(validateDimensions(dimensions) < 2)
        {
            alert('At least 2 dimensions must be 5 cm.');
            return false;
        }

        $jEparcel('#mainform').submit();
    });

	/* Configurations */
	if($jEparcel('.linksync-configuration').length > 0) {
		jQuery('.tooltip').linksynctooltip({
			theme: 'linksynctooltip-shadow',
			contentAsHTML: true
		});

		jQuery('#use_order_weight').change(function(){
			var val = jQuery(this).val();
			if(val == 1)
			{
				jQuery('.use_order_weight_1').show();
				jQuery('.use_order_weight_0').hide();
			}
			else
			{
				jQuery('.use_order_weight_0').show();
				jQuery('.use_order_weight_1').hide();
			}
		});

		jQuery('#use_dimension').change(function(){
			var val = jQuery(this).val();
			if(val == 1)
			{
				jQuery('.use_dimension').show();
			}
			else
			{
				jQuery('.use_dimension').hide();
			}
		});

		jQuery('#display_choosen_status').change(function(){
			var val = jQuery(this).val();
			if(val == 1)
			{
				jQuery('.display_choosen_status').show();
			}
			else
			{
				jQuery('.display_choosen_status').hide();
			}
		});

		jQuery('#insurance').change(function() {
			var insurance = jQuery(this).val();
			if(insurance == 0) {
				jQuery('.order_value_insurance').removeClass('show-tr');
				jQuery('.order_value_insurance').addClass('hide-tr');
			} else {
				jQuery('.order_value_insurance').removeClass('hide-tr');
				jQuery('.order_value_insurance').addClass('show-tr');
			}
		});

		jQuery('#order_value_insurance').change(function() {
			var $this_val = jQuery('#order_value_insurance:checked').length > 0;
			if(!$this_val) {
				jQuery('.default_insurance_value').removeClass('hide-tr');
				jQuery('.default_insurance_value').addClass('show-tr');
				jQuery('#default_insurance_value').val('');
			} else {
				jQuery('.default_insurance_value').removeClass('show-tr');
				jQuery('.default_insurance_value').addClass('hide-tr');
			}
		});
		
		jQuery('#declared_value').change(function() {
			var $this_val = jQuery('#declared_value:checked').length > 0;
			if(!$this_val) {
				jQuery('#declared_value_text').removeClass('hide-tr');
				jQuery('#declared_value_text').addClass('show-tr');
				jQuery('.declared_value_text').val('');
			} else {
				jQuery('#declared_value_text').removeClass('show-tr');
				jQuery('#declared_value_text').addClass('hide-tr');
			}
		});
		
		jQuery('#has_commercial_value').change(function() {
			var $this_val = jQuery('#has_commercial_value:checked').length > 0;
			if($this_val) {
				jQuery('#product_classification').attr('disabled', true);
				jQuery('#product_classification').val('991');
				jQuery('.product_classification_text').val("Merchandise");
				jQuery('#product_classification_text').removeClass("hide-tr");
				jQuery('#product_classification_text').addClass("show-tr");
			} else {
				jQuery('#product_classification').attr('disabled', false);
			}
		});
		
		jQuery('#product_classification').change(function() {
			var $this_val = jQuery('#product_classification').val();
			if($this_val == '991') {
				jQuery('#product_classification_text').removeClass("hide-tr");
				jQuery('#product_classification_text').addClass("show-tr");
				jQuery('.product_classification_text').val("Merchandise");
			} else {
				jQuery('#product_classification_text').removeClass("show-tr");
				jQuery('#product_classification_text').addClass("hide-tr");
				jQuery('.product_classification_text').val("");
			}
		});
		
		jQuery('#user_order_details').change(function() {
			var $this_val = jQuery(this).val();
			if($this_val == '0') {
				jQuery('#default_good_description').removeClass("hide-tr");
				jQuery('#default_good_description').addClass("show-tr");
			} else {
				jQuery('#default_good_description').removeClass("show-tr");
				jQuery('#default_good_description').addClass("hide-tr");
			}
		});
		
		if(jQuery('.accordion').length > 0) {
			var allPanels = jQuery('.accordion > dd').hide();

			jQuery('.accordion > dt > a').click(function() {
				allPanels.slideUp();
				if(jQuery(this).hasClass('active')) {
					jQuery(this).removeClass('active');
					jQuery(this).parent().next().slideUp();
				} else {
					jQuery(this).addClass('active');
					jQuery(this).parent().next().slideDown();
				}
				return false;
			});
		}
	}

	/* Article */
	if($jEparcel('#article-form').length > 0) {
		if($jEparcel('#articles_type').val() == 'Custom') {
			$jEparcel('.create-consignment1').hide(); 
			$jEparcel('.backToPreset').hide(); 
			$jEparcel('#custom_articles').show(); 
			$jEparcel('.cancel-button').hide(); 
				
			var number_of_articles = $jEparcel('#number_of_articles').val();
			for(var i=1; i<=number_of_articles; i++)
			{
				var box = $jEparcel('.custom_articles_template').clone(); 
				box.removeClass('custom_articles_template');
				box.find('h3').html(box.find('h3').html()+' '+i);
				box.find('#article_description').val(box.find('#article_description').val()+' '+i);
				box.find('#article_weight').attr('name','article'+i+'[weight]');
				box.show();
				$jEparcel('#custom_articles_container').append(box);
			}
		}
	}

	/* Create Mass consignment */
	if($jEparcel('.mass-create-consignment').length > 0) {
		$jEparcel('#createMassButton').click(function(e) {
			submitMassConsignmentForm2();
			e.preventDefault();
		});
	}

	/* Manifest */
	if($jEparcel('#manifest-list').length > 0) {
		jQuery('input[name="_wp_http_referer"]').val('');
	}	
	if(jQuery('.datepicker').length > 0) {
		jQuery('.datepicker').datepicker({
			dateFormat: "yy-mm-dd"
		});
	}

	/* Single order page */

	// Default weight view 
	if($jEparcel('.single-create-consignment').length > 0) {
		if($jEparcel('#eparcel_sales_order_view').hasClass('order_weight_articles')) {
			$jEparcel('#number_of_articles').blur(function(){
				var totalArticles = $jEparcel('#totalArticles').val();
				var reminderWeight = $jEparcel('#reminderWeight').val();
				var value = $jEparcel.trim($jEparcel(this).val());

				if(value.length == 0)
				{
					alert('Articles should not be empty');
					$jEparcel(this).val(1);
				}
				if(isNaN(value))
				{
					alert('Articles should be a number');
					$jEparcel(this).val(1);
				}

				value = parseInt(value);
				if(value < 0)
				{
					alert('Articles should be a postive number');
					$jEparcel(this).val(1);
				}

				if(value > 100)
				{
					alert('Articles can be 1-100 per request');
					$jEparcel(this).val(1);
				}

				if($jEparcel('#articles_type').val() == 'Custom')
				{
					var number_of_articles = $jEparcel('#number_of_articles').val();
					var boxes = $jEparcel('#custom_articles_container > div.box_ls').length;
					if(boxes > number_of_articles)
					{
						for(;boxes>number_of_articles; boxes--)
						{
							$jEparcel('#custom_articles_container > div.box_ls:nth-child('+boxes+')').remove();
						}
					}
					else
					{
						var i=1 ;
						i = i + boxes;
						for(;i<=number_of_articles; i++)
						{
							var box_ls = $jEparcel('.custom_articles_template').clone();
							box_ls.removeClass('custom_articles_template');
							box_ls.find('h3').html(box_ls.find('h3').html()+' '+i);
							box_ls.find('#article_description').attr('name','article'+i+'[description]');
							box_ls.find('#article_description').val(box_ls.find('#article_description').val()+' '+i);
							box_ls.find('#article_weight').attr('name','article'+i+'[weight]');
							box_ls.find('#article_height').attr('name','article'+i+'[height]');
							box_ls.find('#article_width').attr('name','article'+i+'[width]');
							box_ls.find('#article_length').attr('name','article'+i+'[length]');
							box_ls.find('#article_weight').addClass('article_weight');
							if(reminderWeight > 0 && i == totalArticles)
							{
								 box_ls.find('#article_weight').val(reminderWeight);
							}
							else if(i > totalArticles)
							{
								 box_ls.find('#article_weight').val(0);
							}
							box_ls.show();
							$jEparcel('#custom_articles_container').append(box_ls);
						}
					}
				}
			});

			if($jEparcel('#articles_type').val() == 'Custom')
			{
				var reminderWeight = $jEparcel('#reminderWeight').val();
				$jEparcel('.create-consignment1').hide();
				$jEparcel('.backToPreset').hide();
				$jEparcel('#custom_articles').show();

				var number_of_articles = $jEparcel('#number_of_articles').val();
				for(var i=1; i<=number_of_articles; i++)
				{
					var box_ls = $jEparcel('.custom_articles_template').clone();
					box_ls.removeClass('custom_articles_template');
					box_ls.find('h3').html(box_ls.find('h3').html()+' '+i);
					box_ls.find('#article_description').attr('name','article'+i+'[description]');
					box_ls.find('#article_description').val(box_ls.find('#article_description').val()+' '+i);
					box_ls.find('#article_weight').attr('name','article'+i+'[weight]');
					box_ls.find('#article_height').attr('name','article'+i+'[height]');
					box_ls.find('#article_width').attr('name','article'+i+'[width]');
					box_ls.find('#article_length').attr('name','article'+i+'[length]');
					box_ls.find('#article_weight').addClass('article_weight');
					if(reminderWeight > 0 && i > 1 && i == number_of_articles)
					{
						 box_ls.find('#article_weight').val(reminderWeight);
					}
					box_ls.show();
					$jEparcel('#custom_articles_container').append(box_ls);
				}
			}

			$jEparcel('#articles_type').change(function(){
				var totalArticles = $jEparcel('#totalArticles').val();
				var reminderWeight = $jEparcel('#reminderWeight').val();
				if($jEparcel(this).val() == 'Custom')
				{
					//$jEparcel('#number_of_articles').removeAttr('readonly');
					$jEparcel('.backToPreset').hide();
					//$jEparcel('#presets').hide();
					$jEparcel('.create-consignment1').hide();
					$jEparcel('#custom_articles').show();

					var number_of_articles = $jEparcel('#number_of_articles').val();
					for(var i=1; i<=number_of_articles; i++)
					{
						var box_ls = $jEparcel('.custom_articles_template').clone();
						box_ls.removeClass('custom_articles_template');
						box_ls.find('h3').html(box_ls.find('h3').html()+' '+i);
						box_ls.find('#article_description').attr('name','article'+i+'[description]');
						box_ls.find('#article_description').val(box_ls.find('#article_description').val()+' '+i);
						box_ls.find('#article_weight').attr('name','article'+i+'[weight]');
						box_ls.find('#article_height').attr('name','article'+i+'[height]');
						box_ls.find('#article_width').attr('name','article'+i+'[width]');
						box_ls.find('#article_length').attr('name','article'+i+'[length]');
						box_ls.find('#article_weight').addClass('article_weight');
						if(reminderWeight > 0 && i == totalArticles)
						{
							 box_ls.find('#article_weight').val(reminderWeight);
						}
						else if(i > totalArticles)
						{
							 box_ls.find('#article_weight').val(0);
						}
						box_ls.show();
						$jEparcel('#custom_articles_container').append(box_ls);
					}
				}
				else
				{
					//$jEparcel('#number_of_articles').attr('readonly','readonly');
					$jEparcel('#number_of_articles').val(totalArticles);
					$jEparcel('#presets').show();
					$jEparcel('#custom_articles').hide();
					$jEparcel('#custom_articles_container').html('');
					$jEparcel('.create-consignment1').show();
				}
			});
		} else {
			$jEparcel('#number_of_articles').blur(function(){
				var value = $jEparcel.trim($jEparcel(this).val());
				if(value.length == 0)
				{
					alert('Articles should not be empty');
					$jEparcel(this).val(1);
				}

				if(isNaN(value))
				{
					alert('Articles should be a number');
					$jEparcel(this).val(1);
				}

				value = parseInt(value);
				if(value < 0)
				{
					alert('Articles should be a postive number');
					$jEparcel(this).val(1);
				}

				if(value > 100)
				{
					alert('Articles can be 1-100 per request');
					$jEparcel(this).val(1);
				}

				var number_of_articles = $jEparcel('#number_of_articles').val();
				var boxes = $jEparcel('#custom_articles_container > div.box_ls').length;
				if(boxes > number_of_articles)
				{
					for(;boxes>number_of_articles; boxes--)
					{
						$jEparcel('#custom_articles_container > div.box_ls:nth-child('+boxes+')').remove();
					}
				}
				else
				{
					var i=1 ;
					i = i + boxes;
					for(;i<=number_of_articles; i++)
					{
						var box_ls = $jEparcel('.custom_articles_template').clone();
						box_ls.removeClass('custom_articles_template');
						box_ls.find('h3').html(box_ls.find('h3').html()+' '+i);
						box_ls.find('#article_description').attr('name','article'+i+'[description]');
						box_ls.find('#article_description').val(box_ls.find('#article_description').val()+' '+i);
						box_ls.find('#article_weight').attr('name','article'+i+'[weight]');
						box_ls.show();
						$jEparcel('#custom_articles_container').append(box_ls);
					}
				}
			});

			if($jEparcel('#articles_type').val() == 'Custom')
			{
				$jEparcel('.create-consignment1').hide();
				$jEparcel('.backToPreset').hide();
				$jEparcel('#custom_articles').show();

				var number_of_articles = $jEparcel('#number_of_articles').val();
				for(var i=1; i<=number_of_articles; i++)
				{
					var box_ls = $jEparcel('.custom_articles_template').clone();
					box_ls.removeClass('custom_articles_template');
					box_ls.find('h3').html(box_ls.find('h3').html()+' '+i);
					box_ls.find('#article_description').attr('name','article'+i+'[description]');
					box_ls.find('#article_description').val(box_ls.find('#article_description').val()+' '+i);
					box_ls.find('#article_weight').attr('name','article'+i+'[weight]');
					box_ls.show();
					$jEparcel('#custom_articles_container').append(box_ls);
				}
			}

			$jEparcel('#articles_type').change(function() {
				$jEparcel('.backToPreset').hide(); 
				if($jEparcel(this).val() == 'Custom')
				{
					$jEparcel('.create-consignment1').hide();
					$jEparcel('#presets').show(); 
					$jEparcel('#custom_articles').show(); 
					$jEparcel('.cancel-button').hide(); 
					
					var number_of_articles = $jEparcel('#number_of_articles').val();
					for(var i=1; i<=number_of_articles; i++)
					{
						var box = $jEparcel('.custom_articles_template').clone(); 
						box.removeClass('custom_articles_template');
						box.find('h3').html(box.find('h3').html()+' '+i);
						box.find('#article_description').val(box.find('#article_description').val()+' '+i);
						box.show();
						$jEparcel('#custom_articles_container').append(box);
					}
				}
				else
				{
					$jEparcel('#presets').show();
					$jEparcel('#custom_articles').hide(); 
					$jEparcel('#custom_articles_container').html(''); 
					$jEparcel('.create-consignment1').show();
					$jEparcel('.cancel-button').show(); 
				}
			});
		}
		if(jQuery("#dialog").hasClass('validate_limit')) {
			jQuery("#dialog").dialog({
		        autoOpen: true,
		        width:'400px',
		        draggable: false,
		        closeOnEscape: false,
		        position: { my: "center", at: "center", of: "#linksynceparcel" }
		    });
		}

		jQuery('#insurance').change(function() {
			var insurance = jQuery(this).val();
			if(insurance == 0) {
				jQuery('.order_value_insurance').removeClass('show-tr');
				jQuery('.order_value_insurance').addClass('hide-tr');
			} else {
				jQuery('.order_value_insurance').removeClass('hide-tr');
				jQuery('.order_value_insurance').addClass('show-tr');
			}
		});
	} else {
		$jEparcel('#articles_type').change(function(){
			$jEparcel('.backToPreset').hide(); 
			if($jEparcel(this).val() == 'Custom')
			{
				$jEparcel('.create-consignment1').hide();
				$jEparcel('#presets').show(); 
				$jEparcel('#custom_articles').show(); 
				$jEparcel('.cancel-button').hide(); 
				
				var number_of_articles = $jEparcel('#number_of_articles').val();
				for(var i=1; i<=number_of_articles; i++)
				{
					var box = $jEparcel('.custom_articles_template').clone(); 
					box.removeClass('custom_articles_template');
					box.find('h3').html(box.find('h3').html()+' '+i);
					box.find('#article_description').val(box.find('#article_description').val()+' '+i);
					box.show();
					$jEparcel('#custom_articles_container').append(box);
				}
			}
			else
			{
				$jEparcel('#presets').show();
				$jEparcel('#custom_articles').hide(); 
				$jEparcel('#custom_articles_container').html(''); 
				$jEparcel('.create-consignment1').show();
				$jEparcel('.cancel-button').show(); 
			}
		});
	}

	$jEparcel("#consignment_submit2").click(function(e) {
		createConsignmentSubmitForm2();
		e.preventDefault();
	});

	$jEparcel("#consignment_submit").click(function(e) {
		createConsignmentSubmitForm();
		e.preventDefault();
	});

	$jEparcel("#consignment_weight_article_submit2").click(function(e) {
		createConsignmentSubmitForm2();
		e.preventDefault();
	});

	$jEparcel("#consignment_weight_article_submit").click(function(e) {
		createConsignmentWeightArticleSubmitForm();
		e.preventDefault();
	});

	/* Consignment List */
	if($jEparcel('#consignment-list').length > 0) {
		jQuery('input[name="_wp_http_referer"]').val('');
	}

	/* Order consignment list */
	if($jEparcel('#consignment-orderlist').length > 0) {
		jQuery('#the-list').delegate('.handler', 'click', function() {
			var key = jQuery(this).data('key');
			if(jQuery(this).hasClass('inactive') || !jQuery(this).hasClass('outactive')) {
				jQuery(this).removeClass('inactive');
				jQuery(this).addClass('outactive');
				jQuery('#key-'+ key).slideUp();
			} else {
				jQuery(this).addClass('inactive');
				jQuery(this).removeClass('outactive');
				jQuery('#key-'+ key).slideDown();
			}
		});

		jQuery('input#_wpnonce').next().remove();
		jQuery("#dialog").dialog({
			autoOpen: false,
			width:'400px'
		});
		
		jQuery("#dialog2").dialog({
			autoOpen: false,
			width:'400px'
		});
		jQuery("#dialog_submit").click(function(e) {
			if(!jQuery("#dialog_checkbox").prop('checked'))
			{
				alert('Please acknowledge to submit test manifest');
				e.preventDefault();
			}
			else
			{
				jQuery('#loading').show();
				var mode = $jEparcel('#consignment-orderlist').attr('data-mode');		
				if(mode == 1)
				{
					jQuery("#dialog2").dialog("close");
				}
				else
				{
					jQuery("#dialog").dialog("close");
				}
				
				jQuery.post(linksync_object.ajaxurl, {'action':'despatched_manifest'}, function(res) {
					if(res == 2) {
						window.location.reload(true);
					}
				});
			}
		});
		
		jQuery('#dialog_submit2').click(function(e) {
			jQuery('#loading').show();
			var mode = $jEparcel('#consignment-orderlist').attr('data-mode');		
			if(mode == 1)
			{
				jQuery("#dialog2").dialog("close");
			}
			else
			{
				jQuery("#dialog").dialog("close");
			}
			
			jQuery.post(linksync_object.ajaxurl, {'action':'despatched_manifest'}, function(res) {
				if(res == 2) {
					window.location.reload(true);
				}
			});
			e.preventDefault();
		});

		jQuery('#doaction').click(function(e) {
			if(jQuery('#bulk-action-selector-top').val() == 'massCreateConsignment') {
				var c = checkAllOrders(false);
				if(c.length > 0) {
					var conf = confirm('Consignments already exist for order '+ c.join(', ') +'. Do you want to continue with creating consignments?');
					if(conf != true) {
						window.location.href = linksync_object.linksynceparcelurl;
						return false;
					}
				}
			}
		});
	}

	/* Load to all */
	$jEparcel('.edit-consignments-defaults').click(function(){
		$jEparcel('.consignment-fields').slideToggle();
	});

	$jEparcel('#use_default_country_hstariff').change(function() {
		var $this_val = $jEparcel('#use_default_country_hstariff:checked').length > 0;
		if($this_val) {
			var country_origin_value = $jEparcel('#country_origin').data('default');
			var hs_tariff_value = $jEparcel('#hs_tariff').data('default');

			$jEparcel('#country_origin').attr('disabled', true);
			$jEparcel('#country_origin').val(country_origin_value);
			$jEparcel('#hs_tariff').attr('disabled', true);
			$jEparcel('#hs_tariff').val(hs_tariff_value);
		} else {
			$jEparcel('#country_origin').attr('disabled', false);
			$jEparcel('#hs_tariff').attr('disabled', false);
		}
	});

	$jEparcel('#product_classification').change(function() {
		var $this_val = $jEparcel('#product_classification').val();
		if($this_val == '991') {
			$jEparcel('#product_classification_text').removeClass("hide-tr");
			$jEparcel('#product_classification_text').addClass("show-tr");
		} else {
			$jEparcel('#product_classification_text').removeClass("show-tr");
			$jEparcel('#product_classification_text').addClass("hide-tr");
			$jEparcel('.product_classification_text').val("");
		}
	});

	$jEparcel('#has_commercial_value').change(function() {
		var $this_val = $jEparcel('#has_commercial_value:checked').length > 0;
		if($this_val) {
			$jEparcel('#product_classification').attr('disabled', true);
			$jEparcel('#product_classification').val('991');
			$jEparcel('#product_classification_text').removeClass("hide-tr");
			$jEparcel('#product_classification_text').addClass("show-tr");
		} else {
			$jEparcel('#product_classification').attr('disabled', false);
		}
	});

	jQuery('#order_value_declared_value').change(function() {
		var $this_val = jQuery(this).val();
		if($this_val == 1) {
			jQuery('#maximum_declared_value').removeClass('hide-tr');
			jQuery('#maximum_declared_value').addClass('show-tr');
			jQuery('#fixed_declared_value').removeClass('show-tr');
			jQuery('#fixed_declared_value').addClass('hide-tr');
		} else if($this_val == 2) {
			jQuery('#fixed_declared_value').removeClass('hide-tr');
			jQuery('#fixed_declared_value').addClass('show-tr');
			jQuery('#maximum_declared_value').removeClass('show-tr');
			jQuery('#maximum_declared_value').addClass('hide-tr');
		} else {
			jQuery('#maximum_declared_value').removeClass('show-tr');
			jQuery('#maximum_declared_value').addClass('hide-tr');
			jQuery('#fixed_declared_value').removeClass('show-tr');
			jQuery('#fixed_declared_value').addClass('hide-tr');
		}
	});

	jQuery('#insurance').change(function() {
		var insurance = jQuery(this).val();
		if(insurance == 0) {
			jQuery('.order_value_insurance').removeClass('show-tr');
			jQuery('.order_value_insurance').addClass('hide-tr');
			jQuery('.default_insurance_value').removeClass('show-tr');
			jQuery('.default_insurance_value').addClass('hide-tr');
		} else {
			jQuery('.order_value_insurance').removeClass('hide-tr');
			jQuery('.order_value_insurance').addClass('show-tr');
			jQuery('.default_insurance_value').removeClass('hide-tr');
			jQuery('.default_insurance_value').addClass('show-tr');
		}
	});

	jQuery('#order_value_insurance').change(function() {
		var $this_val = jQuery('#order_value_insurance:checked').length > 0;
		if(!$this_val) {
			jQuery('.default_insurance_value').removeClass('hide-tr');
			jQuery('.default_insurance_value').addClass('show-tr');
			jQuery('#default_insurance_value').val('');
		} else {
			jQuery('.default_insurance_value').removeClass('show-tr');
			jQuery('.default_insurance_value').addClass('hide-tr');
		}
	});

	$jEparcel('#delivery_signature_allowed').on('change', function() {
		if($jEparcel(this).val() == 1) {
			$jEparcel('.safe-drop-row').removeClass('hide-tr');
			$jEparcel('.safe-drop-row').addClass('show-tr');
		} else {
			$jEparcel('.safe-drop-row').removeClass('show-tr');
			$jEparcel('.safe-drop-row').addClass('hide-tr');
			$jEparcel('#consignments_safe_drop').val(0);
		}
	});

	$jEparcel("#declared_value").on('change', function() {
		var $this_val = $jEparcel('#declared_value:checked').length > 0;
		if($this_val) {
			$jEparcel('.declared_value_text_field').removeClass('show-tr');
			$jEparcel('.declared_value_text_field').addClass('hide-tr');
		} else {
			$jEparcel('.declared_value_text_field').removeClass('hide-tr');
			$jEparcel('.declared_value_text_field').addClass('show-tr');
		}
	});

	/* Menu */
	var pageMenuTitle = $jEparcel('#menu-linksync-holder').attr('data-title');
	var currentMenuPage = $jEparcel('#menu-linksync-holder').attr('data-currentpage');
	if(pageMenuTitle.length > 0) {
		$jEparcel('title').html(pageMenuTitle);
	}

	$jEparcel('#toplevel_page_linksynceparcel .wp-submenu a.wp-first-item').html('Consignments');
	$jEparcel('#toplevel_page_linksynceparcel .wp-submenu li').removeClass('current');
	$jEparcel('#toplevel_page_linksynceparcel .wp-submenu li a').each(function(){
		if($jEparcel(this).attr('href') == currentMenuPage) {
			$jEparcel(this).parent().addClass('current');
		}
	});

	if($jEparcel('div#linksynceparcel_address .inside').length > 0) {
		var inside = $jEparcel('div#linksynceparcel_address .inside').html();
		inside = $jEparcel.trim(inside);
		if(inside.length==0)
		{
			$jEparcel('div#linksynceparcel_address').remove();
		}
	}

	$jEparcel('.print_label').click(function(){
		var consignmentNumber = $jEparcel(this).attr('lang');
		var data = {
			'action':'update_label_as_printed',
			'consignmentNumber':consignmentNumber
		};
		var ajaxCaller = linksync_object.ajaxurl;
		$jEparcel.post(ajaxCaller, data, function() {
			location.href = location.href;
		});
	});
});

/* Save Preset */
function validateDimensions(dimensions)
{
    var shouldBe2 = 0;

    dimensions.forEach(function(number){
      shouldBe2 += (number >= 5) ? 1 : 0;
    });

    return shouldBe2;
}

/* Articles */
function backToPreset()
{
	$jEparcel('#presets').show();
	$jEparcel('#custom_articles').hide(); 
	$jEparcel('#custom_articles_container').html(''); 
	$jEparcel('#articles_type').val($jEparcel('#articles_type > option:first').attr('value'));
}

function setLocationConfirmDialog(url)
{
	if(!confirm('Are you sure?'))
		return false;
	setLocation(url);
}

function setLocation(url)
{
	window.location.href = url;
}

function submitForm()
{
	var valid = true;
	
	var value = $jEparcel.trim($jEparcel('#articles_type').val());
	if(value.length == 0 && valid)
	{
		valid = false;
		alert('Please select article type');
		return false;
	}
	
	$jEparcel('#custom_articles_container .required-entry').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		if(value.length == 0 && valid)
		{
			valid = false;
		}
	});
	if(!valid)
	{
		alert('Please enter/select all the mandatory fields');
		return false;
	}
	
	$jEparcel('.positive-number').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		var label = $jEparcel(this).attr('label');
		if(isNaN(value))
		{
			alert(label +' should be a number');
			valid = false;
		}
		
		value = parseInt(value);
		if(value < 0)
		{
			alert(label +' should be a postive number');
			valid = false;
		}
		
	});
	
	if(!valid)
	{
		return false;
	}
	var weightPerArticle = 22;
	if($jEparcel('#article_weight').hasClass('maximum-value')) {
		$jEparcel('.maximum-value').each(function(){
			var value = $jEparcel.trim($jEparcel(this).val());
			var label = $jEparcel(this).attr('label');
			value = parseFloat(value);
			if(value > weightPerArticle)
			{
				alert('Allowed weight per article is '+ weightPerArticle);
				valid = false;
			}
			
		});
		if(!valid)
		{
			return false;
		}
		else
		{
			$jEparcel('#edit_form').submit();
		}
	} else {
		$jEparcel('#edit_form').submit();
	}
}

function submitForm2()
{
	$jEparcel('#edit_form').submit();
}

/* Create Mass Consignment */
function submitMassConsignmentForm2()
{
	var valid = true;
	$jEparcel('.positive-number').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		var label = $jEparcel(this).attr('label');
		if(isNaN(value))
		{
			alert(label +' should be a number');
			valid = false;
		}

		value = parseInt(value);
		if(value < 0)
		{
			alert(label +' should be a postive number');
			valid = false;
		}

	});

	if(valid)
	{
		create_mass_consignment_ajax();
		return false;
	}
	else
	{
		return false;
	}
}
function create_mass_consignment_ajax() {
	jQuery('#loading').show();
	var data = $jEparcel('#edit_form').serialize() + '&action=create_mass_consignment_ajax';
	$jEparcel.post(linksync_object.ajaxurl, data, function(res) {
		if(res == 1)
		{
			window.location.href = linksync_object.linksynceparcelurl;
		}
	});
}

/* Create single consignment */
function createConsignmentSubmitForm()
{
	$jEparcel('#createConsignmentHidden').val(1);

	var valid = true;
	var value = $jEparcel.trim($jEparcel('#articles_type').val());

	if(value.length == 0 && valid)
	{
		valid = false;
		alert('Please select article type');
		return false;
	}

	$jEparcel('#custom_articles_container .required-entry').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		if(value.length == 0 && valid)
		{
			valid = false;
		}
	});

	if(!valid)
	{
		alert('Please enter/select all the mandatory fields');
		return false;
	}

	$jEparcel('.positive-number').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		var label = $jEparcel(this).attr('label');
		if(isNaN(value))
		{
			alert(label +' should be a number');
			valid = false;
		}

		value = parseInt(value);
		if(value < 0)
		{
			alert(label +' should be a postive number');
			valid = false;
		}
	});

	if(!valid)
	{
		return false;
	}
	else
	{
		create_consignment_ajax();
		return false;
	}
}

function createConsignmentSubmitForm2()
{
	$jEparcel('#createConsignmentHidden').val(1);
	var valid = true;
	var value = $jEparcel.trim($jEparcel('#articles_type').val());
	if(value.length == 0 && valid)
	{
		valid = false;
		alert('Please select article type');
		return false;
	}

	$jEparcel('.required-entry2').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		if(value.length == 0 && valid)
		{
			valid = false;
		}
	});
	if(!valid)
	{
		alert('Please enter/select all the mandatory fields');
		return false;
	}

	if(valid)
	{
		create_consignment_ajax();
		return false;
	}
	else
	{
		return false;
	}
}

function create_consignment_ajax() {
	jQuery('#loading').show();
	var data = '';
	// eParcel Data
	$jEparcel('#eparcel_sales_order_view').find('input, select, textarea').each(function() {
        if($jEparcel(this).attr('type') == 'checkbox') {
			if($jEparcel('#'+ $jEparcel(this).attr('id')).is(':checked')) {
        		data += '&'+ $jEparcel(this).attr('name') +'='+ $jEparcel(this).val();
			}
		} else {
        	data += '&'+ $jEparcel(this).attr('name') +'='+ $jEparcel(this).val();
		}
    });
    data += '&post_ID='+ $jEparcel('#post_ID').val();
    data += '&action=create_consignment_ajax';
    
	$jEparcel.post(linksync_object.ajaxurl, data, function(res) {
		window.location.reload(true);
	});
}

/* Edit Consignment */
function validateEditDimensions()
{
    var dimensions = [
        $jEparcel('#article_height1').val(),
        $jEparcel('#article_width1').val(),
        $jEparcel('#article_length1').val()
    ];
    var shouldBe2 = 0;

    dimensions.forEach(function(number){
      shouldBe2 += (number >= 5) ? 1 : 0;
    });

    return shouldBe2;
}

function submitEditConsigmentForm()
{
	var weightPerArticle = 22;
	var weight = $jEparcel('#overallweight').val();
    var use_dimension = $jEparcel('#use_dimension').val();
	var valid = true;
	
	$jEparcel('.required-entry').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		if(value.length == 0 && valid)
		{
			valid = false;
		}
	});
    if(use_dimension == 1 && validateEditDimensions() < 2)
    {
        alert('At least 2 dimensions must be 5 cm.');
        return false;
    }

	if(!valid)
	{
		alert('Please enter all the mandatory fields');
		return false;
	}
	
	$jEparcel('.positive-number').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		var label = $jEparcel(this).attr('label');
		if(isNaN(value))
		{
			alert(label +' should be a number');
			valid = false;
		}
		
		value = parseInt(value);
		if(value < 0)
		{
			alert(label +' should be a postive number');
			valid = false;
		}
		
	});
	
	if(!valid)
	{
		return false;
	}
	
	var use_order_weight = $jEparcel('#use_order_weight').val();
	if(use_order_weight == 1) {
		$jEparcel('.maximum-value').each(function(){
			var value = $jEparcel.trim($jEparcel(this).val());
			var label = $jEparcel(this).attr('label');
			value = parseFloat(value);
			if(value > weightPerArticle)
			{
				alert('Allowed weight per article is '+ weightPerArticle);
				valid = false;
			}
			
		});
		if(!valid)
		{
			return false;
		}
		
		var totalInputWeight = 0;
		$jEparcel('.article_weight').each(function(){
			var value = $jEparcel.trim($jEparcel(this).val());
			value = parseFloat(value);
			totalInputWeight += value;
		});

		if(totalInputWeight < weight)
		{
			if(!confirm('Combined article weight is less than the total order weight. Do you want to continue?'))
				return false;
			$jEparcel('#edit_form').submit();
		}
		else
		{
			$jEparcel('#edit_form').submit();
		}
	} else {
		$jEparcel('#edit_form').submit();
	}
}

/* Edit Article */
function submitEditArticleForm()
{
	var weightPerArticle = 22;
	var valid = true;
	
	$jEparcel('.required-entry').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		if(value.length == 0 && valid)
		{
			valid = false;
		}
	});
	if(!valid)
	{
		alert('Please enter all the mandatory fields');
		return false;
	}

    var dimensions = [
        $jEparcel("#article_height").val(),
        $jEparcel("#article_width").val(),
        $jEparcel("#article_length").val()
    ]

    if(validateDimensions(dimensions) < 2) {
        alert('At least 2 dimensions must be 5 cm.');
        return false;
    }
	
	$jEparcel('.positive-number').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		var label = $jEparcel(this).attr('label');
		if(isNaN(value))
		{
			alert(label +' should be a number');
			valid = false;
		}
		
		value = parseInt(value);
		if(value < 0)
		{
			alert(label +' should be a postive number');
			valid = false;
		}
		
	});
	if(!valid)
	{
		return false;
	}
	
	var use_order_weight = $jEparcel('#use_order_weight').val();
	if(use_order_weight == 1) {
		$jEparcel('.maximum-value').each(function(){
			var value = $jEparcel.trim($jEparcel(this).val());
			var label = $jEparcel(this).attr('label');
			value = parseFloat(value);
			if(value > weightPerArticle)
			{
				alert('Allowed weight per article is '+ weightPerArticle);
				valid = false;
			}
			
		});
		if(!valid)
		{
			return false;
		}
		else
		{
			$jEparcel('#edit_form').submit();
		}
	} else {
		$jEparcel('#edit_form').submit();
	}
}

function isDimensionValidForMultipleArticles()
{
    var heights = $jEparcel("[name$='[height]']").filter(function(){ return this.name.match(/article([0-9]+\[)height(\])/)});
    var widths = $jEparcel("[name$='[width]']").filter(function(){ return this.name.match(/article([0-9]+\[)width(\])/)});
    var lengths = $jEparcel("[name$='[length]']").filter(function(){ return this.name.match(/article([0-9]+\[)length(\])/)});

    var articles =  $jEparcel("#number_of_articles").val();

    var dimensions;

    var result = { isNotValid: false, articleNum: null };

    for(var i = 0; i < articles; i++) {
        dimensions = [
            heights[i].value,
            widths[i].value,
            lengths[i].value
        ]

        if(validateDimensions(dimensions) < 2) {
            result.isNotValid = true;
            result.articleNum = i + 1;
            return result;
        }
    }

    return result;
}

function submitConsignmentBulkForm()
{
	var action = jQuery('select[name="action"]').val();
	if(action == -1)
	{
		action = jQuery('select[name="action2"]').val();
	}
	if(action == 'massUnassignConsignment' || action == 'massDeleteConsignment' || action == 'massMarkDespatched')
	{
		return confirm('Are you sure?');
	}
	if(action == 'massGenerateLabels') {
		var c = checkAllOrders(true);
		if(c.length > 0) {
			var data = {
				'action':'generate_labels_ajax',
				'order':c
			};
			jQuery('#loading').show();
			$jEparcel.post(linksync_object.ajaxurl, data, function(res) {
				window.location.reload(true);
			});
		} else {
			alert('Please select consignments');
		}
		return false;
	}
	return true;
}

function checkAllOrders(all)
{
	var allvalues = [];
	jQuery('.massaction-checkbox:checked').each(function() {
		var v = jQuery(this).val();
		var r = v.split("_");
		if(r[1] != 0) {
			if(all) {
				allvalues.push(v);
			} else {
				allvalues.push(r[0]);
			}
		}
	});
	return allvalues;
}

function setLocationConfirmOrderListDialog()
{
	if(!jQuery('#despatchManifest').hasClass('disabled'))
	{
		var mode = $jEparcel('#consignment-orderlist').attr('data-mode');		
		if(mode == 1)
		{
			jQuery("#dialog2").dialog("open");
		}
		else
		{
			jQuery("#dialog").dialog("open");
		}
	}
}

function createConsignmentWeightArticleSubmitForm()
{
	var weight = $jEparcel('#overallweight').val();
	var weightPerArticle = 22;
	$jEparcel('#createConsignmentHidden').val(1);

	var valid = true;

	var value = $jEparcel.trim($jEparcel('#articles_type').val());
	if(value.length == 0 && valid)
	{
		valid = false;
		alert('Please select article type');
		return false;
	}

	$jEparcel('#custom_articles_container .required-entry').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		if(value.length == 0 && valid)
		{
			valid = false;
		}
	});
	if(!valid)
	{
		alert('Please enter/select all the mandatory fields');
		return false;
	}

    if(isDimensionValidForMultipleArticles().isNotValid == true)
    {
        var articleNumber = isDimensionValidForMultipleArticles().articleNum;
        if(articleNumber != null) {
            alert('Article ' + articleNumber + ' must have at least 2 dimensions must be 5 cm.');
        } else {
            alert('At least 2 dimensions must be 5 cm.');
        }
        return false;
    }

	$jEparcel('.positive-number').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		var label = $jEparcel(this).attr('label');
		if(isNaN(value))
		{
			alert(label +' should be a number');
			valid = false;
		}

		value = parseInt(value);
		if(value < 0)
		{
			alert(label +' should be a postive number');
			valid = false;
		}

	});
	if(!valid)
	{
		return false;
	}

	$jEparcel('.maximum-value').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		var label = $jEparcel(this).attr('label');
		value = parseFloat(value);
		if(value > weightPerArticle)
		{
			alert('Allowed weight per article is '+ weightPerArticle);
			valid = false;
		}

	});
	if(!valid)
	{
		return false;
	}

	var totalInputWeight = 0;
	$jEparcel('.article_weight').each(function(){
		var value = $jEparcel.trim($jEparcel(this).val());
		value = parseFloat(value);
		totalInputWeight += value;
	});

	if(totalInputWeight < weight)
	{
		if(!confirm('Combined article weight is less than the total order weight. Do you want to continue?'))
			return false;
		create_consignment_ajax();
		return false;
	}
	else
	{
		create_consignment_ajax();
		return false;
	}
}