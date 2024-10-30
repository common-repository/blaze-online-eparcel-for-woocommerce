<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
$order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
$weight = 0;
$default_article_weight = get_option('linksynceparcel_default_article_weight');
if($default_article_weight)
{
	$weight = $default_article_weight;
}
?>
<div id="loading" style="display:none;">
	<div id="img-loader">
		<img src="<?php echo linksynceparcel_URL?>assets/images/load.gif" alt="Loading" />
	</div>
</div>
<?php if(!LinksynceparcelValidator::validateConsignmentLimit()): ?>
<div class="entry-edit wp-core-ui single-create-consignment" id="eparcel_sales_order_view">
	<!-- Get consignment cost -->
	<h3>Consignment Cost: <strong>$<?php echo $consignment_cost; ?></strong></h3>
	<!-- /end -->

	    <input type="hidden" id="createConsignmentHidden" name="createConsignmentHidden" value="0"/>
    	<div class="box_ls" id="presets">
		<?php if($order_status != 'completed'){?>
        Articles&nbsp;&nbsp; <input type="text" id="number_of_articles" name="number_of_articles" size="4" value="1" class="validate-number" style="text-align:center; padding:3px" <?php echo ($shipping_country != 'AU')?"disabled='disabled'":'';?>/>

        <input id="articles_type" name="articles_type" type="hidden" value="Custom"/>
		<?php
            }
         ?>
    	<input id="consignment_submit2" type="submit" name="createConsignment" value="Create Consignment" class="button-primary button create-consignment1 scalable save submit-button <?php if($order_status == 'completed'){ echo 'disabled';}?>" <?php if($order_status == 'completed'){ echo 'disabled="disabled"';}?>/>
</div>

<div class="box_ls custom_articles_template" style="display:none">
    <h3 style="margin:10px 0">Article</h3>
    <span class="field-row1">
        <label class="normal" for="article_description">
         Description:<span class="required">*</span>
        </label>
        <input id="article_description" type="text" name="article[description]" class="required-entry" value="Article"/>
    </span><br /><br />
    <span class="field-row1">
        <label class="normal" for="article_weight">
         Weight (Kgs):<span class="required">*</span>
        </label>
        <input size="10" type="text" style="text-align:center" id="article_weight" name="article[weight]" class="required-entry positive-number maximum-value" label="Weight" value="<?php echo $weight?>"/>
    </span>
</div>

<div id="custom_articles" style="display:none">
    <div id="custom_articles_container">
    </div>
    <br />
    <br />
    <button onclick="backToPreset()" class="scalable back backToPreset button" type="button" title="Back">
        <span><span><span>Back to Preset</span></span></span>
    </button>
    &nbsp;&nbsp;
    <input id="consignment_submit" type="submit" name="createConsignment"  value="Create Consignment" class="button-primary button scalable save submit-button <?php if($order_status == 'completed'){ echo 'disabled';}?>" <?php if($order_status == 'completed'){ echo 'disabled="disabled"';}?>/>
</div>
<?php if($order_status != 'completed'){?>
 <div>
    <br />
    <a href="javascript:void(0)" class="edit-consignments-defaults" style="text-decoration:none"><span style="font-size:13px; color:#F60">Edit Consignment Defaults</span></a>
    <br />
    <br />
 </div>
<?php
    }
 ?>

