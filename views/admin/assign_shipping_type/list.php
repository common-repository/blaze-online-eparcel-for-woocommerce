<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="wrap woocommerce">
    <form method="post" id="mainform" action="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=assign-shipping-types'); ?>" enctype="multipart/form-data">
        <?php wp_nonce_field( 'assign_shipping_types_list_action', 'assign_shipping_types_list_nonce_field' ); ?>
        <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
        <h2>
        	<?php _e('Assign Shipping Types','linksynceparcel'); ?>
        	<a class="add-new-h2" href="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=assign-shipping-types&action=add'); ?>">Add New</a>
        </h2>
        <?php 
        if(isset($result)) { 
            echo '<h3 style="color:green">'.$result.'</h3>'; 
        }
		if(isset($error)) { 
            echo '<h4 style="color:red">'.$error.'</h4>'; 
        }
        ?>
        <?php 
        $myListTable->prepare_items(); 
		$myListTable->display(); 
		?>
    </form>
</div>