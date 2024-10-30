<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once(linksynceparcel_DIR.'model/Consignment/Model.php' );

class ConsignmentsList extends WP_List_Table
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
			case 'shipping_cost':
			case 'number_of_articles':
			case 'label':
				return (is_object($item) ? $item->$column_name : $item[$column_name]);
			default:
				return print_r( $item, true );
		}
	}
	
	public function column_order_id($item)
	{
		$order_id = $item->order_id;
		$order = new WC_Order( $order_id );
		$html = '<a href="'.admin_url('post.php?post='.$order_id.'&action=edit').'">'.$order->get_order_number().'</a>';
		return $html;
	}

	public function column_label($item)
	{
		$order_id = $item->order_id;
		$order_link = admin_url('?f_key='. $item->consignment_number .'&f_type=consignment');
		$consignmentpdf_check = linksynceparcel_UPLOAD_DIR .'consignment/'. $item->consignment_number .'.pdf';
		if(!file_exists($consignmentpdf_check)) {
			$order_link = admin_url('post.php?post='.$order_id.'&action=edit');
		}
		$html = '<a class="print_label" lang="'.$item->consignment_number.'" href="'. $order_link .'" target="_blank" >View</a>';
		return $html;
	}
	
	public function column_shipping_cost($item)
	{
		$cost_amount = $item->shipping_cost;
		$cost = number_format($cost_amount, 2);
		$html = '<p>$'. $cost .'</p>';
		return $html;
	}
	
	public function column_number_of_articles($item)
	{
		$articles = LinksynceparcelHelper::getArticles($item->order_id, $item->consignment_number);
		return count($articles);
	}
	
	public function get_columns()
	{
        $columns = array(
            'order_id' => 'Order No.',
			'consignment_number' => 'Consignment Number',
			'shipping_cost' => 'Consignment Cost',
			'number_of_articles' => 'No. of Articles',
			'label' => 'Label'
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
		$join = $joinFields = '';
		$where = '';
		if(isset($_REQUEST['manifest_number']) && !empty($_REQUEST['manifest_number']))
		{
			$manifest_number = ($_REQUEST['manifest_number']);
			$where .= ' AND main_table.manifest_number like "%'.$manifest_number.'%"';
		}
		
		$data = $consignment->get_all('consignment_number',$join,$joinFields,$where);
		usort( $data, array( &$this, 'usort_reorder' ) );
		
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
	
	function usort_reorder( $a, $b ) 
	{
	  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'consignment_number';
	  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
	  $result = strcmp( $a->$orderby, $b->$orderby );
	  return ( $order === 'asc' ) ? $result : -$result;
	}
}
$myListTable = new ConsignmentsList();
?>