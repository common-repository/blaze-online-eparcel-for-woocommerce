<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php if(!LinksynceparcelHelper::isSoapInstalled()) { ?>
	<br /><br />
	<span style="color:red"><strong>PHP Soap extension is not enabled on your server, contact your web hoster to enable this extension.</strong></span>
<?php }else {?>
<div class="wrap woocommerce linksync-configuration">
    <form method="post" id="mainform" action="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=configuration'); ?>" enctype="multipart/form-data">
        <?php wp_nonce_field( 'linksync_configuration_action', 'linksync_configuration_nonce_field' ); ?>
        <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
        <h2><img src="<?php echo linksynceparcel_URL?>assets/images/logo-blaze.png"/>&nbsp;<?php _e('Blaze Online eParcel Configuration','linksynceparcel'); ?></h2>
        <?php 
        if(isset($result)) { 
            echo '<h3 style="color:green">'.$result.'</h3>'; 
        }
		if(isset($error)) { 
            echo '<h4 style="color:red">'.$error.'</h4>'; 
        }
        ?>
        <fieldset>
			<h3>Account Settings</h3>
            <table width="100%" border="0" cellspacing="0" cellpadding="6">
                 <tr>
                    <td width="20%" valign="top"><?php _e('API Key','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <input type="text" size="40" name="laid" value="<?php echo LinksynceparcelHelper::getFormValue('laid',get_option('linksynceparcel_laid'))?>">
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="The Blaze Online Application ID (LAID) is a unique API Key that's created when you link two apps via the Blaze Online dashboard. You need a valid API Key for this Blaze Online module to work."/>
                       <br />
                        <span class="comment">Note that once you save your API Key it will be permanently linked to (display site URL). If you change the URL of the site, or want to use the API Key on a different site, you’ll need to <a target="_blank" href="https://blaze.online/help/support-request">contact Blaze Online support</a> to have them reset the Site URL.</span>
                   </td>
              </tr>
              <tr>
                    <td width="20%" valign="top"><?php _e('eParcel Post Charge to Account','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <input type="text" size="40" name="post_charge_to_account" value="<?php echo LinksynceparcelHelper::getFormValue('post_charge_to_account',get_option('linksynceparcel_post_charge_to_account'))?>">
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Issued by Australia Post. Merchant account to which Australia Post 'post' the charges against for invoicing purposes."/>
                </td>
              </tr>
			         <tr>
                    <td width="20%" valign="top"><?php _e('Shipping & Tracking API Key','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <input type="text" size="40" name="st_apikey" value="<?php echo LinksynceparcelHelper::getFormValue('st_apikey',get_option('linksynceparcel_st_apikey'))?>">
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Shipping & Tracking API key - Issued by Australia Post for logistics needs; lodge orders, print labels, dispatch your parcels and track their progress from source to destination."/>
                       <br>
                       <span class="comment">please refer to <a href="https://help.linksync.com/hc/en-us/articles/115000764764-How-do-I-get-my-Account-Number-API-Key-and-API-Secret-from-Australia-Post-" target="_blank">Registering for Australia Post Shipping and Tracking</a> for more information.</span>
                 </td>
              </tr>
               <tr>
                    <td width="20%" valign="top"><?php _e('Shipping & Tracking password','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <input type="text" size="40" name="st_password" value="<?php echo LinksynceparcelHelper::getFormValue('st_password',get_option('linksynceparcel_st_password'))?>">
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Shipping & Tracking Password - Issued by Australia Post for logistics needs; lodge orders, print labels, dispatch your parcels and track their progress from source to destination."/>
                 </td>
              </tr>
             
              <tr>
                <td width="20%" valign="top"><?php _e('Operation Mode','linksynceparcel'); ?></td>
                <td align="left" colspan="2">
                    <select name="operation_mode">
                        <option value="0" <?php if (LinksynceparcelHelper::getFormValue('operation_mode',get_option('linksynceparcel_operation_mode')) != 1){ echo 'selected="selected"'; }?>><?php _e('Test','linksynceparcel'); ?></option>
                        <option value="1" <?php if (LinksynceparcelHelper::getFormValue('operation_mode',get_option('linksynceparcel_operation_mode')) == 1){ echo 'selected="selected"'; }?>><?php _e('Live','linksynceparcel'); ?></option>
                  </select>
                  <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Test mode enables you to use and test all features of the linksync eParcel for WooCommerce module without actually submitting a manifest to Australia Post on despatch of a manifest. Live mode will upload your manifest to Australia Post SFTP server on despatch of a manifest."/>
                </td>
              </tr>
			  </table>
			  <h3>Domestic Consignments</h3>
			  <table width="100%" border="0" cellspacing="0" cellpadding="6">
				<tr>
                    <td width="20%" valign="top"><?php _e('Insurance','linksynceparcel'); ?></td>
                    <td align="left">
                       <select name="insurance">
                            <option value="0" <?php if (LinksynceparcelHelper::getFormValue('insurance',get_option('linksynceparcel_insurance')) != 1){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
                            <option value="1" <?php if (LinksynceparcelHelper::getFormValue('insurance',get_option('linksynceparcel_insurance')) == 1){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
                      </select>
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Add insurance to consignment articles by default? This default can be overridden when creating consignments/articles."/>
                </td>
              </tr>
              <tr>
                    <td width="20%" valign="top"><?php _e('Default Insurance value','linksynceparcel'); ?></td>
                    <td align="left">
                       <input type="text" size="40" name="default_insurance_value" value="<?php echo LinksynceparcelHelper::getFormValue('default_insurance_value',get_option('linksynceparcel_default_insurance_value'))?>">
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Default insurance value to add to consignment articles. This default can be overridden when creating consignments/articles."/>
                </td>
              </tr>
				<tr>
                    <td width="20%" valign="top"><?php _e('Signature Required','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                        <select name="signature_required">
                            <option value="0" <?php if (LinksynceparcelHelper::getFormValue('signature_required',get_option('linksynceparcel_signature_required')) != 1){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
                            <option value="1" <?php if (LinksynceparcelHelper::getFormValue('signature_required',get_option('linksynceparcel_signature_required')) == 1){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
                      </select>
                      <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Default setting for 'Signature Required' on consignments. This default can be overridden when creating consignments. Note: if set to 'No', customers will be prompted to confirm that they authorise their delivery to be left if no one is available to sign for it, and then be required to enter special instructions. eg 'leave at side door' - these instructions will show on labels associated with the consignment for the order."/>
                </td>
              </tr>
			  <tr>
                    <td width="20%" valign="top"><?php _e('Safe Drop','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                        <select name="safe_drop">
                            <option value="0" <?php if (LinksynceparcelHelper::getFormValue('safe_drop',get_option('linksynceparcel_safe_drop')) != 1){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
                            <option value="1" <?php if (LinksynceparcelHelper::getFormValue('safe_drop',get_option('linksynceparcel_safe_drop')) == 1){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
                      </select>
                      <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title='Your customers can interact "in flight" with their "Signature on Delivery" domestic eParcel service and request that Australia Post leave the parcel in a Safe Place (Authority To Leave) without a signature. Select No to disable this option.'/>
                </td>
              </tr>
                  <tr>
                    <td width="20%" valign="top"><?php _e('Partial Delivery Allowed','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                        <select name="partial_delivery_allowed">
                            <option value="0" <?php if (LinksynceparcelHelper::getFormValue('partial_delivery_allowed',get_option('linksynceparcel_partial_delivery_allowed')) != 1){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
                            <option value="1" <?php if (LinksynceparcelHelper::getFormValue('partial_delivery_allowed',get_option('linksynceparcel_partial_delivery_allowed')) == 1){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
                      </select>
                      <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Default setting for specifying if partial delivery is allowed for consignments. This default can be overridden when creating consignments."/>
                    </td>
              </tr>
                  <tr>
                    <td width="20%" valign="top"><?php _e('Australia Post email notification','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                        <select name="post_email_notification">
                            <option value="0" <?php if (LinksynceparcelHelper::getFormValue('post_email_notification',get_option('linksynceparcel_post_email_notification')) != 1){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
                            <option value="1" <?php if (LinksynceparcelHelper::getFormValue('post_email_notification',get_option('linksynceparcel_post_email_notification')) == 1){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
                      </select>
                      <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Default setting for using Australia Post's email notification service on consignments. This default can be overridden when creating consignments."/>
                    </td>
              </tr>
              </table>
			  <h3>International Consignments</h3>
			   <table width="100%" border="0" cellspacing="0" cellpadding="6">
			   <tr>
                    <td width="20%" valign="top"><?php _e('Insurance','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <select id="insurance" name="int_insurance">
                            <option value="0" <?php if (LinksynceparcelHelper::getFormValue('int_insurance',get_option('linksynceparcel_int_insurance')) != 1){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
                            <option value="1" <?php if (LinksynceparcelHelper::getFormValue('int_insurance',get_option('linksynceparcel_int_insurance')) == 1){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
                      </select>
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Add insurance to international consignment articles by default? This default can be overridden when creating consignments/articles."/>
                </td>
              </tr>
			  <?php 
				$s_insurance = 'hide-tr';
				if (get_option('linksynceparcel_int_insurance') == 1){
					$s_insurance = 'show-tr';
				}
				$order_value_insurance = '';
				if(get_option('linksynceparcel_order_value_insurance') == 1) {
					$order_value_insurance = 'checked="checked"';
				}
			?>
			  <tr class="order_value_insurance <?php echo $s_insurance; ?>">
                    <td width="20%" valign="top"><?php _e('Order value as Insured Value','linksynceparcel'); ?></td>
					<td align="left" colspan="2">
						<input type="checkbox" id="order_value_insurance" name="order_value_insurance" value="1" <?php echo $order_value_insurance; ?>>
					</td>
				</tr>
			  <?php 
				$d_insurance = 'hide-tr';
				if(empty($order_value_insurance) && get_option('linksynceparcel_int_insurance') == 1){ 
					$d_insurance = 'show-tr';
				}
			?>
              <tr class="default_insurance_value <?php echo $d_insurance; ?>">
                    <td width="20%" valign="top"><?php _e('Default Insurance value','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <input type="text" size="40" id="default_insurance_value" name="int_default_insurance_value" value="<?php echo LinksynceparcelHelper::getFormValue('int_default_insurance_value',get_option('linksynceparcel_int_default_insurance_value'))?>">
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Default insurance value to add to international consignment articles. This default can be overridden when creating consignments/articles."/>
                </td>
              </tr>
				<tr class="hide-tr">
                    <td width="20%" valign="top"><?php _e('Order value as Declared Value','linksynceparcel'); ?></td>
					<td align="left" colspan="2">
						<?php 
							$declared_checked = '';
							if (LinksynceparcelHelper::getFormValue('declared_value',get_option('linksynceparcel_declared_value')) == 1){ 
								$declared_checked = 'checked="checked"';
							}
						?>
						<input type="checkbox" id="declared_value" name="declared_value" value="1" <?php echo $declared_checked; ?>>
					</td>
				</tr>
				<?php 
					$declared_text = '';
					$declared_text_option = 'hide-tr';
					if ($declared_checked == ''){ 
						$declared_text = LinksynceparcelHelper::getFormValue('declared_value_text',get_option('linksynceparcel_declared_value_text'));
						$declared_text_option = 'show-tr';
					}
				?>
				<tr>
                    <td width="20%" valign="top"><?php _e('Declare Value','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
						<?php
							$order_value_declared_value = LinksynceparcelHelper::getFormValue('order_value_declared_value',get_option('linksynceparcel_order_value_declared_value'));
						?>
                        <select id="order_value_declared_value" name="order_value_declared_value">
                            <option value="0" <?php if ($order_value_declared_value == '0'){ echo 'selected="selected"'; }?>><?php _e('Order Value','linksynceparcel'); ?></option>
                            <option value="1" <?php if ($order_value_declared_value == '1'){ echo 'selected="selected"'; }?>><?php _e('Order Value with Maximum','linksynceparcel'); ?></option>
							<option value="2" <?php if ($order_value_declared_value == '2'){ echo 'selected="selected"'; }?>><?php _e('Fixed Value','linksynceparcel'); ?></option>
						</select>
					</td>
				</tr>
				<?php 
					$maximum_declared_value = LinksynceparcelHelper::getFormValue('maximum_declared_value',get_option('linksynceparcel_maximum_declared_value'));
					$maximum_declared_value_class = 'hide-tr';
					if($order_value_declared_value == 1) {
						$maximum_declared_value_class = 'show-tr';
					}
				?>
				<tr id="maximum_declared_value" class="<?php echo $maximum_declared_value_class; ?>">
                    <td width="20%" valign="top"><?php _e('Maximum Declared Value','linksynceparcel'); ?></td>
					<td align="left" colspan="2">
						<input type="number" class="maximum_declared_value" name="maximum_declared_value" value="<?php echo $maximum_declared_value; ?>"><br />
					</td>
				</tr>
				<?php 
					$fixed_declared_value = LinksynceparcelHelper::getFormValue('fixed_declared_value',get_option('linksynceparcel_fixed_declared_value'));
					$fixed_declared_value_class = 'hide-tr';
					if($order_value_declared_value == 2) {
						$fixed_declared_value_class = 'show-tr';
					}
				?>
				<tr id="fixed_declared_value" class="<?php echo $fixed_declared_value_class; ?>">
                    <td width="20%" valign="top"><?php _e('Fixed Declared Value','linksynceparcel'); ?></td>
					<td align="left" colspan="2">
						<input type="number" class="fixed_declared_value" name="fixed_declared_value" value="<?php echo $fixed_declared_value; ?>"><br />
					</td>
				</tr>
				<tr>
                    <td width="20%" valign="top"><?php _e('Has Commercial Value','linksynceparcel'); ?></td>
					<td align="left" colspan="2">
						<?php
							$commercial_checked = '';
							if (LinksynceparcelHelper::getFormValue('has_commercial_value',get_option('linksynceparcel_has_commercial_value')) == 1){ 
								$commercial_checked = 'checked="checked"';
							}
						?>
						<input type="checkbox" id="has_commercial_value" name="has_commercial_value" value="1" <?php echo $commercial_checked; ?>>
					</td>
				</tr>
				<tr>
                    <td width="20%" valign="top"><?php _e('Default Product Classification','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
						<?php
							$product_classification_disable = '';
							$product_classification_value = LinksynceparcelHelper::getFormValue('product_classification',get_option('linksynceparcel_product_classification'));
							if($commercial_checked != '') {
								$product_classification_disable = 'disabled="true"';
								$product_classification_value = '991';
							}
						?>
                        <select id="product_classification" name="product_classification" <?php echo $product_classification_disable; ?>>
                            <option value="991" <?php if ($product_classification_value == '991'){ echo 'selected="selected"'; }?>><?php _e('Other','linksynceparcel'); ?></option>
                            <option value="32" <?php if ($product_classification_value == '32'){ echo 'selected="selected"'; }?>><?php _e('Commercial','linksynceparcel'); ?></option>
							<option value="31" <?php if ($product_classification_value == '31'){ echo 'selected="selected"'; }?>><?php _e('Gift','linksynceparcel'); ?></option>
							<option value="91" <?php if ($product_classification_value == '91'){ echo 'selected="selected"'; }?>><?php _e('Document','linksynceparcel'); ?></option>
						</select>
					</td>
				</tr>
				<?php 
					$product_classification_text = '';
					$product_classification_option = 'hide-tr';
					if ($product_classification_value == '991'){ 
						$product_classification_text = LinksynceparcelHelper::getFormValue('product_classification_text',get_option('linksynceparcel_product_classification_text'));
						$product_classification_option = 'show-tr';
					}
				?>
				<tr id="product_classification_text" class="<?php echo $product_classification_option; ?>">
                    <td width="20%" valign="top" ><?php _e('Classification Explanation','linksynceparcel'); ?></td>
					<td align="left" colspan="2">
						<input type="text" class="product_classification_text" name="product_classification_text" value="<?php echo $product_classification_text; ?>">
					</td>
				</tr>
				<tr>
                    <td width="20%" valign="top"><?php _e('Default Country of Origin','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <select name="country_origin">
                       		<option value="" <?php if (LinksynceparcelHelper::getFormValue('country_origin',get_option('linksynceparcel_country_origin')) == ''){ echo 'selected="selected"'; }?>>
								<?php _e('Please select','linksynceparcel'); ?>
                            </option>
                            <?php foreach($countries as $code => $name) {?>
                            <option value="<?php echo $code; ?>" <?php if (LinksynceparcelHelper::getFormValue('country_origin',get_option('linksynceparcel_country_origin')) == $code){ echo 'selected="selected"'; }?>>
                                <?php echo $name; ?>
                            </option>
                            <?php } ?>
                      </select>
					</td>
				</tr>
				<tr>
                    <td width="20%" valign="top" ><?php _e('Default HS Tariff','linksynceparcel'); ?></td>
					<td align="left" colspan="2">
						<input type="number" name="hs_tariff" value="<?php echo LinksynceparcelHelper::getFormValue('hs_tariff',get_option('linksynceparcel_hs_tariff')); ?>" min="0"><br/>
						<span class="comment"><a target="_blank" href="http://www.foreign-trade.com/reference/hscode.htm"><?php _e("Click here for HS Tariff list",'linksynceparcel'); ?></a></span>
					</td>
				</tr>
				<tr>
                    <td width="20%" valign="top" ><?php _e('Default Contents','linksynceparcel'); ?></td>
					<td align="left" colspan="2">
						<input type="text" name="default_contents" value="<?php echo LinksynceparcelHelper::getFormValue('default_contents',get_option('linksynceparcel_default_contents')); ?>">
					</td>
				</tr>
				<?php
					$user_order_details = LinksynceparcelHelper::getFormValue('user_order_details',get_option('linksynceparcel_user_order_details'));
				?>
				<tr>
                    <td width="20%" valign="top" ><?php _e('User order details for description on customs docs','linksynceparcel'); ?></td>
					<td align="left" colspan="2">
						<select id="user_order_details" name="user_order_details">
                            <option value="1" <?php if ($user_order_details == '1'){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
                            <option value="0" <?php if ($user_order_details == '0'){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
						</select>
					</td>
				</tr>
				<?php
					$class = 'class="hide-tr"';
					if($user_order_details == '0') {
						$class = 'class="show-tr"';
					}
				?>
				<tr id="default_good_description" <?php echo $class; ?>>
                    <td width="20%" valign="top" ><?php _e('Default Product Description','linksynceparcel'); ?></td>
					<td align="left" colspan="2">
						<input type="text" class="default_good_description_text" name="default_good_description" value="<?php echo LinksynceparcelHelper::getFormValue('default_good_description',get_option('linksynceparcel_default_good_description')); ?>">
					</td>
				</tr>
				
				</table>
				<h3>Label Settings</h3>
				<table width="100%" border="0" cellspacing="0" cellpadding="6">
				<tr>
					<td width="25%" valign="top" ><strong><?php _e('Service','linksynceparcel'); ?></strong></td>
					<td width="25%" valign="top" ><strong><?php _e('Label Type','linksynceparcel'); ?></strong></td>
					<td width="25%" valign="top" ><strong><?php _e('Left Offset','linksynceparcel'); ?></strong></td>
					<td width="25%" valign="top" ><strong><?php _e('Top Offset','linksynceparcel'); ?></strong></td>
				</tr>
				<tr>
					<td width="25%" valign="top" ><?php _e('Parcel Post','linksynceparcel'); ?></td>
					<td width="25%" align="left" >
						<select name="parcel_post_label">
						<?php
							$parcel_post_label = LinksynceparcelHelper::getFormValue('parcel_post_label',get_option('linksynceparcel_parcel_post_label'));
						?>
                            <option value="A4-4pp_1" <?php if($parcel_post_label=='A4-4pp_1'){ echo "selected='selected'"; }?>> A4 plain </option>
                            <option value="A4-4pp_0" <?php if($parcel_post_label=='A4-4pp_0'){ echo "selected='selected'"; }?>> A4 pre-printed </option>
                            <option value="A4-1pp_1" <?php if($parcel_post_label=='A4-1pp_1'){ echo "selected='selected'"; }?>> A4 1pp plain </option>
                            <option value="A4-1pp_0" <?php if($parcel_post_label=='A4-1pp_0'){ echo "selected='selected'"; }?>> A4 1pp pre-printed </option>
                            <option value="A6-1pp_1" <?php if($parcel_post_label=='A6-1pp_1'){ echo "selected='selected'"; }?>> A6 plain </option>
                            <option value="A6-1pp_0" <?php if($parcel_post_label=='A6-1pp_0'){ echo "selected='selected'"; }?>> A6 pre-printed </option>
                    	</select>
					</td>
					<td width="25%" align="left" >
					<?php
						$parcel_post_left_offset = LinksynceparcelHelper::getFormValue('parcel_post_left_offset',get_option('linksynceparcel_parcel_post_left_offset'));
						$parcel_post_left_offset = !empty($parcel_post_left_offset)?$parcel_post_left_offset:0;
					?>
						<input type="number" name="parcel_post_left_offset" value="<?php echo $parcel_post_left_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
					<td width="25%" align="left" >
					<?php
						$parcel_post_right_offset = LinksynceparcelHelper::getFormValue('parcel_post_right_offset',get_option('linksynceparcel_parcel_post_right_offset'));
						$parcel_post_right_offset = !empty($parcel_post_right_offset)?$parcel_post_right_offset:0;
					?>
						<input type="number" name="parcel_post_right_offset" value="<?php echo $parcel_post_right_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
				</tr>
				<tr>
					<td width="25%" valign="top" ><?php _e('Express Post','linksynceparcel'); ?></td>
					<td width="25%" align="left" >
						<select name="express_post_label">
						<?php
							$express_post_label = LinksynceparcelHelper::getFormValue('express_post_label',get_option('linksynceparcel_express_post_label'));
						?>
                            <option value="A4-3pp_1" <?php if($express_post_label=='A4-3pp_1'){ echo "selected='selected'"; }?>> A4 plain </option>
                            <option value="A4-3pp_0" <?php if($express_post_label=='A4-3pp_0'){ echo "selected='selected'"; }?>> A4 pre-printed </option>
                            <option value="A4-1pp_1" <?php if($express_post_label=='A4-1pp_1'){ echo "selected='selected'"; }?>> A4 1pp plain </option>
                            <option value="A4-1pp_0" <?php if($express_post_label=='A4-1pp_0'){ echo "selected='selected'"; }?>> A4 1pp pre-printed </option>
                            <option value="A6-1pp_0" <?php if($express_post_label=='A6-1pp_0'){ echo "selected='selected'"; }?>> A6 pre-printed </option>
                    	</select>
					</td>
					<td width="25%" align="left" >
					<?php
						$express_post_left_offset = LinksynceparcelHelper::getFormValue('express_post_left_offset',get_option('linksynceparcel_express_post_left_offset'));
						$express_post_left_offset = !empty($express_post_left_offset)?$express_post_left_offset:0;
					?>
						<input type="number" name="express_post_left_offset" value="<?php echo $express_post_left_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
					<td width="25%" align="left" >
					<?php
						$express_post_right_offset = LinksynceparcelHelper::getFormValue('express_post_right_offset',get_option('linksynceparcel_express_post_right_offset'));
						$express_post_right_offset = !empty($express_post_right_offset)?$express_post_right_offset:0;
					?>
						<input type="number" name="express_post_right_offset" value="<?php echo $express_post_right_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
				</tr>
				<tr style="display:none;">
					<td width="25%" valign="top" ><?php _e('Int. Economy Air','linksynceparcel'); ?></td>
					<td width="25%" align="left" >
						<select name="int_economy_label">
						<?php
							$int_economy_label = LinksynceparcelHelper::getFormValue('int_economy_label',get_option('linksynceparcel_int_economy_label'));
						?>
                            <option value="A4-4pp_1" <?php if($int_economy_label=='A4-4pp_1'){ echo "selected='selected'"; }?>> A4 plain </option>
                            <option value="THERMAL LABEL-1PP_1" <?php if($int_economy_label=='THERMAL LABEL-1PP_1'){ echo "selected='selected'"; }?>> Single plain </option>
                    	</select>
					</td>
					<td width="25%" align="left" >
					<?php
						$int_economy_left_offset = LinksynceparcelHelper::getFormValue('int_economy_left_offset',get_option('linksynceparcel_int_economy_left_offset'));
						$int_economy_left_offset = !empty($int_economy_left_offset)?$int_economy_left_offset:0;
					?>
						<input type="number" name="int_economy_left_offset" value="<?php echo $int_economy_left_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
					<td width="25%" align="left" >
					<?php
						$int_economy_right_offset = LinksynceparcelHelper::getFormValue('int_economy_right_offset',get_option('linksynceparcel_int_economy_right_offset'));
						$int_economy_right_offset = !empty($int_economy_right_offset)?$int_economy_right_offset:0;
					?>
						<input type="number" name="int_economy_right_offset" value="<?php echo $int_economy_right_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
				</tr>
				<tr style="display:none;">
					<td width="25%" valign="top" ><?php _e('Int. Express Courier','linksynceparcel'); ?></td>
					<td width="25%" align="left" >
						<select name="int_express_courier_label">
						<?php
							$int_express_courier_label = LinksynceparcelHelper::getFormValue('int_express_courier_label',get_option('linksynceparcel_int_express_courier_label'));
						?>
                            <option value="A4-4pp_1" <?php if($int_express_courier_label=='A4-4pp_1'){ echo "selected='selected'"; }?>> A4 plain </option>
                            <option value="THERMAL LABEL-1PP_1" <?php if($int_express_courier_label=='THERMAL LABEL-1PP_1'){ echo "selected='selected'"; }?>> Single plain </option>
                    	</select>
					</td>
					<td width="25%" align="left" >
					<?php
						$int_express_courier_left_offset = LinksynceparcelHelper::getFormValue('int_express_courier_left_offset',get_option('linksynceparcel_int_express_courier_left_offset'));
						$int_express_courier_left_offset = !empty($int_express_courier_left_offset)?$int_express_courier_left_offset:0;
					?>
						<input type="number" name="int_express_courier_left_offset" value="<?php echo $int_express_courier_left_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
					<td width="25%" align="left" >
					<?php
						$int_express_courier_right_offset = LinksynceparcelHelper::getFormValue('int_express_courier_right_offset',get_option('linksynceparcel_int_express_courier_right_offset'));
						$int_express_courier_right_offset = !empty($int_express_courier_right_offset)?$int_express_courier_right_offset:0;
					?>
						<input type="number" name="int_express_courier_right_offset" value="<?php echo $int_express_courier_right_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
				</tr>
				<tr style="display:none;">
					<td width="25%" valign="top" ><?php _e('Int. Express Post','linksynceparcel'); ?></td>
					<td width="25%" align="left" >
						<select name="int_express_post_label">
						<?php
							$int_express_post_label = LinksynceparcelHelper::getFormValue('int_express_post_label',get_option('linksynceparcel_int_express_post_label'));
						?>
                            <option value="A4-4pp_1" <?php if($int_express_post_label=='A4-4pp_1'){ echo "selected='selected'"; }?>> A4 plain </option>
                            <option value="THERMAL LABEL-1PP_1" <?php if($int_express_post_label=='THERMAL LABEL-1PP_1'){ echo "selected='selected'"; }?>> Single plain </option>
                    	</select>
					</td>
					<td width="25%" align="left" >
					<?php
						$int_express_post_left_offset = LinksynceparcelHelper::getFormValue('int_express_post_left_offset',get_option('linksynceparcel_int_express_post_left_offset'));
						$int_express_post_left_offset = !empty($int_express_post_left_offset)?$int_express_post_left_offset:0;
					?>
						<input type="number" name="int_express_post_left_offset" value="<?php echo $int_express_post_left_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
					<td width="25%" align="left" >
					<?php
						$int_express_post_right_offset = LinksynceparcelHelper::getFormValue('int_express_post_right_offset',get_option('linksynceparcel_int_express_post_right_offset'));
						$int_express_post_right_offset = !empty($int_express_post_right_offset)?$int_express_post_right_offset:0;
					?>
						<input type="number" name="int_express_post_right_offset" value="<?php echo $int_express_post_right_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
				</tr>
				<tr style="display:none;">
					<td width="25%" valign="top" ><?php _e('Int. Pack & Track','linksynceparcel'); ?></td>
					<td width="25%" align="left" >
						<select name="int_pack_track_label">
						<?php
							$int_pack_track_label = LinksynceparcelHelper::getFormValue('int_pack_track_label',get_option('linksynceparcel_int_pack_track_label'));
						?>
                            <option value="A4-4pp_1" <?php if($int_pack_track_label=='A4-4pp_1'){ echo "selected='selected'"; }?>> A4 plain </option>
                            <option value="THERMAL LABEL-1PP_1" <?php if($int_pack_track_label=='THERMAL LABEL-1PP_1'){ echo "selected='selected'"; }?>> Single plain </option>
                    	</select>
					</td>
					<td width="25%" align="left" >
					<?php
						$int_pack_track_left_offset = LinksynceparcelHelper::getFormValue('int_pack_track_left_offset',get_option('linksynceparcel_int_pack_track_left_offset'));
						$int_pack_track_left_offset = !empty($int_pack_track_left_offset)?$int_pack_track_left_offset:0;
					?>
						<input type="number" name="int_pack_track_left_offset" value="<?php echo $int_pack_track_left_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
					<td width="25%" align="left" >
					<?php
						$int_pack_track_right_offset = LinksynceparcelHelper::getFormValue('int_pack_track_right_offset',get_option('linksynceparcel_int_pack_track_right_offset'));
						$int_pack_track_right_offset = !empty($int_pack_track_right_offset)?$int_pack_track_right_offset:0;
					?>
						<input type="number" name="int_pack_track_right_offset" value="<?php echo $int_pack_track_right_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
				</tr>
				<tr style="display:none;">
					<td width="25%" valign="top" ><?php _e('Int. Registered','linksynceparcel'); ?></td>
					<td width="25%" align="left" >
						<select name="int_registered_label">
						<?php
							$int_registered_label = LinksynceparcelHelper::getFormValue('int_registered_label',get_option('linksynceparcel_int_registered_label'));
						?>
                            <option value="A4-4pp_1" <?php if($int_registered_label=='A4-4pp_1'){ echo "selected='selected'"; }?>> A4 plain </option>
                            <option value="THERMAL LABEL-1PP_1" <?php if($int_registered_label=='THERMAL LABEL-1PP_1'){ echo "selected='selected'"; }?>> Single plain </option>
                    	</select>
					</td>
					<td width="25%" align="left" >
					<?php
						$int_registered_left_offset = LinksynceparcelHelper::getFormValue('int_registered_left_offset',get_option('linksynceparcel_int_registered_left_offset'));
						$int_registered_left_offset = !empty($int_registered_left_offset)?$int_registered_left_offset:0;
					?>
						<input type="number" name="int_registered_left_offset" value="<?php echo $int_registered_left_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
					<td width="25%" align="left" >
					<?php
						$int_registered_right_offset = LinksynceparcelHelper::getFormValue('int_registered_right_offset',get_option('linksynceparcel_int_registered_right_offset'));
						$int_registered_right_offset = !empty($int_registered_right_offset)?$int_registered_right_offset:0;
					?>
						<input type="number" name="int_registered_right_offset" value="<?php echo $int_registered_right_offset; ?>" min="-999" max="999">&nbsp;mm
					</td>
				</tr>
				<tr>
					<td colspan="4" valign="top" ><a target="_blank" href="https://blaze.online/help/labeltypes">Click here for an explanation of each Label Type.</a></td>
				</tr>
				</table>
				<h3>General Settings</h3>
				<table width="100%" border="0" cellspacing="0" cellpadding="6">
				<tr>
                    <td width="20%" valign="top"><?php _e('Copy order notes to label?','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                        <select name="copy_order_notes">
                            <option value="0" <?php if (LinksynceparcelHelper::getFormValue('copy_order_notes',get_option('linksynceparcel_copy_order_notes')) != 1){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
                            <option value="1" <?php if (LinksynceparcelHelper::getFormValue('copy_order_notes',get_option('linksynceparcel_copy_order_notes')) == 1){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
                      </select><br />
                        <span class="comment"><?php _e("If set to yes, then order notes will be copied to label",'linksynceparcel'); ?></span>
                </td>
              </tr>
              
              <tr>
                    <td width="20%" valign="top"><?php _e('Use order total weight','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                        <select name="use_order_weight" id="use_order_weight">
                            <option value="0" <?php if (LinksynceparcelHelper::getFormValue('use_order_weight',get_option('linksynceparcel_use_order_weight')) != 1){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
                            <option value="1" <?php if (LinksynceparcelHelper::getFormValue('use_order_weight',get_option('linksynceparcel_use_order_weight')) == 1){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
                      </select>
                      <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="If your products in WooCommerce have weights associated with them, you can use this option to use the total weight of the combined items on an order as the weight for the consignment. Aust Post eParcel requires that all weights are in KG, so if your product weight is entered in grams, lbs or oz (per WooCommerce Product settings), the combined order weight will be converted to KG for consignment and articles weights."/>
                  </td>
              </tr>
              
               <tr class="use_order_weight_1" style="<?php if (LinksynceparcelHelper::getFormValue('use_order_weight',get_option('linksynceparcel_use_order_weight')) != 1){?>display:none<?php }?>">
                    <td width="20%" valign="top"><?php _e('Packaging Allowance Type','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                        <select name="allowance_type">
                            <option value="F" <?php if (LinksynceparcelHelper::getFormValue('allowance_type',get_option('linksynceparcel_allowance_type')) != 'P'){ echo 'selected="selected"'; }?>><?php _e('Fixed','linksynceparcel'); ?></option>
                            <option value="P" <?php if (LinksynceparcelHelper::getFormValue('allowance_type',get_option('linksynceparcel_allowance_type')) == 'P'){ echo 'selected="selected"'; }?>><?php _e('Percentage','linksynceparcel'); ?></option>
                      </select>
                      <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Use this option if you want to add additional weight to your order total to allow for packaging. Use 'Fixed' if you want to add a set weight to each order, or use Percentage to add an allowance based on a percentage of the total weight for each order."/>
                  </td>
              </tr>
              
               <tr class="use_order_weight_1" style="<?php if (LinksynceparcelHelper::getFormValue('use_order_weight',get_option('linksynceparcel_use_order_weight')) != 1){?>display:none<?php }?>">
                    <td width="20%" valign="top"><?php _e('Packaging Allowance Value','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <input type="text" size="40" name="allowance_value" value="<?php echo LinksynceparcelHelper::getFormValue('allowance_value',get_option('linksynceparcel_allowance_value'))?>">
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Depending on the option you choose for 'Packaging Allowance Type', this value will determine what value to add to the total weight of the combined items on an order. If you select Fixed, then input the additional weight you want to add in KG. eg. .25 for .25 kg/250 grams. If you select percentage, then input the percentage you want to add to an order eg. 5 for 5%. Leave this field empty if you don't want to apply a packaging allowance to orders."/>
                </td>
              </tr>
              
              
              <tr class="use_order_weight_0" style="<?php if (LinksynceparcelHelper::getFormValue('use_order_weight',get_option('linksynceparcel_use_order_weight')) == 1){?>display:none<?php }?>">
                    <td width="20%" valign="top"><?php _e('Default Article Weight (Kgs)','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <input type="text" size="40" name="default_article_weight" value="<?php echo LinksynceparcelHelper::getFormValue('default_article_weight',get_option('linksynceparcel_default_article_weight'))?>">
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Set a default Article Weight. This value is used as the default value when selecting 'Custom' article type, and can be overridden when creating articles. Leave blank if you don't want to set a default."/>
                </td>
              </tr>

              
              <tr>
                    <td width="20%" valign="top"><?php _e('Use article dimensions','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                        <select name="use_dimension" id="use_dimension">
                            <option value="0" <?php if (LinksynceparcelHelper::getFormValue('use_dimension',get_option('linksynceparcel_use_dimension')) != 1){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
                            <option value="1" <?php if (LinksynceparcelHelper::getFormValue('use_dimension',get_option('linksynceparcel_use_dimension')) == 1){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
                      </select>
                      <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="If you're on a dead-weight of cubic-weight contract with Australia Post, then you may not be required to enter dimensions for each article you ship with eParcel. If that's the case, then set this option to No. If you are required to enter dimensions for articles, then set this option to Yes."/>
                  </td>
              </tr>
              
              
              <tr class="use_dimension" style="<?php if (LinksynceparcelHelper::getFormValue('use_dimension',get_option('linksynceparcel_use_dimension')) != 1){?>display:none<?php }?>">
                    <td width="20%" valign="top"><?php _e('Default Article Height (cm)','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <input type="text" size="40" name="default_article_height" value="<?php echo LinksynceparcelHelper::getFormValue('default_article_height',get_option('linksynceparcel_default_article_height'))?>">
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Set a default Article Height. This value is used as the default value when selecting 'Custom' article type, and can be overridden when creating articles. Leave blank if you don't want to set a default."/>
                </td>
              </tr>
              <tr class="use_dimension" style="<?php if (LinksynceparcelHelper::getFormValue('use_dimension',get_option('linksynceparcel_use_dimension')) != 1){?>display:none<?php }?>">
                    <td width="20%" valign="top"><?php _e('Default Article Width (cm)','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <input type="text" size="40" name="default_article_width" value="<?php echo LinksynceparcelHelper::getFormValue('default_article_width',get_option('linksynceparcel_default_article_width'))?>">
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Set a default Article Width. This value is used as the default value when selecting 'Custom' article type, and can be overridden when creating articles. Leave blank if you don't want to set a default."/>
                </td>
              </tr>
              <tr class="use_dimension" style="<?php if (LinksynceparcelHelper::getFormValue('use_dimension',get_option('linksynceparcel_use_dimension')) != 1){?>display:none<?php }?>">
                    <td width="20%" valign="top"><?php _e('Default Article Length (cm)','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <input type="text" size="40" name="default_article_length" value="<?php echo LinksynceparcelHelper::getFormValue('default_article_length',get_option('linksynceparcel_default_article_length'))?>">
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Set a default Article Length. This value is used as the default value when selecting 'Custom' article type, and can be overridden when creating articles. Leave blank if you don't want to set a default."/>
                </td>
              </tr>

              <tr>
                    <td width="20%" valign="top"><?php _e('Choose the statuses to show in the consignment view?','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <select name="display_choosen_status" id="display_choosen_status">
                             <option value="0" <?php if (LinksynceparcelHelper::getFormValue('display_choosen_status',get_option('linksynceparcel_display_choosen_status')) != 1){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
                            <option value="1" <?php if (LinksynceparcelHelper::getFormValue('display_choosen_status',get_option('linksynceparcel_display_choosen_status')) == 1){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
                      </select>
                      <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="By default, orders that have a status of 'Processing' or have an open consignment against them are displayed in the Consignment View. If you'd like to choose different order statuses to display in the Consignment View then set this option to Yes."/>
                </td>
              </tr>
              
              <tr class="display_choosen_status" style="<?php if (LinksynceparcelHelper::getFormValue('display_choosen_status',get_option('linksynceparcel_display_choosen_status')) != 1){?>display:none<?php }?>">
                    <td width="20%" valign="top"><?php _e('Select the statuses','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                    <?php 
						$chosen_statuses = get_option('linksynceparcel_chosen_statuses');
						if (isset($_REQUEST['linksynceparcel']['chosen_statuses']))
						{
							$post_chosen_statuses = $_REQUEST['linksynceparcel']['chosen_statuses'];
						}
					?>
                       <select name="chosen_statuses[]" multiple="multiple" size="6">
                       <?php if($is_greater_than_21){?>
                            <?php foreach($statuses as $term_id => $status) {?>
                            <option value="<?php echo $term_id?>" 
							<?php 
							if (isset($_REQUEST['linksynceparcel']['chosen_statuses']))
							{
								if($post_chosen_statuses && in_array($term_id,$post_chosen_statuses))
								{
									echo 'selected="selected"';
								}
							}
							else
							{
								if($chosen_statuses && in_array($term_id,$chosen_statuses))
								{
									echo 'selected="selected"';
								}
							}
							?>
							><?php echo $status?></option>
                            <?php } ?>
                       <?php }else{?>
                            <?php foreach($statuses as $status) {?>
                            <option value="<?php echo $status->slug?>" 
                            <?php 
							if (isset($_REQUEST['linksynceparcel']['chosen_statuses']))
							{
								if($post_chosen_statuses && in_array($status->slug,$post_chosen_statuses))
								{
									echo 'selected="selected"';
								}
							}
							else
							{
								if($chosen_statuses && in_array($status->slug,$chosen_statuses))
								{
									echo 'selected="selected"';
								}
							}
							?>
                            ><?php echo $status->name?></option>
                            <?php } ?>
                      <?php }?>
                      </select>
                      <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Select one or more order statuses to display in the Consignment View. Only orders with matching Order Statuses will be displayed in the Consignment View."/>
                </td>
              </tr>
              
              <tr>
                    <td width="20%" valign="top"><?php _e('Change order status on despatch of Manifest','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <select name="change_order_status">
                       <?php if($is_greater_than_21){?>
                       		 <option value="" <?php if (LinksynceparcelHelper::getFormValue('change_order_status',get_option('linksynceparcel_change_order_status')) == ''){ echo 'selected="selected"'; }?>>
								<?php _e('Please select','linksynceparcel'); ?>
                            </option>
                            <?php foreach($statuses as $term_id => $status) {?>
                            <option value="<?php echo $term_id?>" <?php if (LinksynceparcelHelper::getFormValue('change_order_status',get_option('linksynceparcel_change_order_status')) == $term_id){ echo 'selected="selected"'; }?>><?php echo $status?></option>
                            <?php } ?>
                       <?php }else{?>
                            <option value="" <?php if (LinksynceparcelHelper::getFormValue('change_order_status',get_option('linksynceparcel_change_order_status')) == ''){ echo 'selected="selected"'; }?>>
								<?php _e('Please select','linksynceparcel'); ?>
                            </option>
                            <?php foreach($statuses as $status) {?>
                            <option value="<?php echo $status->term_id?>" <?php if (LinksynceparcelHelper::getFormValue('change_order_status',get_option('linksynceparcel_change_order_status')) == $status->term_id){ echo 'selected="selected"'; }?>><?php echo $status->name?></option>
                            <?php } ?>
                      <?php }?>
                      </select>
                      <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Use this option to change the order status when a manifest is despatched."/>
                </td>
              </tr> 
              
              
                <tr>
                    <td width="20%" valign="top"><?php _e('Notify Customers on despatch of Manifest','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                        <select name="notify_customers">
                            <option value="0" <?php if (LinksynceparcelHelper::getFormValue('notify_customers',get_option('linksynceparcel_notify_customers')) != 1){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
                            <option value="1" <?php if (LinksynceparcelHelper::getFormValue('notify_customers',get_option('linksynceparcel_notify_customers')) == 1){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
                      </select>
                      <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="Use this option to notify customers of tracking numbers when a manifest is despatched. This default can be overridden when creating consignments."/>
                  </td>
              </tr>
              
                <tr>
                    <td width="20%" valign="top"><?php _e('From email address','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <input type="text" size="40" name="from_email_address" value="<?php echo LinksynceparcelHelper::getFormValue('from_email_address',get_option('linksynceparcel_from_email_address'))?>">
                       <img src="<?php echo linksynceparcel_URL?>assets/images/icon-tooltip.png" class="tooltip" title="The 'from' email address to be used when notifying customers of tracking information when a manifest is despatched."/>
                  </td>
              </tr>
              <tr>
                    <td width="20%" valign="top"><?php _e('Subject','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                       <input type="text" size="40" name="subject" value="<?php echo LinksynceparcelHelper::getFormValue('subject',get_option('linksynceparcel_subject'))?>">
                       <br />
                       <span class="comment"><?php _e("You can use the [TrackingNumber],[OrderNumber], [CustomerFirstname] dynamic variables",'linksynceparcel'); ?></span>
                </td>
              </tr>
              <tr>
                    <td width="20%" valign="top"><?php _e('Email Body','linksynceparcel'); ?></td>
                    <td align="left" colspan="2">
                    	<?php wp_editor(LinksynceparcelHelper::getFormValue('linksynceparcel_email_body',get_option('linksynceparcel_email_body'),''), 'linksynceparcel_email_body', array('textarea_rows'=>4), false);  ?>
                       <br />
                       <span class="comment"><?php _e("You can use the [TrackingNumber],[OrderNumber], [CustomerFirstname] dynamic variables",'linksynceparcel'); ?></span>
                </td>
              </tr>
			  </table>
			<dl class="accordion">
			<dt><a style="text-decoration:none;" href=""><h3 style="display: inline-block;margin-right: 15px;">Blaze Online support</h3><em>Click here to show options.</em></a></dt>
			<dd>
				<table width="100%" border="0" cellspacing="0" cellpadding="6">
				  <tr>
						<td width="20%" valign="top"><?php _e('Download Log','linksynceparcel'); ?></td>
            <td align="left" valign="top">
              <a href="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=downloadlog&ajax=true'); ?>" class="button-primary"><?php _e('Download Log','linksynceparcel'); ?></a>
              <br />
              <span class="comment"><?php _e("To be used if instructed by Blaze Online support",'linksynceparcel'); ?></span>
					</td>
				  </tr>
				  <tr>
						<td width="20%" valign="top"><?php _e('Enable Mark as Despatched Action on Consignment UI','linksynceparcel'); ?></td>
						<td align="left" colspan="2">
							<select name="mark_despatch">
								<option value="0" <?php if (LinksynceparcelHelper::getFormValue('mark_despatch',get_option('linksynceparcel_mark_despatch')) != 1){ echo 'selected="selected"'; }?>><?php _e('No','linksynceparcel'); ?></option>
								<option value="1" <?php if (LinksynceparcelHelper::getFormValue('mark_despatch',get_option('linksynceparcel_mark_despatch')) == 1){ echo 'selected="selected"'; }?>><?php _e('Yes','linksynceparcel'); ?></option>
							</select><br />
							<span class="comment"><?php _e("To be used if instructed by Blaze Online support",'linksynceparcel'); ?></span>
						</td>
					</tr>
				</table>
			</dd>
			</dl>
        </fieldset>
        <br />
        <input type="submit" name="submitConfiguration" value="<?php _e('Save','linksynceparcel'); ?>" class="button-primary" />
    </form>
</div>
<?php }?>