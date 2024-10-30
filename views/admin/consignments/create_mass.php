<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
$use_order_weight = (int)get_option('linksynceparcel_use_order_weight');
$use_dimension = (int)get_option('linksynceparcel_use_dimension');
?>
<div id="loading" style="display:none;">
	<div id="img-loader">
		<img src="<?php echo linksynceparcel_URL?>assets/images/load.gif" alt="Loading" />
	</div>
</div>
<div class="entry-edit wp-core-ui">
    <h3>Create Consignment</h3>
</div>

<?php if(LinksynceparcelValidator::validateConsignmentLimit()  && ! LinksyncUserHelper::isTerminated()): ?>
<?php LinksyncUserHelper::setCappingMessage(false); ?>
<?php else: ?>
<div class="entry-edit wp-core-ui mass-create-consignment" id="eparcel_sales_order_view">
    <form name="edit_form" id="edit_form" method="post" action="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=create-mass-consignment'); ?>">
    	<?php wp_nonce_field( 'create_mass_consignment_action', 'create_mass_consignment_nonce_field' ); ?>
    	<div class="box" id="presets">
        <input id="number_of_articles" name="number_of_articles" size="4" value="1" type="hidden"/>
        <?php
		foreach($orders as $order)
		{
		?>
		<input name="order[]" value="<?php echo $order?>" type="hidden"/>
		<?php
		}
		?>
        Article Type&nbsp;&nbsp;
        <select id="articles_type" name="articles_type" class="required-entry2" style="padding:3px" >
        	<?php if($use_order_weight == 1):?>
            	<option value="order_weight">Use Order Weight</option>
            <?php endif;?>
            <?php if($use_dimension == 1):?>
            <?php
            foreach($presets as $preset)
            {
                ?>
                <option value="<?php echo $preset->id ?>">
                    <?php echo $preset->name. ' ('.$preset->weight.'kg - '.$preset->height.'x'.$preset->width.'x'.$preset->length.')'?>
                </option>
                <?php
            }
            ?>
            <?php endif;?>
        </select>
        &nbsp;&nbsp;&nbsp;&nbsp;

    	<input type="submit" id="createMassButton" name="createConsignment" value="Create Consignment" class="button-primary button create-consignment1 scalable save submit-button"/>
        &nbsp;&nbsp;
        <button onclick="setLocation('<?php echo admin_url('admin.php?page=linksynceparcel')?>')" class="scalable back button" type="button" >
        	<span><span><span>Cancel</span></span></span>
    	</button>
</div>

