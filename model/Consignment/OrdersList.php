<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once(linksynceparcel_DIR.'model/Consignment/OrdersModel.php' );

class ConsignmentOrdersList extends WP_List_Table
{
	public function __construct()
	{
    	global $status, $page;

        parent::__construct( 
			array(
				'singular'  => 'Order',
				'plural'    => 'Orders',
				'ajax'      => false
    		)
		);
    }

	public function column_default( $item, $column_name )
	{
		switch( $column_name )
		{ 
			case 'service':
			case 'order_id':
			case 'consignment_number':
			case 'shipping_cost':
			case 'add_date':
			case 'is_address_valid':
			case 'number_of_articles':
			case 'shipping_description':
			case 'is_label_printed':
			case 'is_return_label_printed':
			case 'is_next_manifest':
			case 'weight':
				return (is_object($item) ? $item->$column_name : $item[$column_name]);
			default:
				return print_r( $item, true );
		}
	}
	
	public function column_consignment_number($item)
	{
		$order_id = $item->ID;
		$html = '<a href="'.admin_url('post.php?post='.$order_id.'&action=edit').'">'.($item->consignment_number ? $item->consignment_number:'Create Consignment').'</a>';
		return $html;
	}
	
	public function column_shipping_cost($item)
	{
		$order_id = $item->ID;
		$cost_amount = $item->shipping_cost;
		if(empty($cost_amount)) {
			$cost_amount = LinksynceparcelHelper::getTempConsignmentCost($order_id);
			// if(is_null($cost_amount) || empty($cost_amount)) {
			// 	$cost_amount = LinksynceparcelHelper::getTempConsignmentPrice($order_id);
			// }
			if(is_null($cost_amount) || empty($cost_amount)) {
				$cost_amount = 0;
			}
		}
		$cost = number_format($cost_amount, 2);
		$html = '<p>$'. $cost .'</p>';
		return $html;
	}
	
	public function column_service($item)
	{
		$color = '';
		$servicecode = '';
		$meta = get_post_meta($item->order_id);
		$ship_country = $meta['_shipping_country'][0];
		if($ship_country != 'AU') {
			$color = 'blue';
			$servicecode = 'Int.';
		} else {
			$allowedChargeCodes = LinksynceparcelHelper::getEParcelChargeCodes();
			$chargecode = $item->chargecode;
			if(empty($chargecode)) {
				$chargecode = LinksynceparcelHelper::getOrderChargeCode($item->order_id);
			}
			$chargeCodeData = $allowedChargeCodes[$chargecode];
			switch($chargeCodeData['serviceType']) {
				case 'express':
					$color = 'orange';
					break;
				case 'standard':
					$color = 'yellow';
					break;
				case 'international':
					$color = 'blue';
					break;
			}
			$servicecode = ucfirst($chargeCodeData['service']);
		}
		$html = '<p class="bg-'. $color .'">'. $servicecode .'</p>';
		return $html;
	}
	
	public function column_order_id($item)
	{
		$order_id = $item->ID;
		$meta = get_post_meta($item->order_id);
		$order = new WC_Order( $order_id );
		$html = '<a href="'.admin_url('post.php?post='.$order_id.'&action=edit').'"><strong>#'.$order->get_order_number().'</strong></a> by <a href="'. admin_url('user-edit.php?user_id='. $meta['_customer_user'][0]) .'">'. $meta['_shipping_first_name'][0] .'</a><small class="meta email"><a href="mailto:'. $meta['_billing_email'][0] .'">'. $meta['_billing_email'][0] .'</a></small>';
		return $html;
	}
	
