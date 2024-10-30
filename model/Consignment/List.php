<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once(linksynceparcel_DIR.'model/Consignment/Model.php' );

class ConsignmentList extends WP_List_Table
{
	public function __construct()
	{
    	global $status, $page;

        parent::__construct( 
			array(
				'singular'  => 'Consignment',
				'plural'    => 'Consignments',
				'ajax'      => false
    		)
		);
    }

	public function column_default( $item, $column_name )
	{
		switch( $column_name )
		{ 
			case 'order_id':
			case 'consignment_number':
			case 'add_date':
			case 'label':
			case 'number_of_articles':
			case 'width':
			case 'length':
				return (is_object($item) ? $item->$column_name : $item[$column_name]);
			default:
				return print_r( $item, true );
		}
	}
	
	public function column_order_id($item)
	{
		$order_id = $item->order_id;
		$order = new WC_Order( $order_id );
		$html = '<a href="'.admin_url('post.php?post='.$order_id.'&action=edit').'" >'.$order->get_order_number().'</a>';
		return $html;
	}
	
	public function column_customer_name($item)
	{
		$meta = get_post_meta($item->order_id);
		return $meta['_shipping_first_name'][0].' '.$meta['_shipping_last_name'][0];
	}
	
	public function column_state($item)
	{
		$meta = get_post_meta($item->order_id);
		return $meta['_shipping_state'][0];
	}
	
	public function column_postcode($item)
	{
		$meta = get_post_meta($item->order_id);
		return $meta['_shipping_postcode'][0];
	}
	
	public function column_country($item)
	{
		$countries = WC()->countries->countries;
		$meta = get_post_meta($item->order_id);
		return $countries[$meta['_shipping_country'][0]];
	}
	
	public function column_despatch_date($item)
	{
		return $item->despatch_date;
	}
	
	public function column_label($item)
	{
		$meta = get_post_meta($item->order_id);
		if($meta['_shipping_country'][0] == "AU") {
			$html = '<a class="print_label" lang="'.$item->consignment_number.'" href="'. admin_url() .'?f_key='. $item->consignment_number .'&f_type=consignment" target="_blank" >View</a>';
		} else {
			if(!empty($item->label)) {
				$html = '<a class="print_label" lang="int'.$item->consignment_number.'" href="'. admin_url() .'?f_key='. $item->consignment_number .'&f_type=consignment" target="_blank" >View</a>';
			} else {
				$html = '<a href="javascript:void(0)" >---</a>';
			}
		}
		return $html;
	}
	
	public function column_track($item)
	{
		$html = '<a href="http://auspost.com.au/track/track.html?id='.$item->consignment_number.'" target="_blank" >Click</a>';
		return $html;
	}
	
	public function column_number_of_articles($item)
	{
		return '<div style="text-align:center">'.$item->number_of_articles.'</div>';
	}

	public function get_columns()
	{
        $columns = array(
            'order_id' => 'Order No.',
			'customer_name' => 'Customer Name',
			'state' => 'State',
			'postcode' => 'Postcode',
			'country' => 'Country',
			'consignment_number' => 'Consignment Number',
			'add_date' => 'Created Date',
			'despatch_date' => 'Despatch Date',
			'number_of_articles' => 'No. of Articles',
			'label' => 'Label',
			'track' => 'Track',
        );
		return $columns;
    }
	