<div class="box_ls consignment-fields" style="display:none">
    <h3>Consignment Fields</h3>
	<input type="hidden" name="date_process" value="<?php echo base64_encode(date("Y-m-d H:i:s")); ?>"/>
    <table width="100%" border="0" cellspacing="6" cellpadding="6" class="tablecustom">
		<tr>
			<td width="30%">Delivery instructions</td>
			<td>
				<textarea name="delivery_instruction" maxlength="256" cols="40" rows="4"><?php echo $ordernotes; ?></textarea>
			</td>
		</tr>
		<?php if($shipping_country == 'AU') { ?>
      <tr>
        <td width="30%">Partial Delivery allowed?</td>
        <td>
        <?php if(LinksynceparcelHelper::isDisablePartialDeliveryMethod($order_id)): ?>
        <select id="partial_delivery_allowed" name="partial_delivery_allowed" disabled="disabled" style="width:140px">
            <option value="0">No</option>
        </select>
        <?php else: ?>
        <select id="partial_delivery_allowed" name="partial_delivery_allowed"  style="width:140px">
            <option value="1" <?php echo (get_option('linksynceparcel_partial_delivery_allowed')==1?'selected':'')?>>Yes</option>
            <option value="0" <?php echo (get_option('linksynceparcel_partial_delivery_allowed')!=1?'selected':'')?>>No</option>
        </select>
         <?php endif; ?>
        </td>
      </tr>
      <?php if(LinksynceparcelHelper::isCashToCollect($order_id)): ?>
      <tr>
        <td>Cash to collect</td>
        <td><input id="cash_to_collect" name="cash_to_collect" type="text" /></td>
      </tr>
      <?php endif; ?>
      <tr>
        <td>Delivery signature required?</td>
        <td><select id="delivery_signature_allowed" name="delivery_signature_allowed" style="width:140px">
            <option value="1" <?php echo (get_option('linksynceparcel_signature_required')==1?'selected':'')?>>Yes</option>
            <option value="0" <?php echo (get_option('linksynceparcel_signature_required')!=1?'selected':'')?>>No</option>
        </select></td>
      </tr>
	  <tr>
        <td>Safe Drop</td>
        <td><select id="safe_drop" name="safe_drop" style="width:140px">
			<option value="1" <?php echo (get_option('linksynceparcel_safe_drop') == 1?'selected="selected"':'');?>>Yes</option>
            <option value="0" <?php echo (get_option('linksynceparcel_safe_drop') != 1?'selected="selected"':'');?>>No</option>
        </select></td>
      </tr>
      <tr>
        <td>Transit cover required?</td>
        <td><select id="transit_cover_required" name="transit_cover_required" style="width:140px">
            <option value="1" <?php echo (get_option('linksynceparcel_insurance')==1?'selected':'')?>>Yes</option>
            <option value="0" <?php echo (get_option('linksynceparcel_insurance')!=1?'selected':'')?>>No</option>
        </select></td>
      </tr>
      <tr>
        <td>Transit cover Amount</td>
        <td><input id="transit_cover_amount" type="text" size="14" class="positive-number" label="Transit cover amount" name="transit_cover_amount" value="<?php echo get_option('linksynceparcel_default_insurance_value')?>" /></td>
      </tr>
	  <?php } ?>
	  <?php if($shipping_country != 'AU') : ?>
	  <tr>
		<td width="30%">Insurance</td>
		<td>
			<select id="insurance" name="insurance">
				<option value="0" <?php if (get_option('linksynceparcel_int_insurance') == 0){ echo 'selected="selected"'; }?>>No</option>
				<option value="1" <?php if (get_option('linksynceparcel_int_insurance') == 1){ echo 'selected="selected"'; }?>>Yes</option>
			</select>
		</td>
	  </tr>
	  <?php
		$s_insurance = 'hide-tr';
		if (get_option('linksynceparcel_int_insurance') == 1){
			$s_insurance = 'show-tr';
		}
		$order_value_insurance = '';
		if(get_option('order_value_insurance') == 1) {
			$order_value_insurance = 'checked="checked"';
		}
	?>
	  <tr class="order_value_insurance <?php echo $s_insurance; ?>">
			<td width="30%">Order value as Insured Value</td>
			<td>
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
			<td width="30%">Insurance value</td>
			<td>
			   <input type="text" size="40" id="default_insurance_value" name="insurance_value" value="<?php echo get_option('linksynceparcel_int_default_insurance_value')?>">
		</td>
	  </tr>
	  <tr>
        <td>Export Declaration Number</td>
        <td>
			<input type="text" id="export_declaration_number" name="export_declaration_number" value="" />
		</td>
      </tr>
	  <tr class="hide-tr">
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
	  <tr class="declared_value_text_field hide-tr">
        <td></td>
        <td>
			<input type="number" id="declared_value_text" name="declared_value_text" value="<?php echo $declared_text; ?>">
		</td>
      </tr>
	  <tr>
		<td>Declare Value</td>
		<td>
			<?php
				$order_value_declared_value = get_option('linksynceparcel_order_value_declared_value');
			?>
			<select id="order_value_declared_value" name="order_value_declared_value" <?php echo $product_classification_disable; ?>>
				<option value="0" <?php if ($order_value_declared_value == '0'){ echo 'selected="selected"'; }?>>Order Value</option>
				<option value="1" <?php if ($order_value_declared_value == '1'){ echo 'selected="selected"'; }?>>Order Value with Maximum</option>
				<option value="2" <?php if ($order_value_declared_value == '2'){ echo 'selected="selected"'; }?>>Fixed Value</option>
			</select>
		</td>
	</tr>
	<?php
		$maximum_declared_value = get_option('linksynceparcel_maximum_declared_value');
		$maximum_declared_value_class = 'hide-tr';
		if($order_value_declared_value == 1) {
			$maximum_declared_value_class = 'show-tr';
		}
	?>
	<tr id="maximum_declared_value" class="<?php echo $maximum_declared_value_class; ?>">
		<td>Maximum Declared Value</td>
		<td>
			<input type="number" class="maximum_declared_value" name="maximum_declared_value" value="<?php echo $maximum_declared_value; ?>"><br />
		</td>
	</tr>
	<?php
		$fixed_declared_value = get_option('linksynceparcel_fixed_declared_value');
		$fixed_declared_value_class = 'hide-tr';
		if($order_value_declared_value == 2) {
			$fixed_declared_value_class = 'show-tr';
		}
	?>
	<tr id="fixed_declared_value" class="<?php echo $fixed_declared_value_class; ?>">
		<td>Fixed Declared Value</td>
		<td>
			<input type="number" class="fixed_declared_value" name="fixed_declared_value" value="<?php echo $fixed_declared_value; ?>"><br />
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
	<!--
	<tr>
		<td>Use Country of Origin and HS Tariff defaults for this consignment</td>
		<td>
			<input type="checkbox" id="use_default_country_hstariff" value="1" checked>
		</td>
	</tr>
	-->
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
	  <?php endif; ?>
	<?php if($shipping_country == 'AU') { ?>
      <tr>
        <td>Shipment contains dangerous goods?</td>
        <td><select id="contains_dangerous_goods" name="contains_dangerous_goods" style="width:140px">
            <option value="1">Yes</option>
            <option value="0" selected>No</option>
        </select></td>
      </tr>
	<?php } ?>
    </table>
