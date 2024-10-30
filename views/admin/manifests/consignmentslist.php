<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="wrap woocommerce">
	<?php
	$error = get_option('linksynceparcel_manifest_view_error');
	if($error)
	{
		LinksynceparcelHelper::addError($error);
		delete_option('linksynceparcel_manifest_view_error');
	}
	?>
    <form method="get" id="mainform" action="<?php echo admin_url('admin.php'); ?>" enctype="multipart/form-data">
    	<?php wp_nonce_field( 'manifest_consignments_action', 'manifest_consignments_nonce_field' ); ?>
    	<input type="hidden" name="page" value="linksynceparcel" />
        <input type="hidden" name="subpage" value="manifests" />
        <input type="hidden" name="action" value="list-consignments" />
        <input type="hidden" name="manifest_number" value="<?php echo $_REQUEST['manifest_number']?>" />
       <?php 
        if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		else
			echo '<input type="hidden" name="orderby" value="consignment_number" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		else
			echo '<input type="hidden" name="order" value="asc" />';
		?>
        <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
        <h2>
        	Manifest Consignments - <?php echo $_REQUEST['manifest_number']?>
        </h2>
        <?php 
        ?>
        <?php 
        $myListTable->prepare_items(); 
		$myListTable->display(); 
		?>
    </form>
</div>