<div class="box consignment-fields" style="display:none">
    <h3>Consignment Fields</h3>
    <table width="100%" border="0" cellspacing="6" cellpadding="6" class="tablecustom">
	  <tr>
			<td width="30%">Delivery instructions</td>
			<td>
				<textarea name="delivery_instruction" maxlength="256" cols="40" rows="4"></textarea>
			</td>
		</tr>
      <tr>
        <td width="30%">Partial Delivery allowed?</td>
        <td>
        <select id="partial_delivery_allowed" name="partial_delivery_allowed"  style="width:140px">
            <option value="1" <?php echo (get_option('linksynceparcel_partial_delivery_allowed')==1?'selected':'')?>>Yes</option>
            <option value="0" <?php echo (get_option('linksynceparcel_partial_delivery_allowed')!=1?'selected':'')?>>No</option>
        </select>
        </td>
      </tr>

      <tr>
        <td>Cash to collect</td>
        <td><input id="cash_to_collect" name="cash_to_collect" type="text" /></td>
      </tr>

      <tr>
        <td>Delivery signature required?</td>
        <td><select id="delivery_signature_allowed" name="delivery_signature_allowed" style="width:140px">>
            <option value="1" <?php echo (get_option('linksynceparcel_signature_required')==1?'selected':'')?>>Yes</option>
            <option value="0" <?php echo (get_option('linksynceparcel_signature_required')!=1?'selected':'')?>>No</option>
        </select></td>
      </tr>
	  <tr>
        <td>Safe Drop</td>
        <td><select id="safe_drop" name="safe_drop">
			<option value="1" <?php echo (get_option('linksynceparcel_safe_drop') == 1?'selected="selected"':'');?>>Yes</option>
            <option value="0" <?php echo (get_option('linksynceparcel_safe_drop') != 1?'selected="selected"':'');?>>No</option>
        </select></td>
      </tr>
	  <tr>
		<td>Insurance</td>
		<td>
			<select id="insurance" name="insurance">
				<option value="0" <?php if (get_option('linksynceparcel_insurance') == 0){ echo 'selected="selected"'; }?>>No</option>
				<option value="1" <?php if (get_option('linksynceparcel_insurance') == 1){ echo 'selected="selected"'; }?>>Yes</option>
			</select>
		</td>
	  </tr>
	  <?php
		$order_value_insurance = '';
		$s_insurance = 'hide-tr';
		if (get_option('linksynceparcel_insurance') == 1){
			if(get_option('order_value_insurance') == 1) {
				$order_value_insurance = 'checked="checked"';
			}
			$s_insurance = 'show-tr';
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
		if(empty($order_value_insurance) && get_option('linksynceparcel_insurance') == 1){
			$d_insurance = 'show-tr';
		}
	?>
	  <tr class="default_insurance_value <?php echo $d_insurance; ?>">
			<td width="20%" valign="top"><?php _e('Insurance value','linksynceparcel'); ?></td>
			<td align="left" colspan="2">
			   <input type="text" size="40" id="default_insurance_value" name="insurance_value" value="<?php echo get_option('linksynceparcel_default_insurance_value')?>">
		</td>
	  </tr>
	  <tr>
        <td>Export Declaration Number</td>
        <td>
			<input type="text" id="export_declaration_number" name="export_declaration_number" value="" />
		</td>
      </tr>
	  <tr>
        <td>Order value as Declared Value</td>
        <td>
			<?php
				$declared_checked = '';
				if (get_option('linksynceparcel_declared_value') == 1){
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
			$declared_text = get_option('linksynceparcel_declared_value_text');
			$declared_text_option = 'show-tr';
		}
	?>
	  <tr class="declared_value_text_field <?php echo $declared_text_option; ?>">
        <td></td>
        <td>
			<input type="number" id="declared_value_text" name="declared_value_text" value="<?php echo $declared_text; ?>">
		</td>
      </tr>
	  <tr>
        <td>Has Commercial Value</td>
        <td>
		<?php
			$commercial_checked = '';
			if (get_option('linksynceparcel_has_commercial_value') == 1){
				$commercial_checked = 'checked="checked"';
			}
		?>
			<input type="checkbox" id="has_commercial_value" name="has_commercial_value" value="1" <?php echo $commercial_checked; ?>>
		</td>
      </tr>
	  <tr>
		<td>Product Classification</td>
		<td>
			<?php
				$product_classification_disable = '';
				$product_classification_value = get_option('linksynceparcel_product_classification');
				if($commercial_checked != '') {
					$product_classification_disable = 'disabled="true"';
					$product_classification_value = '991';
				}
			?>
			<select id="product_classification" name="product_classification" <?php echo $product_classification_disable; ?>>
				<option value="991" <?php if ($product_classification_value == '991'){ echo 'selected="selected"'; }?>>Other</option>
				<option value="32" <?php if ($product_classification_value == '32'){ echo 'selected="selected"'; }?>>Commercial</option>
				<option value="31" <?php if ($product_classification_value == '31'){ echo 'selected="selected"'; }?>>Gift</option>
				<option value="91" <?php if ($product_classification_value == '91'){ echo 'selected="selected"'; }?>>Document</option>
			</select>
		</td>
	  </tr>
	  <?php
		$product_classification_text = '';
		$product_classification_option = 'hide-tr';
		if ($product_classification_value == '991'){
			$product_classification_text = get_option('linksynceparcel_product_classification_text');
			$product_classification_option = 'show-tr';
		}
	?>
	<tr id="product_classification_text" class="<?php echo $product_classification_option; ?>">
		<td>Classification Explanation</td>
		<td>
			<input type="text" class="product_classification_text" name="product_classification_text" value="<?php echo $product_classification_text; ?>">
		</td>
	</tr>
	<tr>
		<td>Use Country of Origin and HS Tariff defaults for this consignment</td>
		<td>
			<input type="checkbox" id="use_default_country_hstariff" value="1" checked>
		</td>
	</tr>
	<tr>
		<td>Country of Origin</td>
		<td>
			<select data-default="<?php echo get_option('linksynceparcel_country_origin'); ?>" id="country_origin" name="country_origin">
				<option value="" <?php if (get_option('linksynceparcel_country_origin') == ''){ echo 'selected="selected"'; }?>>
					<?php _e('Please select','linksynceparcel'); ?>
				</option>
				<?php foreach($countries as $code => $name) {?>
				<option value="<?php echo $code; ?>" <?php if (get_option('linksynceparcel_country_origin') == $code){ echo 'selected="selected"'; }?>>
					<?php echo $name; ?>
				</option>
				<?php } ?>
			</select>
		</td>
	</tr>
	 <tr>
        <td>HS Tariff</td>
        <td>
			<input type="number" data-default="<?php echo get_option('linksynceparcel_hs_tariff'); ?>" id="hs_tariff" name="hs_tariff" value="<?php echo get_option('linksynceparcel_hs_tariff'); ?>" min="0"><br/>
			<span class="comment"><a target="_blank" href="http://www.foreign-trade.com/reference/hscode.htm"><?php _e("Click here for HS Tariff list",'linksynceparcel'); ?></a></span>
		</td>
      </tr>
	  <tr>
        <td>Contents</td>
        <td>
			<input type="text" name="default_contents" value="<?php echo get_option('linksynceparcel_default_contents'); ?>">
		</td>
      </tr>
      <tr>
        <td>Transit cover required?</td>
        <td><select id="transit_cover_required" name="transit_cover_required" style="width:140px">>
            <option value="1" <?php echo (get_option('linksynceparcel_insurance')==1?'selected':'')?>>Yes</option>
            <option value="0" <?php echo (get_option('linksynceparcel_insurance')!=1?'selected':'')?>>No</option>
        </select></td>
      </tr>
      <tr>
        <td>Transit cover Amount</td>
        <td><input id="transit_cover_amount" type="text" size="14" class="positive-number" label="Transit cover amount" name="transit_cover_amount" value="<?php echo get_option('linksynceparcel_default_insurance_value')?>" /></td>
      </tr>
      <tr>
        <td>Shipment contains dangerous goods?</td>
        <td><select id="contains_dangerous_goods" name="contains_dangerous_goods" style="width:140px">>
            <option value="1">Yes</option>
            <option value="0" selected>No</option>
        </select></td>
      </tr>
      <tr>
        <td>Print return labels?</td>
        <td><select id="print_return_labels" name="print_return_labels" style="width:140px">>
            <option value="1" <?php echo (get_option('linksynceparcel_print_return_labels')==1?'selected':'')?>>Yes</option>
            <option value="0" <?php echo (get_option('linksynceparcel_print_return_labels')!=1?'selected':'')?>>No</option>
        </select></td>
      </tr>
      <tr>
        <td>Notify Customers?</td>
        <td><select id="notify_customers" name="notify_customers" style="width:140px">>
            <option value="1" <?php echo (get_option('linksynceparcel_notify_customers')==1?'selected':'')?>>Yes</option>
            <option value="0" <?php echo (get_option('linksynceparcel_notify_customers')!=1?'selected':'')?>>No</option>
        </select></td>
      </tr>
    </table>
</div>
</form>
</div>

<?php endif; ?>
