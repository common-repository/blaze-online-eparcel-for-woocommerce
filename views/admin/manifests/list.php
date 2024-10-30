<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="wrap woocommerce" id="manifest-list">

	<?php
	$error = get_option('linksynceparcel_manifest_view_error');
	if($error)
	{
		LinksynceparcelHelper::addError($error);
		delete_option('linksynceparcel_manifest_view_error');
	}
	$success = get_option('linksynceparcel_manifest_view_success');
	if($success)
	{
		LinksynceparcelHelper::addSuccess($success);
		delete_option('linksynceparcel_manifest_view_success');
	}
	?>

    <form method="get" id="mainform" action="<?php echo admin_url('admin.php'); ?>" enctype="multipart/form-data">
    	<?php wp_nonce_field( 'manifest_list_action', 'manifest_list_nonce_field' ); ?>
    	<input type="hidden" name="page" value="linksynceparcel" />
        <input type="hidden" name="subpage" value="manifests" />
       <?php 
        if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		else
			echo '<input type="hidden" name="orderby" value="manifest_number" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		else
			echo '<input type="hidden" name="order" value="desc" />';
		?>
        <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
        <h2>
        	Manifests
        </h2>
        <?php 
        ?>
        <?php 
        $myListTable->prepare_items(); 
		$myListTable->display(); 
		?>
        <input type="hidden" value="" name="_wp_http_referer">
    </form>
</div>