	public function column_order_item($item)
	{
		global $wpdb;
		$order_id = $item->ID;
		$order = new WC_Order( $order_id );
		$items = array();
		$numberItems = 0;
		foreach($order->get_items() as $item) {
			$productid = $item['product_id'];
			$name = $item['name'];
			$qty = $item['qty'];
			$numberItems += $qty;
			
			$variation_id = $item['variation_id'];
			
			// Check if product has variation.
			if ($variation_id) { 
				$product = new WC_Product_Variation($variation_id);
			} else {
				$product = new WC_Product($productid);
			}
			$sku = $product->get_sku();
			
			$items[] = array(
				'productid'	=> $productid,
				'name' 	=> $name,
				'qty'	=> $qty,
				'sku' 	=> $sku
			);
		}
		$text = 'item';
		if($numberItems > 1)
			$text = 'items';
		
		$handler = $this->randomKey() .'_'. strtotime(date('Y-m-d H:i:s'));
		$html = '<a href="javascript:void(0)" class="handler" data-key="'. $handler .'">'. $numberItems .' '. $text .'</a>';
		$html .= '<table class="order_items" id="key-'. $handler .'"><tbody>';
		$count = count($items)-1;
		for($i=0;$i<=$count;$i++) {
			$html .= '<tr>';
			$html .= '<td class="qty">'. $items[$i]['qty'] .'</td>';
			$html .= '<td class="name">'. $items[$i]['sku'] .' - <a href="'. admin_url('post.php?post='.$items[$i]['productid'].'&action=edit') .'">'. $items[$i]['name'] .'</a></td>';
			$html .= '</tr>';
		}
		$html .= '</tbody></table>';
		return $html;
	}
	
