<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="wrap woocommerce">
    <form method="post" id="mainform" action="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=assign-shipping-types&action=add'); ?>" enctype="multipart/form-data">
        <?php wp_nonce_field( 'assign_shipping_types_action', 'assign_shipping_types_nonce_field' ); ?>
        <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
        <h2>
        	<?php _e('Add Assign Shipping Type','linksynceparcel'); ?>
        	<a class="add-new-h2" href="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=assign-shipping-types'); ?>">Back</a>
		</h2>
        <?php 
        if(isset($result)) { 
            echo '<h3 style="color:green">'.$result.'</h3>'; 
        }
		if(isset($error)) { 
            echo '<h4 style="color:red">'.$error.'</h4>'; 
        }
        ?>
        <fieldset style="border:1px solid">
            <table width="100%" border="0" cellspacing="0" cellpadding="6">
				  
				<tr class="shipping_desc">
					<td width="20%" valign="top"><?php _e('Shipping Description','linksynceparcel'); ?></td>
					<td align="left">
					   <select name="linksynceparcel[method]" style="width:200px">
							<option value="" <?php if (LinksynceparcelHelper::getFormValue('method') == ''){ echo 'selected="selected"'; }?>>
								<?php _e('Please select','linksynceparcel'); ?>
							</option>
							<?php foreach($shipping_titles as $key=>$shipping_title) {?>
                            <?php 
                                $method = !empty($key)?$key:$shipping_title['title'];
                            ?>
							<option value="<?php echo $method; ?>" <?php if (LinksynceparcelHelper::getFormValue('method') == $method){ echo 'selected="selected"'; }?>>
								<?php echo $shipping_title['title']; ?>
							</option>
							<?php } ?>
						</select>
						<br>
						<span style="margin-left:3px; font-size:11px;">
					   NOTE - when you create a new shipping type for your site IT WILL NOT BE AVAILABLE HERE UNTIL THERE ARE ORDERS USING THE NEW SHIPPING METHOD. <br /> &nbsp;We suggest you to create a test order using the new shipping method and then return to this screen to assign the Charge Code to it.</span>
					</td>
				</tr>
              
				<tr>
                    <td width="20%" valign="top"><?php _e('Services','linksynceparcel'); ?></td>
                    <td align="left">
                     	<select name="linksynceparcel[charge_code]" style="width:200px">
                        	<option value="" <?php if (LinksynceparcelHelper::getFormValue('charge_code') == ''){ echo 'selected="selected"'; }?>>
								<?php _e('Please select','linksynceparcel'); ?>
                            </option>
                            <?php 
                            if(!empty($chargeCodes)) {
							 foreach($chargeCodes as $chargeCode) {
							?>
							<option value="<?php echo $chargeCode['Name']?>">
                                <?php echo $chargeCode['Type'] .' => '. $chargeCode['Name']; ?>
                            </option>
                            <?php
                                }
                            } 
                            ?>
                       </select>
                   </td>
              </tr>
          </table>
		</fieldset>
        <br />
        <input type="submit" name="save" value="<?php _e('Save','linksynceparcel'); ?>" class="button-primary" />
    </form>
</div>