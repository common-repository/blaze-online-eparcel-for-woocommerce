<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="wrap woocommerce">
    <form method="post" id="mainform" action="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=article-presets&action=add'); ?>" enctype="multipart/form-data">
        <?php wp_nonce_field( 'article_preset_action', 'article_preset_nonce_field' ); ?>
        <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
        <h2>
        	<?php _e('Add Article Preset','linksynceparcel'); ?>
        	<a class="add-new-h2" href="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=article-presets'); ?>">Back</a>
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
                 <tr>
                    <td width="20%" valign="top"><?php _e('Preset Name','linksynceparcel'); ?></td>
                    <td align="left">
                       <input type="text" size="40" name="linksynceparcel[name]" value="<?php echo LinksynceparcelHelper::getFormValue('name');?>">
                   </td>
              </tr>
              <tr>
                    <td width="20%" valign="top"><?php _e('Weight (Kgs) ','linksynceparcel'); ?></td>
                    <td align="left">
                       <input type="text" size="40" name="linksynceparcel[weight]" value="<?php echo LinksynceparcelHelper::getFormValue('weight');?>">
                   </td>
              </tr>
              <tr>
                    <td width="20%" valign="top"><?php _e('Height (cm)','linksynceparcel'); ?></td>
                    <td align="left">
                       <input type="text" size="40" name="linksynceparcel[height]" value="<?php echo LinksynceparcelHelper::getFormValue('height');?>">
                   </td>
              </tr>
              <tr>
                    <td width="20%" valign="top"><?php _e('Width (cm)','linksynceparcel'); ?></td>
                    <td align="left">
                       <input type="text" size="40" name="linksynceparcel[width]" value="<?php echo LinksynceparcelHelper::getFormValue('width');?>">
                   </td>
              </tr>
             <tr>
                    <td width="20%" valign="top"><?php _e('Length (cm)','linksynceparcel'); ?></td>
                    <td align="left">
                       <input type="text" size="40" name="linksynceparcel[length]" value="<?php echo LinksynceparcelHelper::getFormValue('length');?>">
                   </td>
              </tr>
              <tr>
                    <td width="20%" valign="top"><?php _e('Enabled','linksynceparcel'); ?></td>
                    <td align="left">
                        <select name="linksynceparcel[status]">
                         	<option value="1" <?php echo ( isset($_REQUEST['linksynceparcel']) ? (LinksynceparcelHelper::getFormValue('status') == 1 ? 'selected="selected"' : '') : 'selected="selected"') ?>>
								<?php _e('Yes','linksynceparcel'); ?>
                            </option>
                            <option value="0" <?php echo ( (isset($_REQUEST['linksynceparcel']) && LinksynceparcelHelper::getFormValue('status') != 1) ? 'selected="selected"' : '') ?>>
								<?php _e('No','linksynceparcel'); ?>
                            </option>
                      </select>
                </td>
              </tr>  
          </table>
</fieldset>
        <br />
        <input type="submit" id="save_preset" name="save" value="<?php _e('Save','linksynceparcel'); ?>" class="button-primary" />
    </form>
</div>