<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelAdminConsignmentsOrderView
{
	public static function output()
	{
		global $is_greater_than_21;
		require_once(linksynceparcel_DIR.'model/ArticlePreset/Model.php' );
		$articlePreset = new ArticlePreset();
		$presets = $articlePreset->get_by(array('status' => 1));
		$order_id = (int)($_GET['post']);
		$order = new WC_Order( $order_id );
		
		$shipping_country = get_post_meta($order_id,'_shipping_country',true);
		
		if($is_greater_than_21)
		{
            $or_status = method_exists($order, 'get_status') ? $order->get_status() : $order->post_status;
			$order_status = substr($or_status,3);
		}
		else
		{
			$order_status = method_exists($order, 'get_status') ? $order->get_status() : $order->status;
		}
		
		$countries = WC()->countries->countries;
		
		$ordernotes = '';
		if(get_option('linksynceparcel_copy_order_notes') == 1)
		{
			$ordernotes = method_exists($order, 'get_customer_note') ? $order->get_customer_note() : $order->customer_message;
		}
		
		$consignment_cost = LinksynceparcelHelper::getConsignmentCost($order_id);
		$consignment_cost = number_format($consignment_cost, 2);
		$use_order_weight = (int)get_option('linksynceparcel_use_order_weight');
		$use_dimension = (int)get_option('linksynceparcel_use_dimension');
		if($use_order_weight == 1 && $use_dimension != 1)
		{
			include_once(linksynceparcel_DIR.'views/admin/consignments/order_weight_view.php');
		}
		elseif($use_order_weight != 1 && $use_dimension != 1)
		{
			include_once(linksynceparcel_DIR.'views/admin/consignments/default_weight_view.php');
		}
		elseif($use_order_weight == 1 && $use_dimension == 1)
		{
			include_once(linksynceparcel_DIR.'views/admin/consignments/order_weight_articles_view.php');
		}
		else
		{
        	include_once(linksynceparcel_DIR.'views/admin/consignments/view.php');
		}
	}
}
?>