</div>
</div>
<?php endif; ?>
<?php $consignments = LinksynceparcelHelper::getConsignments($order_id, true);?>
<?php foreach($consignments as $consignment):?>
<?php $articles = LinksynceparcelHelper::getArticles($order_id, $consignment->consignment_number);?>
<?php
	$int_fields = LinksynceparcelHelper::getInternationFields($order_id, $consignment->consignment_number);
	$shipCountry = $consignment->delivery_country;
	if(empty($shipCountry)) {
		$shipCountry = $shipping_country;
	}
?>
<br />
<div class="entry-edit" style="border:1px solid silver; padding:10px">
	<div class="entry-edit-head" style="min-height: 30px; background-color: silver;padding-bottom: 7px;">
		<span class="icon-head head-products" style="font-weight: bold; font-size: 14px; float: left; margin-top: 10px; padding-left: 5px;width: 39%;">
		Consigment: <?php echo $consignment->consignment_number?>
		<?php if($consignment->despatched):?>
			(despatched) - <a href="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=manifests&action=list-consignments&manifest_number='.$consignment->manifest_number); ?>">manifest <?php echo $consignment->manifest_number?></a>
		<?php endif;?>
		</span>
		<span id="new-buttons-set" style="float: right; margin-right: 4px; margin-top: 5px;">
		<a class="button" target="_blank" href="http://auspost.com.au/track/track.html?id=<?php echo $consignment->consignment_number?>">Track</a>
        <?php if(!empty($consignment->label)):?>
		<?php
			$consignmentpdf = admin_url() ."?f_key=". $consignment->consignment_number ."&f_type=consignment";
		?>
        <a class="button print_label" lang="<?php echo $consignment->consignment_number?>" target="_blank" href="<?php echo $consignmentpdf ?>" type="button" title="Print Label">
             <?php echo ($consignment->is_label_printed ? 'Reprint' : 'Print');?> Label
        </a>
        <?php else: ?>
        <a class="button" target="_blank" href="<?php echo admin_url('admin.php?page=linksynceparcel&action=singleGenerateLabel&order='. $order_id .'_'. $consignment->consignment_number); ?>" title="Print Label">Create Label</a>
        <?php endif;?>
		<?php if(!$consignment->despatched):?>
			<?php if($shipCountry == 'AU') : ?>
				<?php if(count($articles) < 20):?>
				<button class="button" onclick="setLocation('<?php echo admin_url('admin.php?page=linksynceparcel&subpage=add-article&order_id='.$order_id.'&consignment_number='.$consignment->consignment_number); ?>')" type="button">Add Articles</button>
				<?php endif;?>
				<button class="button" onclick="setLocation('<?php echo admin_url('admin.php?page=linksynceparcel&order_id='.$order_id.'&subpage=edit-consignment&consignment_number='.$consignment->consignment_number); ?>')" type="button">Edit Consignment</button>
			<?php endif;?>
			<button class="button" onclick="setLocationConfirmDialog('<?php echo admin_url('admin.php?page=linksynceparcel&order_id='.$order_id.'&action=delete_consignment&consignment_number='.$consignment->consignment_number); ?>')" type="button">Delete Consignment</button>
		<?php endif;?>
        </span>
		<div style="clear:both;"></div>
	</div>
	<div class="box_ls">
		<table width="100%" border="0" cellspacing="6" cellpadding="6" class="tablecustom">
		  <tr>
			<td>Delivery instructions</td>
			<td><?php echo $consignment->delivery_instruction;?></td>
		  </tr>
		  <?php if($shipCountry == 'AU') : ?>
		  <tr>
			<td width="40%">Partial Delivery allowed?</td>
			<td><?php echo ($consignment->partial_delivery_allowed==1?'Yes':'No')?></td>
		  </tr>
		  <?php if(LinksynceparcelHelper::isCashToCollect($order_id)): ?>
		  <tr>
			<td>Cash to collect</td>
			<td><?php echo $consignment->cash_to_collect?></td>
		  </tr>
		  <?php endif; ?>
		  <tr>
			<td>Delivery signature required?</td>
			<td><?php echo ($consignment->delivery_signature_allowed==1?'Yes':'No')?></td>
		  </tr>
		  <tr>
			<td width="40%">Safe Drop</td>
			<td><?php echo ($consignment->safe_drop==1?'Yes':'No')?></td>
		  </tr>
		  <?php endif; ?>
		  <?php if($shipCountry != 'AU') : ?>
		  <tr>
			<td>Insurance</td>
			<td><?php echo ($int_fields->insurance==1?'Yes':'No')?></td>
		  </tr>
		  <tr>
			<td>Insurance Value</td>
			<td><?php echo $int_fields->insurance_value; ?></td>
		  </tr>
		  <tr>
			<td>Export Declaration Number</td>
			<td><?php echo $int_fields->export_declaration_number; ?></td>
		  </tr>
		  <!--<tr>
			<td>Declared Value</td>
			<td><?php //echo $int_fields->declared_value_text; ?></td>
		  </tr>-->
		  <tr>
			<td>Has Commercial Value</td>
			<td><?php echo ($int_fields->has_commercial_value==1?'Yes':'No'); ?></td>
		  </tr>
		  <tr>
			<td>Product Classification</td>
			<td>
			<?php
				switch($int_fields->product_classification) {
					case '32':
						$classification = 'Commercial';
						break;

					case '31':
						$classification = 'Gift';
						break;

					case '91':
						$classification = 'Document';
						break;

					default:
						$classification = 'Other';
						break;
				}
				echo $classification;
			?>
			</td>
		  </tr>
		  <tr>
			<td>Classification Explanation</td>
			<td><?php echo $int_fields->product_classification_text; ?></td>
		  </tr>
		   <tr>
			<td>Country Origin</td>
			<td><?php echo $int_fields->country_origin; ?></td>
		  </tr>
		  <tr>
			<td>HS Tariff</td>
			<td><?php echo $int_fields->hs_tariff; ?></td>
		  </tr>
		  <tr>
			<td>Contents</td>
			<td><?php echo $int_fields->default_contents; ?></td>
		  </tr>
		  <?php endif; ?>
		  <?php if($shipCountry == 'AU') : ?>
		  <tr>
			<td>Shipment contains dangerous goods?</td>
			<td><?php echo ($consignment->contains_dangerous_goods==1?'Yes':'No')?></td>
		  </tr>
		  <?php endif; ?>
		</table>
		<br />
		<table cellspacing="1" class="data order-tables" width="100%" style="background-color: rgb(238, 238, 238); padding: 1px;">
		<thead style="font-size: 11px; text-align: left;">
			<tr class="headings">
				<th height="25px" bgcolor="#EEEEEE">Article Number</th>
				<th bgcolor="#EEEEEE">Description</th>
				<th bgcolor="#EEEEEE" class="a-center" style="text-align:center">Weight (Kgs)</th>
				<th bgcolor="#EEEEEE" class="a-center" style="text-align:center">Width (cm)</th>
				<th bgcolor="#EEEEEE" class="a-center" style="text-align:center">Height (cm)</th>
				<th bgcolor="#EEEEEE" class="a-center" style="text-align:center">Length (cm)</th>
				<th bgcolor="#EEEEEE" class="a-center" style="text-align:center">Transit Cover</th>
				<th bgcolor="#EEEEEE" class="a-right" style="text-align:center">Transit Value</th>
				<th bgcolor="#EEEEEE" class="a-right" style="text-align:center">Action</th>
			</tr>
		</thead>
		<tbody class="even" style="font-size: 11px;">
		<?php foreach($articles as $article):?>
			<tr class="border">
				<td height="32px" bgcolor="#FBFBFB" class="a-left"><?php echo $article->article_number?></td>
			  <td bgcolor="#FBFBFB" class="a-left"><?php echo stripslashes($article->article_description)?></td>
			  <td bgcolor="#FBFBFB" class="a-center" style="text-align:center"><?php echo $article->actual_weight?></td>
			  <td bgcolor="#FBFBFB" class="a-center" style="text-align:center"><?php echo $article->width?></td>
			  <td bgcolor="#FBFBFB" class="a-center" style="text-align:center"><?php echo $article->height?></td>
			  <td bgcolor="#FBFBFB" class="a-center" style="text-align:center"><?php echo $article->length?></td>
			  <td bgcolor="#FBFBFB" class="a-center" style="text-align:center"><?php echo $article->is_transit_cover_required?></td>
			  <td bgcolor="#FBFBFB" class="a-right" style="text-align:center"><?php echo (is_numeric($article->transit_cover_amount) ? number_format($article->transit_cover_amount,2) : 'NA') ?></td>
				<td bgcolor="#FBFBFB"  style="text-align:center">
				<?php if(!$consignment->despatched):?>
			    <button style="font-size: 11px;" class="button" onclick="setLocation('<?php echo admin_url('admin.php?page=linksynceparcel&order_id='.$order_id.'&subpage=edit-article&consignment_number='.$consignment->consignment_number.'&article_number='.$article->article_number); ?>')" type="button">Edit</button>
					<button class="button" onclick="setLocationConfirmDialog('<?php echo admin_url('admin.php?page=linksynceparcel&order_id='.$order_id.'&action=delete_article&consignment_number='.$consignment->consignment_number.'&article_number='.$article->article_number); ?>')" type="button" style="font-size: 11px;">Delete</button>
				<?php else:?>
					N/A
				<?php endif;?>
				</td>
			</tr>
		<?php endforeach;?>
		   </tbody>
		</table>
  </div>
	<div class="clear"></div>
</div>
<?php
 endforeach;

$validate_limit = "";
if(LinksynceparcelValidator::validateConsignmentLimit()) {
	$validate_limit = "validate_limit";
}
?>
<div id="dialog" class="<?php echo $validate_limit; ?>" title="Consignment Limit Reached" style="display:none;">
    <?php echo LinksyncUserHelper::generateCappingMessage(true); ?>
</div>