	public function randomKey() {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array();
		$alphaLength = strlen($alphabet) - 1;
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass);
	}
	
	public function column_customer_name($item)
	{
		$meta = get_post_meta($item->order_id);
		$ship_add_1 = $meta['_shipping_address_1'][0].', ';
		$ship_add_2 = !empty($meta['_shipping_address_2'][0])?$meta['_shipping_address_2'][0].', ':'';
		$ship_city = $meta['_shipping_city'][0].', ';
		$ship_state = $meta['_shipping_state'][0].', ';
		$ship_postcode = $meta['_shipping_postcode'][0];
		$ship_country = ', '.$meta['_shipping_country'][0];
		
		$f_address = $ship_add_1 . $ship_add_2 . $ship_city . $ship_state . $ship_postcode;
		return '<a href="http://maps.google.com/maps?&q='. urlencode($f_address . $ship_country) .'&z=16">'. $meta['_shipping_first_name'][0].' '.$meta['_shipping_last_name'][0] .', '. $f_address .'</a>';
	}
	
	public function column_customers_note($item)
	{
		$order_id = $item->ID;
		$order = new WC_Order( $order_id );
		$notes = $order->get_customer_note();
		if(!empty($notes)) {
			return '<span class="notes_head tips tooltip" title="'. $notes .'">Customer Message</span>';
		} else {
			return "-";
		}
	}
	
	public function column_shipping_description($item)
	{
		$chargecode = $item->chargecode;
		if(empty($chargecode)) {
			$chargecode = LinksynceparcelHelper::getOrderChargeCode($item->order_id);
		}
		$shipping_description = $item->shipping_description;
		$allowedChargeCodes = LinksynceparcelHelper::getEParcelChargeCodes();
		$chargeCodeData = $allowedChargeCodes[$chargecode];
		$html = $shipping_description .' - '. $chargeCodeData['name'];
		return $html;
	}
	
	public function column_is_address_valid($item)
	{
		$shipping_country = get_post_meta($item->ID,'_shipping_country',true);
		$order_id = $item->ID;
		if($shipping_country == 'AU') {
			if(isset($item->is_address_valid)) {
				if($item->is_address_valid == 1)
				{
					$html = '<span class="column-order_status"><mark class="completed tips" style="cursor:pointer">Yes</mark></span>';
				} else {
					$html = '<a href="'.admin_url('post.php?post='.$order_id.'&action=edit#order_data').'"><span class="column-order_status"><mark class="cancelled tips">No</mark></span></a>';
				}
			} else {
				$valid = LinksynceparcelHelper::isOrderAddressValid($order_id);
				if($valid == 1) {
					$html = '<span class="column-order_status"><mark class="completed tips" style="cursor:pointer">Yes</mark></span>';
				} else {
					$html = '<a href="'.admin_url('post.php?post='.$order_id.'&action=edit#order_data').'"><span class="column-order_status"><mark class="cancelled tips">No</mark></span></a>';
				}
			}
		} else {
			$html = '<span class="column-order_status"><mark class="on-hold tips">International</mark></span>';
		}
		
		return $html;
	}
	
	public function column_is_label_printed($item)
	{
		if(isset($item->is_label_printed))
		{
			if($item->is_label_printed == 1)
			{
				$html = '<a lang="'.$item->consignment_number.'" href="'. admin_url() .'?f_key='. $item->consignment_number .'&f_type=consignment" target="_blank" ><span class="column-order_status"><mark class="completed tips" style="cursor:pointer">Yes</mark></span></a>';
			}
			else if($item->is_label_created == 1)
			{
				$html = '<a class="print_label" lang="'.$item->consignment_number.'" href="'. admin_url() .'?f_key='. $item->consignment_number .'&f_type=consignment" target="_blank" ><span class="column-order_status"><mark class="cancelled tips">No</mark></span></a>';
			}
			else
			{
				$html = '<a href="'.admin_url('admin.php?page=linksynceparcel&action=singleGenerateLabel&order='.$item->order_consignment).'" target="_blank" ><span class="column-order_status"><mark class="cancelled tips">No</mark></span></a>';
			}
		}
		else
		{
			$html = '&nbsp;';
		}
		return $html;
	}
	
	public function column_is_next_manifest($item)
	{
		if(isset($item->is_next_manifest))
		{
			if($item->is_next_manifest == 1)
			{
				$html = '<span class="column-order_status"><mark class="completed tips" style="cursor:pointer">Yes</mark></span>';
			}
			else
			{
				$html = '<span class="column-order_status"><mark class="cancelled tips">No</mark></span>';
			}
		}
		else
		{
			$html = '&nbsp;';
		}
		return $html;
	}

	public function get_columns()
	{
		$user = wp_get_current_user();
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'service' => 'Service',
			'order_id' => 'Order',
			'order_item' => 'Order Item',
			'order_status' => '<span class="status_head tips" title="Status">Status</span>',
			'customer_name' => 'Ship to',
			'customers_note' => '<span class="notes_head tips tooltip" title="Customer Message">Customer Message</span>',
			'weight' => 'Weight',
			'is_address_valid' => 'Address Valid',
			'consignment_number' => 'Consignment Number',
			'shipping_cost' => 'Consignment Cost',
			'shipping_description' => 'Delivery Type',
			'is_label_printed' => 'Labels Printed?',
			'is_next_manifest' => 'Next Manifest?',
			'number_of_articles' => 'No. of Articles',
			'add_date' => 'Date Created',
		);
		
		// Hide for this release
		unset($columns['is_return_label_printed']);
		
		// Hide Columns
		$user_defaults = get_user_meta($user->ID, 'rawhtml_defaults', true);
		if(isset($user_defaults['show_service'])) {
			if(isset($user_defaults['show_service']) && empty($user_defaults['show_service'])) {
				unset($columns['service']);
			}
			if(isset($user_defaults['show_orderstatus']) && empty($user_defaults['show_orderstatus'])) {
				unset($columns['order_status']);
			}
			if(isset($user_defaults['show_orderitem']) && empty($user_defaults['show_orderitem'])) {
				unset($columns['order_item']);
			}
			if(isset($user_defaults['show_shipto']) && empty($user_defaults['show_shipto'])) {
				unset($columns['customer_name']);
			}
			if(isset($user_defaults['show_customernotes']) && empty($user_defaults['show_customernotes'])) {
				unset($columns['customers_note']);
			}
			if(isset($user_defaults['show_weight']) && empty($user_defaults['show_weight'])) {
				unset($columns['weight']);
			}
			if(isset($user_defaults['show_deliverytype']) && empty($user_defaults['show_deliverytype'])) {
				unset($columns['shipping_description']);
			}
			if(isset($user_defaults['show_numberofarticles']) && empty($user_defaults['show_numberofarticles'])) {
				unset($columns['number_of_articles']);
			}
			if(isset($user_defaults['show_consignmentcost']) && empty($user_defaults['show_consignmentcost'])) {
				unset($columns['shipping_cost']);
			}
		}
		
		return $columns;
    }
	
	public function column_order_status($item)
	{
		global $is_greater_than_21;
		$statuses = LinksynceparcelHelper::getOrderStatuses();
		if($is_greater_than_21)
		{
			$order_status = $item->post_status;
			$icon = substr($order_status,3);
			$label = str_replace('-',' ',$icon);
			$label = ucwords($label);
			foreach($statuses as $term_id => $status)
			{
				if($order_status == $status)
					$label = $status;
			}
			echo '<mark class="'.$icon.' tips" title="'.$label.'">'.$label.'</mark>';
		}
		else
		{
			$order_status = $item->status;
			$icon = $order_status;
			$label = str_replace('-',' ',$order_status);
			$label = ucwords($label);
			foreach($statuses as $status)
			{
				if($order_status == $status->slug)
					$label = $status->name;
			}
			echo '<mark class="'.$icon.' tips" title="'.$label.'">'.$label.'</mark>';
		}
	}
	
	public function prepare_items()
	{
		global $wpdb,$is_greater_than_21;
		$consignmentOrders = new ConsignmentOrders();
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		
		$chosen_statuses = get_option('linksynceparcel_chosen_statuses');
		if($is_greater_than_21)
		{
			$status_condition = 'post_status="wc-pending" OR post_status="wc-processing" OR post_status="wc-on-hold" OR ';
			
			$display_choosen_status = (int)get_option('linksynceparcel_display_choosen_status');
			if($display_choosen_status == 1)
			{
				if($chosen_statuses && count($chosen_statuses) > 0)
				{
					$status_condition = '';
					foreach($chosen_statuses as $chosen_status)
					{
						$status_condition .= 'post_status="'.$chosen_status.'" OR ';
					}
				}
			}
			
			$where = ' AND main_table.post_type="shop_order"';
			
			$join = ' LEFT JOIN '.$wpdb->prefix.'postmeta pm ON main_table.ID=pm.post_id';
			
			$where .= ' AND pm.meta_key="_shipping_country" AND IFNULL(c.despatched,0) = 0';
			$where .= ' AND ('.$status_condition.' (case when (select count(*) from '.$wpdb->prefix.'linksynceparcel_consignment where order_id = main_table.ID) > 0 then (select count(*) from '.$wpdb->prefix.'linksynceparcel_consignment where order_id = main_table.ID and despatched = 0) else 0 end) > 0 )';
			
			$joinFields = ',c.chargecode,c.weight';
			$join .= ' LEFT JOIN '.$wpdb->prefix.'linksynceparcel_consignment c ON main_table.ID=c.order_id';
			
			$joinFields .= ",(case when c.consignment_number != '' then ( select count(*) from ".$wpdb->prefix."linksynceparcel_article where consignment_number = c.consignment_number) else null end) as number_of_articles";
			
			$joinFields .= ',av.is_address_valid,av.is_address_valid as is_not_open';
			$join .= ' LEFT JOIN '.$wpdb->prefix.'linksynceparcel_address_valid av ON main_table.ID=av.order_id';
			
			$joinFields .= ",woi.order_item_name as shipping_description";
			$join .= ' LEFT JOIN '.$wpdb->prefix.'woocommerce_order_items woi ON main_table.ID=woi.order_id AND woi.order_item_type="shipping"';
			
			$joinFields .= ",woim.meta_value as shipping_method";
			$join .= ' LEFT JOIN '.$wpdb->prefix.'woocommerce_order_itemmeta woim ON woi.order_item_id=woim.order_item_id AND woi.order_item_type="shipping" AND woim.meta_key="method_id"';
			
			$where .= ' AND ( 
							case 
							when (select count(*) from '.$wpdb->prefix.'linksynceparcel_nonlinksync where method = woim.meta_value and charge_code != "none") > 0 
								then 1
							when (select count(*) from '.$wpdb->prefix.'linksynceparcel_nonlinksync where method = woi.order_item_name and charge_code != "none") > 0 
								then 1
							when (select charge_code from '.$wpdb->prefix.'linksynceparcel_nonlinksync where method = woim.meta_value) = "none"
								then 0
							when (select charge_code from '.$wpdb->prefix.'linksynceparcel_nonlinksync where method = woi.order_item_name) = "none"
								then 0
							else 1
							end
						) > 0
					';
			
			$joinFields .= ',CONCAT(main_table.ID, "_",IFNULL(c.consignment_number,0)) as order_consignment';
			$joinFields .= ',main_table.ID as order_id';
			$joinFields .= ',c.is_label_printed,c.is_customdocs_printed,c.is_return_label_printed,c.consignment_number,c.add_date,c.is_next_manifest,c.is_label_created,c.print_return_labels,c.shipping_cost';
		}
		else
		{
			$status_condition = 't.slug="pending" OR t.slug="processing" OR t.slug="on-hold" OR ';
			
			$display_choosen_status = (int)get_option('linksynceparcel_display_choosen_status');
			if($display_choosen_status == 1)
			{
				if($chosen_statuses && count($chosen_statuses) > 0)
				{
					$status_condition = '';
					foreach($chosen_statuses as $chosen_status)
					{
						$status_condition .= 't.slug="'.$chosen_status.'" OR ';
					}
				}
			}
			
			$where = ' AND main_table.post_type="shop_order" AND post_status="publish"';
			
			$join = ' LEFT JOIN '.$wpdb->prefix.'postmeta pm ON main_table.ID=pm.post_id';
			$join .= ' LEFT JOIN '.$wpdb->prefix.'term_relationships tr ON main_table.ID=tr.object_id';
			$join .= ' LEFT JOIN '.$wpdb->prefix.'term_taxonomy tt ON tr.term_taxonomy_id=tt.term_taxonomy_id';
			$join .= ' LEFT JOIN '.$wpdb->prefix.'terms t ON tt.term_id=t.term_id';
			
			$joinFields = ',t.slug as status';
			$where .= ' AND pm.meta_key="_shipping_country" AND pm.meta_value="AU" AND IFNULL(c.despatched,0) = 0';
			$where .= ' AND tt.taxonomy="shop_order_status"';
			$where .= ' AND ('.$status_condition.' (case when (select count(*) from '.$wpdb->prefix.'linksynceparcel_consignment where order_id = main_table.ID) > 0 then (select count(*) from '.$wpdb->prefix.'linksynceparcel_consignment where order_id = main_table.ID and despatched = 0) else 0 end) > 0 )';
			
			$joinFields .= ',c.chargecode,c.weight';
			$join .= ' LEFT JOIN '.$wpdb->prefix.'linksynceparcel_consignment c ON main_table.ID=c.order_id';
			
			$joinFields .= ",(case when c.consignment_number != '' then ( select count(*) from ".$wpdb->prefix."linksynceparcel_article where consignment_number = c.consignment_number) else null end) as number_of_articles";
			
			$joinFields .= ',av.is_address_valid,av.is_address_valid as is_not_open';
			$join .= ' LEFT JOIN '.$wpdb->prefix.'linksynceparcel_address_valid av ON main_table.ID=av.order_id';
			
			$joinFields .= ",woi.order_item_name as shipping_description";
			$join .= ' LEFT JOIN '.$wpdb->prefix.'woocommerce_order_items woi ON main_table.ID=woi.order_id AND woi.order_item_type="shipping"';
			
			$joinFields .= ",woim.meta_value as shipping_method";
			$join .= ' LEFT JOIN '.$wpdb->prefix.'woocommerce_order_itemmeta woim ON woi.order_item_id=woim.order_item_id AND woi.order_item_type="shipping" AND woim.meta_key="method_id"';
			
			$where .= ' AND ( 
							case 
							when (select count(*) from '.$wpdb->prefix.'linksynceparcel_nonlinksync where method = woim.meta_value and charge_code != "none") > 0 
								then 1 
							when (select charge_code from '.$wpdb->prefix.'linksynceparcel_nonlinksync where method = woim.meta_value) = "none"
								then 0 
							else 1
							end
						) > 0
					';
			
			$joinFields .= ',CONCAT(main_table.ID, "_",IFNULL(c.consignment_number,0)) as order_consignment';
			$joinFields .= ',main_table.ID as order_id';
			$joinFields .= ',c.is_label_printed,c.is_customdocs_printed,c.is_return_label_printed,c.consignment_number,c.add_date,c.is_next_manifest,c.is_label_created,c.print_return_labels,c.shipping_cost';
		}
		
		$orderids = LinksynceparcelHelper::getAllOrderId();
		if($orderids != false) {
			$where = ' AND main_table.post_type="shop_order"';
			$where .= ' AND pm.meta_key="_shipping_country" AND IFNULL(c.despatched,0) = 0';
			$where .= ' AND main_table.ID IN ('. $orderids .')';
		}
		
		if(isset($_REQUEST['service']) && !empty($_REQUEST['service']))
		{
			$service = $_REQUEST['service'];
			$order_ids = self::isInternationalServiceFilter($service);
			$implodes = 0;
			if(!empty($order_ids)) {
				$implodes = implode('","', $order_ids);
			}
			$where .= ' AND main_table.ID IN ("'. $implodes .'")';
		}
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id']))
		{
			$order_id = (int)($_REQUEST['order_id']);
			$where .= ' AND main_table.ID like "%'.$order_id.'%"';
		}
		
		if(isset($_REQUEST['is_address_valid']) && !empty($_REQUEST['is_address_valid']))
		{
			$is_address_valid = ($_REQUEST['is_address_valid']);
			if($is_address_valid == 1)
				$where .= ' AND (case when (select count(*) from '.$wpdb->prefix.'linksynceparcel_address_valid where order_id = main_table.ID) > 0 then av.is_address_valid else 0 end) > 0';
			else
				$where .= ' AND (case when (select count(*) from '.$wpdb->prefix.'linksynceparcel_address_valid where order_id = main_table.ID) > 0 then av.is_address_valid else 0 end) = 0';
		}
		if(isset($_REQUEST['consignment_number']) && !empty($_REQUEST['consignment_number']))
		{
			$consignment_number = ($_REQUEST['consignment_number']);
			$where .= ' AND c.consignment_number like "%'.$consignment_number.'%"';
		}
		if(isset($_REQUEST['shipping_description']) && !empty($_REQUEST['shipping_description']))
		{
			$shipping_description = ($_REQUEST['shipping_description']);
			$where .= ' AND c.chargecode = "'.$shipping_description.'"';
		}
		if(isset($_REQUEST['is_label_printed']) && !empty($_REQUEST['is_label_printed']))
		{
			$is_label_printed = ($_REQUEST['is_label_printed']);
			if($is_label_printed == 1)
				$where .= ' AND c.is_label_printed = 1';
			else
				$where .= ' AND c.is_label_printed != 1';
		}
		if(isset($_REQUEST['is_customdocs_printed']) && !empty($_REQUEST['is_customdocs_printed']))
		{
			$is_customdocs_printed = ($_REQUEST['is_customdocs_printed']);
			if($is_customdocs_printed == 1)
				$where .= ' AND c.is_customdocs_printed = 1';
			else
				$where .= ' AND c.is_customdocs_printed != 1';
		}
		if(isset($_REQUEST['is_return_label_printed']) && !empty($_REQUEST['is_return_label_printed']))
		{
			$is_return_label_printed = ($_REQUEST['is_return_label_printed']);
			if($is_return_label_printed == 1)
				$where .= ' AND c.is_return_label_printed = 1';
			else
				$where .= ' AND c.is_return_label_printed != 1';
		}
		if(isset($_REQUEST['is_next_manifest']) && !empty($_REQUEST['is_next_manifest']))
		{
			$is_next_manifest = ($_REQUEST['is_next_manifest']);
			if($is_next_manifest == 1)
				$where .= ' AND c.is_next_manifest = 1';
			else
				$where .= ' AND c.is_next_manifest != 1';
		}
		if(isset($_REQUEST['add_date_from']) && !empty($_REQUEST['add_date_from']))
		{
			$add_date_from = ($_REQUEST['add_date_from']);
			$where .= ' AND c.add_date >= "'.$add_date_from.' 00:00:00"';
		}
		if(isset($_REQUEST['add_date_to']) && !empty($_REQUEST['add_date_to']))
		{
			$add_date_to = ($_REQUEST['add_date_to']);
			$where .= ' AND c.add_date <= "'.$add_date_to.' 23:59:59"';
		}
		
		$groupBy = '';
		$orderBy = 'main_table.ID desc';
		if(isset($_GET['orderby']) && !empty($_GET['orderby']))
		{
			if($_GET['orderby'] == 'order_id')
			{
				$orderBy = 'main_table.ID '.$_GET['order'];
			}
			else if($_GET['orderby'] == 'number_of_articles')
			{
				$orderBy = 'number_of_articles '.$_GET['order'];
			}
			else
			{
				$orderBy = '';
			}
		}

		$data = $consignmentOrders->get_all($orderBy,$join,$joinFields,$where,$groupBy);


		if(isset($_GET['orderby']) && !empty($_GET['orderby']))
		{
			if( !($_GET['orderby'] == 'order_id' || $_GET['orderby'] == 'number_of_articles') )
			{
				usort( $data, array( &$this, 'usort_reorder' ) );
			}
		}
		
		$per_page = (int)get_option('consignment_per_page');
		if($per_page == 0)
		{
			$per_page = 20;
		}
		$current_page = $this->get_pagenum();
		$total_items = count($data);

 		$found_data = array_slice($data,(($current_page-1)*$per_page),$per_page);

		$this->set_pagination_args( array(
		 'total_items' => $total_items,
		 'per_page'    => $per_page 
		) );
		$this->items = $found_data;
	}
	
	function get_sortable_columns()
	{
	  $sortable_columns = array(
		'service'  => array('service',false),
		'order_id'  => array('order_id',true),
		'consignment_number' => array('consignment_number',false),
		'created_date'   => array('created_date',false),
		'number_of_articles'   => array('number_of_articles',false),
	  );
	  return $sortable_columns;
	}
	
	function usort_reorder( $a, $b ) 
	{
	  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'order_id';
	  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
	  $result = strcmp( $a->$orderby, $b->$orderby );
	  return ( $order === 'asc' ) ? $result : -$result;
	}
	
	public function column_cb($item)
	{
	 	return sprintf('<input type="checkbox" name="%1$s[]" class="massaction-checkbox" value="%2$s" />', $this->_args['singular'], (is_object($item) ? $item->order_consignment : $item['order_consignment']));
	}
	
	public function get_bulk_actions()
	{
		$use_order_weight = (int)get_option('linksynceparcel_use_order_weight');
		$use_dimension = (int)get_option('linksynceparcel_use_dimension');

		if($use_order_weight == 1)
		{
			$actions['massCreateConsignment'] =  'Create Consignment';
		}
		else if($use_dimension == 1)
		{
			require_once(linksynceparcel_DIR.'model/ArticlePreset/Model.php' );
			$articlePreset = new ArticlePreset();
			$presets = $articlePreset->get_by(array('status' => 1));
			if(count($presets) > 0)
			{
				$actions['massCreateConsignment'] =  'Create Consignment';
			}
		}
		
		$actions2 = array(
			'massGenerateLabels' => 'Generate Labels',
			'massUnassignConsignment' => 'Remove from Manifest',
			'massAssignConsignment' => 'Add to Manifest',
			'massDeleteConsignment' => 'Delete Consignment'
		);
		
		if(isset($actions) && is_array($actions))
			$actions = array_merge($actions,$actions2);
		else
			$actions = $actions2;
			
		if(trim(get_option('linksynceparcel_mark_despatch')) == 1)
		{
			$actions['massMarkDespatched'] =  'Mark as Despatched';
		}
		
		return $actions;
	}
	
	public function extra_tablenav($which)
	{
?>
        <div class="alignleft actions">
        <?php
			if ( 'top' == $which)
			{
				echo '<a id="despatchManifest" href="javascript:void(0)" onclick="setLocationConfirmOrderListDialog()" class="button '.(!LinksynceparcelHelper::isCurrentMainfestHasConsignmentsForDespatch() ? 'disabled': '').'">Despatch</a>';
				echo '&nbsp;&nbsp;';
				echo '<a href="'.admin_url('admin.php?page=linksynceparcel&reset=1').'" class="button">Reset Filter</a>';
				echo '&nbsp;&nbsp;';
				submit_button( __( 'Search' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
			}
        ?>
        </div>
        <?php        
	}

	public function print_column_headers( $with_id = true ) 
	{
		parent::print_column_headers( $with_id );
		
		if($with_id)
		{
			list( $columns, $hidden, $sortable ) = $this->get_column_info();
			echo '<tr>';
			foreach ( $columns as $column_key => $column_display_name )
			{
				if($column_key != 'order_item' && $column_key != 'customer_name' && $column_key != 'customers_note' && $column_key != 'cb' && $column_key != 'number_of_articles'  && $column_key != 'order_status' && $column_key != 'weight')
				{
					if($column_key == 'add_date')
					{
						echo '<td>
								<input class="new-input datepicker" type="text" name="'.$column_key.'_from" value="'.(isset($_REQUEST[$column_key.'_from']) ? $_REQUEST[$column_key.'_from'] : '').'" />
								<br/>
								<input class="new-input datepicker" type="text" name="'.$column_key.'_to" value="'.(isset($_REQUEST[$column_key.'_to']) ? $_REQUEST[$column_key.'_to'] : '').'" />
							</td>';
					}
					else if($column_key == 'is_address_valid' || $column_key == 'is_label_printed' || $column_key == 'is_customdocs_printed' || $column_key == 'is_return_label_printed' || $column_key == 'is_next_manifest')
					{
						echo '<td style="text-align:center">
								<select name="'.$column_key.'">
									<option value=""></option>
									<option value="1" '.( (isset($_REQUEST[$column_key]) && $_REQUEST[$column_key] == 1) ? "selected='selected'" : "" ).'>Yes</option>
									<option value="2" '.( (isset($_REQUEST[$column_key]) && $_REQUEST[$column_key] == 2) ? "selected='selected'" : "" ).' >No</option>
								</select>
							</td>';
					}
					else if($column_key == 'shipping_description')
					{
						$chargeCodes = LinksynceparcelHelper::getChargeCodeValues();
						echo '<td style="text-align:center">
								<select style="width:100%;" name="'.$column_key.'">
									<option value=""></option>';
								foreach($chargeCodes as $chargeCode => $codeLabels) 
								{
                            		echo '<option value="'.$chargeCode.'" '.( (isset($_REQUEST[$column_key]) && $_REQUEST[$column_key] == $chargeCode) ? "selected='selected'" : "" ).'>'.$chargeCode.' - '. $codeLabels['name'] .'</option>';
								}
						echo '  </select>
							</td>';
					}
					else if($column_key == 'service')
					{
						echo '<td style="text-align:center">
								<select style="width:100%;" name="'.$column_key.'">
									<option value=""></option>
									<option value="international" '.( (isset($_REQUEST[$column_key]) && $_REQUEST[$column_key] == 'international') ? "selected='selected'" : "" ).'>Int</option>
									<option value="standard" '.( (isset($_REQUEST[$column_key]) && $_REQUEST[$column_key] == 'standard') ? "selected='selected'" : "" ).' >Std</option>
									<option value="express" '.( (isset($_REQUEST[$column_key]) && $_REQUEST[$column_key] == 'express') ? "selected='selected'" : "" ).' >Exp</option>';
						echo '  </select>
							</td>';
					}
					else
					{
						echo '<td><input class="new-input" type="text" name="'.$column_key.'" value="'.(isset($_REQUEST[$column_key]) ? $_REQUEST[$column_key] : '').'" /></td>';
					}
				}
				else
				{
					echo '<td>&nbsp;</td>';
				}
			}
			echo '</tr>';
		}
	}
	
	public function checkNonLinksync() {
		global $wpdb;
		$table = $wpdb->prefix ."linksynceparcel_nonlinksync";
		$sql = "SELECT count(*) as num FROM ". $table;
		$rows = $wpdb->get_row($sql);
		return $rows->num;
	}
	
	public function isInternationalServiceFilter($service) {
		global $wpdb;
		$table = $wpdb->prefix ."posts";
		$status_condition = ' post_status="wc-pending" OR post_status="wc-processing" OR post_status="wc-on-hold"';
		$sql = "SELECT ID FROM $table WHERE". $status_condition;
		$results = $wpdb->get_results($sql);
		if(!empty($results)) {
			$order_ids = array();
			foreach($results as $result) {
				$allowedChargeCodes = LinksynceparcelHelper::getEParcelChargeCodes();
				$chargecode = LinksynceparcelHelper::getOrderChargeCode($result->ID);
				$chargeCodeData = $allowedChargeCodes[$chargecode];
				if($chargeCodeData['serviceType'] == $service) {
					$order_ids[] = $result->ID;
				}
			}
			return !empty($order_ids)?$order_ids:false;
		}
		return false;
	}
}
$myListTable = new ConsignmentOrdersList();
?>