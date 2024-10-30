<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
$order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
$use_order_weight = (int)get_option('linksynceparcel_use_order_weight');
$use_dimension = (int)get_option('linksynceparcel_use_dimension');
if($use_order_weight == 1)
{
	$weightPerArticle = LinksynceparcelHelper::getAllowedWeightPerArticle();
}
?>


<form name="edit_form" id="edit_form" method="post" action="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=edit-article&action=save&order_id='.$order_id.'&consignment_number='.$consignment->consignment_number.'&article_number='.$articleNumber); ?>">
<?php wp_nonce_field( 'edit_article_action', 'edit_article_nonce_field' ); ?>
<div class="entry-edit wp-core-ui">
    <h3>Edit Article #<?php echo $articleNumber?> for Consignment #<?php echo $consignment->consignment_number?></h3>
    
    <div id="custom_articles">
        <h4 style="margin:10px 0">Article</h4>
        <span class="field-row1">
            <label class="normal" for="article_description">
             Description:<span class="required">*</span>
            </label>
            <input id="article_description" type="text" name="article[description]" class="required-entry" value="<?php echo $article->article_description?>"/>
        </span><br /><br />
        <span class="field-row1"> 
            <label class="normal" for="article_weight">
             Weight (Kgs):<span class="required">*</span>
            </label>
            <input size="10" type="text" style="text-align:center" id="article_weight" name="article[weight]" class="required-entry positive-number maximum-value" label="Weight" value="<?php echo $article->actual_weight?>"/>
        </span>
        <?php if($use_dimension == 1): ?>
        <span class="field-row1">
            <label class="normal" for="article_height">
            Height (cm):
            </label>
            <input size="10" type="text" style="text-align:center" id="article_height" class="positive-number" label="Height" name="article[height]"  value="<?php echo $article->height?>"/>
        </span>
        <span class="field-row1">
            <label class="normal" for="article_width">
             Width (cm):
            </label>
            <input size="10" type="text" style="text-align:center" id="article_width" class="positive-number" label="Width" name="article[width]" value="<?php echo $article->width?>"/>
        </span>
        <span class="field-row1">
            <label class="normal" for="article_length">
            Length (cm):
            </label>
            <input size="10" type="text" style="text-align:center" id="article_length" class="positive-number" label="Length" name="article[length]" value="<?php echo $article->length?>"/>
        </span>
        <?php else: ?>
            <input type="hidden" name="article[height]" value="0"/>
            <input type="hidden" name="article[width]" value="0"/>
            <input type="hidden" name="article[length]" value="0"/>
        <?php endif; ?>
        <input type="hidden" id="use_order_weight" value="<?php echo $use_order_weight; ?>"/>
    </div>

    <div class="box consignment-fields">
      <h4 style="margin-bottom:6px">Consignment Fields</h4>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tablecustom">
          <tr>
            <td width="30%" height="35">Partial Delivery allowed?</td>
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
            <td height="35">Delivery signature required?</td>
            <td><select id="delivery_signature_allowed" name="delivery_signature_allowed" style="width:140px">>
                <option value="1" <?php echo ($consignment->delivery_signature_allowed==1?'selected':'')?>>Yes</option>
                <option value="0" <?php echo ($consignment->delivery_signature_allowed!=1?'selected':'')?>>No</option>
            </select></td>
          </tr>
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
          <tr>
            <td height="35">Shipment contains dangerous goods?</td>
            <td><select id="contains_dangerous_goods" name="contains_dangerous_goods" style="width:140px">>
                <option value="1" <?php echo ($consignment->contains_dangerous_goods==1?'selected':'')?>>Yes</option>
                <option value="0" <?php echo ($consignment->contains_dangerous_goods!=1?'selected':'')?>>No</option>
            </select></td>
          </tr>
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
        </table>
    </div>
    
	<div style="margin-top:15px">
        <input type="submit" name="updateArticle"  value="Update Article" onclick="return submitEditArticleForm()" class="button-primary button scalable save submit-button"/>
            &nbsp;&nbsp;
        <button onclick="setLocation('<?php echo admin_url('post.php?post='.$order_id.'&action=edit')?>')" class="scalable back button" type="button" >
            <span><span><span>Cancel</span></span></span>
        </button>
    </div>
</div>
</form>