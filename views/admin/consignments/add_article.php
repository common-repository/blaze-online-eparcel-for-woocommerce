<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
$order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
$use_order_weight = (int)get_option('linksynceparcel_use_order_weight');
$use_dimension = (int)get_option('linksynceparcel_use_dimension');
$weight = 0;
if($use_order_weight == 1)
{
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
}

$selected = false;
$selectedWeight = 0;
if($weight <= $weightPerArticle)
{
	$upCheck = $weightPerArticle - $weight;
	if(LinksynceparcelHelper::presetMatch($presets,$weight))
	{
		$selectedWeight = $weight;
	}
	else
	{
		for($i=.01;$i<=$upCheck;$i = $i + 0.01)
		{
			$newUpWeight = $weight + $i;
			if(LinksynceparcelHelper::presetMatch($presets,$newUpWeight))
			{
				$selectedWeight = ''.$newUpWeight.'';
				break;
			}
		}
	}
}
?>

<div class="entry-edit wp-core-ui" id="article-form">
    <h3>Add an Article for Consignment #<?php echo $consignment->consignment_number?></h3>
</div>

<div class="entry-edit wp-core-ui" id="eparcel_sales_order_view">
    <form name="edit_form" id="edit_form" method="post" action="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=add-article&action=save&order_id='.$order_id.'&consignment_number='.$consignment->consignment_number); ?>">
        <?php wp_nonce_field( 'add_article_action', 'add_article_nonce_field' ); ?>
    	<input id="number_of_articles" name="number_of_articles" size="4" value="1" type="hidden"/>
    	<?php if($use_dimension == 1): ?>
    	<div class="box" id="presets">
        Article Type&nbsp;&nbsp; 
        <select id="articles_type" name="articles_type" class="required-entry2" style="padding:3px" >
            <?php
            foreach($presets as $preset)
            {
                ?>
                <option value="<?php echo $preset->name.'<=>'.$preset->weight.'<=>'.$preset->height.'<=>'.$preset->length.'<=>'.$preset->width?>"
                		<?php 
							if($preset->weight == $selectedWeight && !$selected)
							{
								echo 'selected="selected"'; 
								$selected = true;
							}
							?>
                >
                    <?php echo $preset->name. ' ('.$preset->weight.'kg - '.$preset->height.'x'.$preset->length.'x'.$preset->width.')'?>
                </option>
                <?php
            }
            ?>
            <option value="Custom" <?php echo ($weight > $weightPerArticle) ? 'selected="selected"' : ''?>>Custom</option>
        </select>
        &nbsp;&nbsp;&nbsp;&nbsp;
    	<input type="submit" name="createConsignment" value="Add Article" onclick="return submitForm2()" class="button-primary button create-consignment1 scalable save submit-button <?php if($order_status == 'completed'){ echo 'disabled';}?>" <?php if($order_status == 'completed'){ echo 'disabled="disabled"';}?>/>
        &nbsp;&nbsp;
        <button onclick="setLocation('<?php echo admin_url('post.php?post='.$order_id.'&action=edit')?>')" class="scalable back button cancel-button" type="button" >
        	<span><span><span>Cancel</span></span></span>
    	</button>
    
		</div>
        <?php else: ?>
		<input type="hidden" id="articles_type" name="articles_type" value="Custom"/>
    	<?php endif; ?>

    <div class="box custom_articles_template" style="display:none">
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
            <?php if($use_order_weight == 1): ?>
            <input size="10" type="text" style="text-align:center" id="article_weight" name="article[weight]" class="required-entry positive-number  maximum-value" label="Weight" value="<?php echo ($weight > $weightPerArticle) ? $weightPerArticle : $weight?>"/>
            <?php else: ?>
            <input type="text" size="10" style="text-align:center" id="article_weight" name="article[weight]" class="required-entry positive-number" label="Weight" value="<?php echo get_option('linksynceparcel_default_article_weight')?>"/>
            <?php endif; ?>
        </span>
        <?php if($use_dimension == 1): ?>
        <span class="field-row1">
            <label class="normal" for="article_height">
            Height (cm):
            </label>
            <input size="10" type="text" style="text-align:center" id="article_height" class="positive-number" label="Height" name="article[height]"  value="<?php echo get_option('linksynceparcel_default_article_height')?>"/>
        </span>
        <span class="field-row1">
            <label class="normal" for="article_width">
             Width (cm):
            </label>
            <input size="10" type="text" style="text-align:center" id="article_width" class="positive-number" label="Width" name="article[width]" value="<?php echo get_option('linksynceparcel_default_article_width')?>"/>
        </span>
        <span class="field-row1">
            <label class="normal" for="article_length">
            Length (cm):
            </label>
            <input size="10" type="text" style="text-align:center" id="article_length" class="positive-number" label="Length" name="article[length]" value="<?php echo get_option('linksynceparcel_default_article_length')?>"/>
        </span>
        <?php else: ?>
            <input type="hidden" name="article[height]" value="0"/>
            <input type="hidden" name="article[width]" value="0"/>
            <input type="hidden" name="article[length]" value="0"/>
        <?php endif; ?>
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
    <input type="submit" name="createConsignment"  value="Add Article" onclick="return submitForm()" class="button-primary button scalable save submit-button <?php if($order_status == 'completed'){ echo 'disabled';}?>" <?php if($order_status == 'completed'){ echo 'disabled="disabled"';}?>/>
    &nbsp;&nbsp;
    <button onclick="setLocation('<?php echo admin_url('post.php?post='.$order_id.'&action=edit')?>')" class="scalable back button" type="button" >
        <span><span><span>Cancel</span></span></span>
    </button>
    
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
 
<div class="box consignment-fields" style="display:none">
    <h3>Consignment Fields</h3>
    <table width="100%" border="0" cellspacing="6" cellpadding="6" class="tablecustom">
      <tr>
        <td width="30%">Partial Delivery allowed?</td>
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
        <td>Cash to collect</td>
        <td><input id="cash_to_collect" name="cash_to_collect" type="text" value="<?php echo $consignment->cash_to_collect?>" /></td>
      </tr>
      <?php endif; ?>
      
      <tr>
        <td>Delivery signature required?</td>
        <td><select id="delivery_signature_allowed" name="delivery_signature_allowed" style="width:140px">>
            <option value="1" <?php echo ($consignment->delivery_signature_allowed==1?'selected':'')?>>Yes</option>
            <option value="0" <?php echo ($consignment->delivery_signature_allowed!=1?'selected':'')?>>No</option>
        </select></td>
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
            <option value="1" <?php echo ($consignment->contains_dangerous_goods==1?'selected':'')?>>Yes</option>
            <option value="0" <?php echo ($consignment->contains_dangerous_goods!=1?'selected':'')?>>No</option>
        </select></td>
      </tr>
      <tr>
        <td>Print return labels?</td>
        <td><select id="print_return_labels" name="print_return_labels" style="width:140px">>
            <option value="1" <?php echo ($consignment->print_return_labels==1?'selected':'')?>>Yes</option>
            <option value="0" <?php echo ($consignment->print_return_labels!=1?'selected':'')?>>No</option>
        </select></td>
      </tr>
      <tr>
        <td>Notify Customers?</td>
        <td><select id="notify_customers" name="notify_customers" style="width:140px">>
            <option value="1" <?php echo ($consignment->notify_customers==1?'selected':'')?>>Yes</option>
            <option value="0" <?php echo ($consignment->notify_customers!=1?'selected':'')?>>No</option>
        </select></td>
      </tr>
    </table>
</div>
</form>
</div>