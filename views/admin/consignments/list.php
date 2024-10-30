<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div id="consignment-list" class="wrap woocommerce">
	<?php 
	$error = get_option('linksynceparcel_consignment_error');
	if($error)
	{
		LinksynceparcelHelper::addError($error);
		delete_option('linksynceparcel_consignment_error');
	}
	?>
    <form method="get" id="mainform" action="<?php echo admin_url('admin.php'); ?>" enctype="multipart/form-data">
    	<?php wp_nonce_field( 'consignment_list_action', 'consignment_list_nonce_field' ); ?>
    	<input type="hidden" name="page" value="linksynceparcel" />
        <input type="hidden" name="subpage" value="consignments-search" />
        <?php 
        if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		else
			echo '<input type="hidden" name="orderby" value="consignment_number" />';
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
        ?>
        <?php 
        $myListTable->prepare_items(); 
		$myListTable->display(); 
		?>
    </form>
</div>