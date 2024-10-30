<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php global $my_plugin_hook,$linksynceparcel_consignment_menu; ?>
<div class="wrap woocommerce" id="consignment-orderlist" data-mode="<?php echo trim(get_option('linksynceparcel_operation_mode')); ?>">
	<div id="loading" style="display:none;">
		<div id="img-loader">
			<img src="<?php echo linksynceparcel_URL?>assets/images/load.gif" alt="Loading" />
		</div>
	</div>
	<?php
	$error = get_option('linksynceparcel_consignment_error');
	if($error)
	{
		LinksynceparcelHelper::addError($error);
		delete_option('linksynceparcel_consignment_error');
	}
	$success = get_option('linksynceparcel_consignment_success');
	if($success)
	{
		LinksynceparcelHelper::addSuccess($success);
		delete_option('linksynceparcel_consignment_success');
	}
	$config_checker = LinksynceparcelHelper::checkAssignConfigurationSettings();
	?>
<form class="linksynceparcel-table-form" method="get" id="mainform" action="<?php echo admin_url('admin.php'); ?>" enctype="multipart/form-data" onsubmit="return submitConsignmentBulkForm()">
		<?php wp_nonce_field( 'consignment_order_list_action', 'consignment_order_list_nonce_field' ); ?>
    	<input type="hidden" name="page" value="linksynceparcel" />
        <?php 
        if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		else
			echo '<input type="hidden" name="orderby" value="order_id" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		else
			echo '<input type="hidden" name="order" value="desc" />';
		?>
        <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
        <h2>
        	Consignments
        </h2>
        <?php
		if($config_checker) {
		?>
		<div class="error">
			<p>API Key, Shipping and Tracking for Blaze Online eParcel configuration are not complete. <a href="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=configuration'); ?>">Click here to setup your account.</a> If you dont have account yet <a target="_blank" href="https://my.blaze.online/signup.php">Click here to signup.</a></p>
		</div>
		<?php
		} else {
			$linksynceparcel_Version = LinksynceparcelHelper::checkLinksynceParcelVersion();
			if($linksynceparcel_Version > '1.1.2') {
				if( LinksynceparcelHelper::checkNewChargeCodeConfig() ) {
					$checker = $myListTable->checkNonLinksync();
					if (isset($checker) && $checker > 0)
					{ 
						$myListTable->prepare_items(); 
						$myListTable->display();
					} else { ?>
						<div class="update-nag notice">
							<p>Blaze Online eParcel requires at least one shipping type. <a href="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=assign-shipping-types&action=add')?>">Click here</a> to assing shipping types.</p>
						</div>
					<?php }
				} else {
				?>
					<div class="update-nag notice">
						<p>Blaze Online eParcel requires to update your charge code configuration.  After you configure your charge code settings please update also your shipping types for eParcel.</p>
						<br/>
						<p><a href="https://help.linksync.com/hc/en-us/articles/206771050" target="_blank">Follow this link that contains a step by step instruction on how to configure your new charge code settings.</a></p>
					</div>
				<?php
				}
			} else {
				$checker = $myListTable->checkNonLinksync();
				if (isset($checker) && $checker > 0)
				{ 
					$myListTable->prepare_items(); 
					$myListTable->display();
				} else { ?>
					<div class="update-nag notice">
						<p>Blaze Online eParcel requires at least one shipping type. <a href="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=assign-shipping-types&action=add')?>">Click here</a> to assing shipping types.</p>
					</div>
				<?php }
			}
					
		}
		?>
    </form>
</div>

<div id="dialog" title="Submit Test Manifest" style="display:none">
<form method="post">
<p>You are in test mode. Test mode enables you to use and test all features of the Blaze Online eParcel without actually submitting a manifest to Australia Post on despatch of a manifest.</p>
<label> <input id="dialog_checkbox" name="dialog_checkbox" type="checkbox"> I acknowledge this is only a test. </label>
<br /><br /><br/>
<input id="dialog_submit" type="submit" value="Submit" style="float:right" class="button">
</form>
</div>

<div id="dialog2" title="Submit Manifest" style="display:none">
<form method="post">
<p>You are about to submit your manifest to Australia Post. Once your manifest is despatched, you won't be able to modify it, or the associated consignments..</p>
<br /><br/>
<input id="dialog_submit2" type="submit" value="Submit" style="float:right" class="button">
</form>
</div>
