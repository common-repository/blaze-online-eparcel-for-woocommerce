<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="wrap woocommerce">
    <form method="post" id="mainform" action="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=article-presets'); ?>" enctype="multipart/form-data">
        <?php wp_nonce_field( 'article_preset_list_action', 'article_preset_list_nonce_field' ); ?>
        <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
        <h2>
        	<?php _e('Article Presets','linksynceparcel'); ?>
        	<a class="add-new-h2" href="<?php echo admin_url('admin.php?page=linksynceparcel&subpage=article-presets&action=add'); ?>">Add New</a>
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