	public function prepare_items()
	{
		global $wpdb;
		$consignment = new Consignment();
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		
		$joinFields = ',despatch_date';
		$join = ' LEFT JOIN '.$wpdb->prefix.'linksynceparcel_manifest lsm ON main_table.manifest_number=lsm.manifest_number';
		
		$joinFields .= ',COUNT(article_number) as number_of_articles';
		$join .= ' LEFT JOIN '.$wpdb->prefix.'linksynceparcel_article lsa ON main_table.consignment_number=lsa.consignment_number';
		
		$where = '';
		if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id']))
		{
			$order_id = (int)($_REQUEST['order_id']);
			$where .= ' AND main_table.order_id = '.$order_id;
		}
		if(isset($_REQUEST['customer_name']) && !empty($_REQUEST['customer_name']))
		{
			$customer_name = ($_REQUEST['customer_name']);
			$consignmentNumbers = array();
			$tempData = $consignment->get_all(NULL,$join,$joinFields,$where,'main_table.consignment_number');
			foreach($tempData as $key => $dataItem) 
			{
				$consignment_number = $dataItem->consignment_number;
				$orderId =  $dataItem->order_id;
				$meta = get_post_meta($orderId);

				$firstname =  $meta['_shipping_first_name'][0];
				$lastname =  $meta['_shipping_last_name'][0];
				$fullname = $firstname.' '.$lastname;
			
				if (preg_match('/'.$customer_name.'/i',$fullname))
					$consignmentNumbers[] = '"'.$consignment_number.'"';
			}
			
			if (count($consignmentNumbers) > 0) 
				$where .= ' AND main_table.consignment_number IN('.implode(',',$consignmentNumbers).')';
			else 
				$where .= ' AND main_table.consignment_number IN(-1)';
		}
		if(isset($_REQUEST['state']) && !empty($_REQUEST['state']))
		{
			$state = ($_REQUEST['state']);
			$consignmentNumbers = array();
			$tempData = $consignment->get_all(NULL,$join,$joinFields,$where,'main_table.consignment_number');
			foreach($tempData as $key => $dataItem) 
			{
				$consignment_number = $dataItem->consignment_number;
				$orderId =  $dataItem->order_id;
				$meta = get_post_meta($orderId);
				$state2 =  $meta['_shipping_state'][0];
				if (preg_match('/'.$state.'/i',$state2))
					$consignmentNumbers[] = '"'.$consignment_number.'"';
			}
			
			if (count($consignmentNumbers) > 0) 
				$where .= ' AND main_table.consignment_number IN('.implode(',',$consignmentNumbers).')';
			else 
				$where .= ' AND main_table.consignment_number IN(-1)';
		}
		if(isset($_REQUEST['postcode']) && !empty($_REQUEST['postcode']))
		{
			$postcode = ($_REQUEST['postcode']);
			$consignmentNumbers = array();
			$tempData = $consignment->get_all(NULL,$join,$joinFields,$where,'main_table.consignment_number');
			foreach($tempData as $key => $dataItem) 
			{
				$consignment_number = $dataItem->consignment_number;
				$orderId =  $dataItem->order_id;
				$meta = get_post_meta($orderId);
				$postcode2 =  $meta['_shipping_postcode'][0];
				if (preg_match('/'.$postcode.'/i',$postcode2))
					$consignmentNumbers[] = '"'.$consignment_number.'"';
			}
			
			if (count($consignmentNumbers) > 0) 
				$where .= ' AND main_table.consignment_number IN('.implode(',',$consignmentNumbers).')';
			else 
				$where .= ' AND main_table.consignment_number IN(-1)';
		}
		if(isset($_REQUEST['country']) && !empty($_REQUEST['country']))
		{
			$country = ($_REQUEST['country']);
			$consignmentNumbers = array();
			$tempData = $consignment->get_all(NULL,$join,$joinFields,$where,'main_table.consignment_number');
			foreach($tempData as $key => $dataItem) 
			{
				$consignment_number = $dataItem->consignment_number;
				$orderId =  $dataItem->order_id;
				$meta = get_post_meta($orderId);
				$country2 =  $meta['_shipping_country'][0];
				if (preg_match('/'.$country.'/i',$country2))
					$consignmentNumbers[] = '"'.$consignment_number.'"';
			}
			
			if (count($consignmentNumbers) > 0) 
				$where .= ' AND main_table.consignment_number IN('.implode(',',$consignmentNumbers).')';
			else 
				$where .= ' AND main_table.consignment_number IN(-1)';
		}
		if(isset($_REQUEST['consignment_number']) && !empty($_REQUEST['consignment_number']))
		{
			$consignment_number = ($_REQUEST['consignment_number']);
			$where .= ' AND main_table.consignment_number like "%'.$consignment_number.'%"';
		}
		if(isset($_REQUEST['add_date_from']) && !empty($_REQUEST['add_date_from']))
		{
			$add_date_from = ($_REQUEST['add_date_from']);
			$where .= ' AND main_table.add_date >= "'.$add_date_from.' 00:00:00"';
		}
		if(isset($_REQUEST['add_date_to']) && !empty($_REQUEST['add_date_to']))
		{
			$add_date_to = ($_REQUEST['add_date_to']);
			$where .= ' AND main_table.add_date <= "'.$add_date_to.' 23:59:59"';
		}
		if(isset($_REQUEST['despatch_date_from']) && !empty($_REQUEST['despatch_date_from']))
		{
			$despatch_date_from = ($_REQUEST['despatch_date_from']);
			$where .= ' AND lsm.despatch_date >= "'.$despatch_date_from.' 00:00:00"';
		}
		if(isset($_REQUEST['despatch_date_to']) && !empty($_REQUEST['despatch_date_to']))
		{
			$despatch_date_to = ($_REQUEST['despatch_date_to']);
			$where .= ' AND lsm.despatch_date <= "'.$despatch_date_to.' 23:59:59"';
		}
		if(isset($_REQUEST['number_of_articles']) && !empty($_REQUEST['number_of_articles']))
		{
			$number_of_articles = (int)($_REQUEST['number_of_articles']);
			
			$consignmentNumbers = array();
			$tempData = $consignment->get_all(NULL,$join,$joinFields,$where,'main_table.consignment_number');
			foreach($tempData as $key => $dataItem) 
			{
				$consignment_number = $dataItem->consignment_number;
				$orderId =  $dataItem->order_id;
				$articles = LinksynceparcelHelper::getArticles($orderId, $consignment_number);
				$totalArticles = count($articles);
				
				if ($totalArticles == $number_of_articles) 
					$consignmentNumbers[] = '"'.$consignment_number.'"';
			}
			
			if (count($consignmentNumbers) > 0) 
				$where .= ' AND main_table.consignment_number IN('.implode(',',$consignmentNumbers).')';
			else 
				$where .= ' AND main_table.consignment_number IN(-1)';
		}
		
		$orderBy = 'main_table.order_id desc';
		if(isset($_GET['orderby']) && !empty($_GET['orderby']))
		{
			if($_GET['orderby'] == 'order_id')
			{
				$orderBy = 'main_table.order_id '.$_GET['order'];
			}
			else
			{
				$orderBy = '';
			}
		}

		$data = $consignment->get_all($orderBy,$join,$joinFields,$where,'main_table.consignment_number');

		if(isset($_GET['orderby']) && !empty($_GET['orderby']))
		{
			if( !($_GET['orderby'] == 'order_id') )
			{
				usort( $data, array( &$this, 'usort_reorder' ) );
			}
		}
		
		$per_page = 20;
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
		'order_id'  => array('order_id',false),
		'customer_name' => array('customer_name',false),
		'state'   => array('state',false),
		'postcode'  => array('postcode',false),
		'country'  => array('country',false),
		'consignment_number' => array('consignment_number',false),
		'created_date'   => array('created_date',false),
		'despatch_date' => array('despatch_date',false),
		'no_of_articles'   => array('no_of_articles',false),
	  );
	  return $sortable_columns;
	}
	
	function usort_reorder( $a, $b ) 
	{
	  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'order_id';
	  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
	  $result = isset($a->$orderby) ? strcmp( $a->$orderby, $b->$orderby ) : $_REQUEST[$orderby];
	  return ( $order === 'asc' ) ? $result : -$result;
	}
	
	public function column_status($item)
	{
		return ($item->status == 1 ? 'Enabled' : 'Disabled');
	}
	public function extra_tablenav($which)
	{
?>
        <div class="alignleft actions">
        <?php
			if ( 'top' == $which)
			{
				//add_filter( 'parse_query', 'ba_admin_posts_filter' );
				echo '<a href="'.admin_url('admin.php?page=linksynceparcel&subpage=consignments-search').'" class="button">Reset Filter</a>';
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
				if($column_key != 'track' && $column_key != 'label')
				{
					if($column_key == 'add_date' || $column_key == 'despatch_date')
					{
						echo '<td>
								<input class="new-input datepicker" type="text" name="'.$column_key.'_from" value="'.(isset($_REQUEST[$column_key.'_from']) ? $_REQUEST[$column_key.'_from'] : '').'" />
								<br/>
								<input class="new-input datepicker" type="text" name="'.$column_key.'_to" value="'.(isset($_REQUEST[$column_key.'_to']) ? $_REQUEST[$column_key.'_to'] : '').'" />
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
}
$myListTable = new ConsignmentList();
?>