<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
$order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
$use_order_weight = (int)get_option('linksynceparcel_use_order_weight');
$use_dimension = (int)get_option('linksynceparcel_use_dimension');
$weight = LinksynceparcelHelper::getOrderWeight($order);
if($weight == 0)
{
	$default_article_weight = get_option('linksynceparcel_default_article_weight');
	if($default_article_weight)
	{
		$weight = $default_article_weight;
	}
}
$weightPerArticle = LinksynceparcelHelper::getAllowedWeightPerArticle();
$exactArticles = (int)($weight / $weightPerArticle);
$totalArticles = $exactArticles;
$reminderWeight = fmod ($weight, $weightPerArticle);
if($reminderWeight > 0)
{
	$totalArticles++;
}
?>
<form name="edit_form" id="edit_form" method="post" action="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=edit-consignment&action=save&order_id='.$order_id.'&consignment_number='.$consignment->consignment_number); ?>" class="consignment-form">
<?php wp_nonce_field( 'edit_consignment_action', 'edit_consignment_nonce_field' ); ?>
<div class="entry-edit wp-core-ui">
    <h3>Edit Consignment #<?php echo $consignment->consignment_number?></h3>
    <?php $articles = LinksynceparcelHelper::getArticles($order_id, $consignment->consignment_number);?>
    <?php $i=0;?>
    <input id="number_of_articles2" type="hidden" name="number_of_articles" value="<?php echo count($articles)?>" />
    <input id="articles_type" type="hidden" name="articles_type" value="Custom" />
    <input id="use_dimension" type="hidden" value="<?php echo $use_dimension; ?>" />
    <input id="overallweight" type="hidden" value="<?php echo $weight; ?>" />
    <input id="use_order_weight" type="hidden" value="<?php echo $use_order_weight; ?>" />
    
    <?php if( ($use_order_weight == 1) && ($weight > $weightPerArticle) ):?>
        <h3>Total Order Weight: <strong><?php echo $weight?></strong></h3>
    <?php endif;?>
    
    <?php foreach($articles as $article):?>
    <div id="custom_articles">
        <h4 style="margin:10px 0">Article <?php echo $i+1?></h4>
        <span class="field-row1">
            <label class="normal" for="article_description">
             Description:<span class="required">*</span>
            </label>
            <input id="article_description<?php echo $i+1?>" type="text" name="article<?php echo $i+1?>[description]" class="required-entry" value="<?php echo $article->article_description?>"/>
        </span><br /><br />
        <span class="field-row1"> 
            <label class="normal" for="article_weight">
             Weight (Kgs):<span class="required">*</span>
            </label>
            <input size="10" type="text" style="text-align:center" id="article_weight<?php echo $i+1?>" name="article<?php echo $i+1?>[weight]" class="required-entry positive-number article_weight maximum-value" label="Weight" value="<?php echo $article->actual_weight?>"/>
        </span>
        <?php if($use_dimension == 1): ?>
        <span class="field-row1">
            <label class="normal" for="article_height">
            Height (cm):
            </label>
            <input size="10" type="text" style="text-align:center" id="article_height<?php echo $i+1?>" class="positive-number" label="Height" name="article<?php echo $i+1?>[height]"  value="<?php echo $article->height?>"/>
        </span>
        <span class="field-row1">
            <label class="normal" for="article_width">
             Width (cm):
            </label>
            <input size="10" type="text" style="text-align:center" id="article_width<?php echo $i+1?>" class="positive-number" label="Width" name="article<?php echo $i+1?>[width]" value="<?php echo $article->width?>"/>
        </span>
        <span class="field-row1">
            <label class="normal" for="article_length">
            Length (cm):
            </label>
            <input size="10" type="text" style="text-align:center" id="article_length<?php echo $i+1?>" class="positive-number" label="Length" name="article<?php echo $i+1?>[length]" value="<?php echo $article->length?>"/>
        </span>
        <?php else: ?>
            <input type="hidden" name="article<?php echo $i+1?>[height]" value="5"/>
            <input type="hidden" name="article<?php echo $i+1?>[width]" value="5"/>
            <input type="hidden" name="article<?php echo $i+1?>[length]" value="5"/>
            <input type="hidden" name="article<?php echo $i+1?>[article_number]" value="<?php echo $article->article_number?>"/>
        <?php endif; ?>
    </div>
		<?php $i++; ?>
	<?php endforeach;?>
    <div class="box consignment-fields">
      <h4 style="margin-bottom:6px">Consignment Fields</h4>
        <table width="100%" border="0" cellspacing="6" cellpadding="6" class="tablecustom">
		  <tr>
			<td width="30%">Delivery instructions</td>
			<td>
            <?php 
                $customer_note = method_exists($order, 'get_customer_note') ? $order->get_customer_note() : $order->customer_message;
            ?>
				<textarea name="delivery_instruction" maxlength="256" cols="40" rows="4"><?php echo !empty($consignment->delivery_instruction)?$consignment->delivery_instruction:$customer_note; ?></textarea>
			</td>
		  </tr>
		  <?php if($shipCountry == 'AU') : ?>
          <tr>
            <td height="35">Partial Delivery allowed?</td>
            <td>
            <?php if(LinksynceparcelHelper::isDisablePartialDeliveryMethod($order_id)): ?>
            <select id="partial_delivery_allowed" name="partial_delivery_allowed" disabled="disabled" style="width:140px">>
                <option value="0">No</option>
            </select>
            <?php else: ?>
            <select id="partial_delivery_allowed" name="partial_delivery_allowed"  style="width:140px">
                <option value="1" <?php echo ($consignment->partial_delivery_allowed==1?'selected':'')?>>Yes</option>
                <option value="0" <?php echo ($consignment->partial_delivery_allowed!=1?'selected':'')?>>No</option>
            </select>
             <?php endif; ?>
            </td>
          </tr>
          
          <?php if(LinksynceparcelHelper::isCashToCollect($order_id)): ?>
          <tr>
            <td height="35">Cash to collect</td>
            <td><input id="cash_to_collect" name="cash_to_collect" type="text" value="<?php echo $consignment->cash_to_collect?>" /></td>
          </tr>
          <?php endif; ?>
          
          <tr>
            <td width="40%" height="35">Delivery signature required?</td>
            <td><select id="delivery_signature_allowed" name="delivery_signature_allowed" style="width:140px">>
                <option value="1" <?php echo ($consignment->delivery_signature_allowed==1?'selected':'')?>>Yes</option>
                <option value="0" <?php echo ($consignment->delivery_signature_allowed!=1?'selected':'')?>>No</option>
            </select></td>
          </tr>
		  <tr>
			<td width="40%" height="35">Safe Drop</td>
			<td><select id="safe_drop" name="safe_drop" style="width:140px">
				<option value="1" <?php echo ($consignment->safe_drop == 1?'selected="selected"':'');?>>Yes</option>
				<option value="0" <?php echo ($consignment->safe_drop != 1?'selected="selected"':'');?>>No</option>
			</select></td>
		  </tr>
          <?php endif; ?>
		  <?php if($shipCountry != 'AU') : ?>
		  <?php
			$opt_drop = 'hide-tr';
			$opt_drop_val = 0;
			if($consignment->delivery_signature_allowed == 1) {
				$opt_drop = 'show-tr';
				$opt_drop_val = $int_fields->safe_drop;
				if(empty($opt_drop_val)) {
					$opt_drop_val = get_option('linksynceparcel_safe_drop');
				}
			}
		  ?>
		  <tr class="safe-drop-row <?php echo $opt_drop; ?>">
			<td>Safe Drop</td>
			<td>
				<select id="safe_drop" name="safe_drop" style="width:140px">
					<option value="1" <?php echo ($opt_drop_val==1?'selected':'')?>>Yes</option>
					<option value="0" <?php echo ($opt_drop_val!=1?'selected':'')?>>No</option>
				</select>
			</td>
		  </tr>
		  <tr>
			<td>Export Declaration Number</td>
			<td>
				<input type="text" id="export_declaration_number" name="export_declaration_number" value="<?php echo $int_fields->export_declaration_number; ?>" />
			</td>
		  </tr>
		  <tr>
			<td>Order value as Declared Value</td>
			<td>
				<?php 
					$declared_checked = '';
					$declared_value = $int_fields->declared_value;
					if(empty($declared_value)) {
						$declared_value = get_option('linksynceparcel_declared_value');
					}
					if ($declared_value == 1){ 
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
				$declared_text = $int_fields->declared_value_text;
				if(empty($declared_text)) {
					$declared_text = get_option('linksynceparcel_declared_value_text');
				}
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
				$has_commercial_value = $int_fields->has_commercial_value;
				if(empty($has_commercial_value)) {
					$has_commercial_value = get_option('linksynceparcel_has_commercial_value');
				}
				if ($has_commercial_value == 1){ 
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
					$product_classification_value = $int_fields->product_classification;
					if(empty($product_classification_value)) {
						$product_classification_value = get_option('linksynceparcel_product_classification');
					}
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
				$product_classification_text = $int_fields->product_classification_text;
				if(empty($product_classification_text)){
					$product_classification_text = get_option('linksynceparcel_product_classification_text');
				}
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
				<input type="checkbox" id="use_default_country_hstariff" value="1" <?php if(empty($int_fields)){ echo 'checked'; }?>>
			</td>
		</tr>
		<tr>
			<td>Country of Origin</td>
			<td>
				<?php 
					$country_origin = $int_fields->country_origin;
					$disabled_c = '';
					if(empty($int_fields->country_origin)) {
						$country_origin = get_option('linksynceparcel_country_origin');
						$disabled_c = 'disabled';
					}
				?>
				<select data-default="<?php echo get_option('linksynceparcel_country_origin'); ?>" id="country_origin" name="country_origin" <?php echo $disabled_c; ?>>
					<option value="" <?php if ($country_origin == ''){ echo 'selected="selected"'; }?>>
						<?php _e('Please select','linksynceparcel'); ?>
					</option>
					<?php foreach($countries as $code => $name) {?>
					<option value="<?php echo $code; ?>" <?php if ($country_origin == $code){ echo 'selected="selected"'; }?>>
						<?php echo $name; ?>
					</option>
					<?php } ?>
				</select>
			</td>
		</tr>
		 <tr>
			<td>HS Tariff</td>
			<td>
				<?php 
					$hs_tariff = $int_fields->hs_tariff;
					$disabled_h = '';
					if(empty($hs_tariff)) {
						$hs_tariff = get_option('linksynceparcel_hs_tariff');
						$disabled_h = 'disabled';
					}
				?>
				<input type="number" data-default="<?php echo get_option('linksynceparcel_hs_tariff'); ?>" id="hs_tariff" name="hs_tariff" value="<?php echo $hs_tariff; ?>" min="0" <?php echo $disabled_h; ?>><br/>
				<span class="comment"><a target="_blank" href="http://www.foreign-trade.com/reference/hscode.htm"><?php _e("Click here for HS Tariff list",'linksynceparcel'); ?></a></span>
			</td>
		  </tr>
		  <tr>
			<td>Contents</td>
			<td>
				<?php 
					$default_contents = $int_fields->default_contents;
					if(empty($default_contents)) {
						$default_contents = get_option('linksynceparcel_default_contents');
					}
				?>
				<input type="text" name="default_contents" value="<?php echo $default_contents; ?>">
			</td>
		  </tr>
		  <?php endif; ?>
		  <?php if($shipCountry == 'AU') : ?>
          <tr>
            <td height="35">Transit cover required?</td>
            <td><select id="transit_cover_required" name="transit_cover_required" style="width:140px">>
                <option value="1" <?php echo (get_option('linksynceparcel_insurance')==1?'selected':'')?>>Yes</option>
                <option value="0" <?php echo (get_option('linksynceparcel_insurance')!=1?'selected':'')?>>No</option>
            </select></td>
          </tr>
          <tr>
            <td height="35">Transit cover Amount</td>
            <td><input id="transit_cover_amount" type="text" size="14" class="positive-number" label="Transit cover amount" name="transit_cover_amount" value="<?php echo get_option('linksynceparcel_default_insurance_value')?>" /></td>
          </tr>
		  <?php endif; ?>
          <tr>
            <td height="35">Shipment contains dangerous goods?</td>
            <td><select id="contains_dangerous_goods" name="contains_dangerous_goods" style="width:140px">>
                <option value="1" <?php echo ($consignment->contains_dangerous_goods==1?'selected':'')?>>Yes</option>
                <option value="0" <?php echo ($consignment->contains_dangerous_goods!=1?'selected':'')?>>No</option>
            </select></td>
          </tr>
		  <?php if($shipCountry == 'AU') : ?>
          <tr>
            <td height="35">Print return labels?</td>
            <td><select id="print_return_labels" name="print_return_labels" style="width:140px">>
                <option value="1" <?php echo ($consignment->print_return_labels==1?'selected':'')?>>Yes</option>
                <option value="0" <?php echo ($consignment->print_return_labels!=1?'selected':'')?>>No</option>
            </select></td>
          </tr>
          <tr>
            <td height="35">Notify Customers?</td>
            <td><select id="notify_customers" name="notify_customers" style="width:140px">>
                <option value="1" <?php echo ($consignment->notify_customers==1?'selected':'')?>>Yes</option>
                <option value="0" <?php echo ($consignment->notify_customers!=1?'selected':'')?>>No</option>
            </select></td>
          </tr>
		  <?php endif; ?>
        </table>
    </div>
    
	<div style="margin-top:15px">
        <input type="submit" name="updateConsignment"  value="Update Consignment" onclick="return submitEditConsigmentForm()" class="button-primary button scalable save submit-button"/>
            &nbsp;&nbsp;
        <button onclick="setLocation('<?php echo admin_url('post.php?post='.$order_id.'&action=edit')?>')" class="scalable back button" type="button" >
            <span><span><span>Cancel</span></span></span>
        </button>
    </div>
</div>
</form>