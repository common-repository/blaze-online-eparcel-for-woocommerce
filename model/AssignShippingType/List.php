<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once(linksynceparcel_DIR.'model/AssignShippingType/Model.php' );

class AssignShippingTypeList extends WP_List_Table
{
	public function __construct()
	{
    	global $status, $page;

        parent::__construct( 
			array(
				'singular'  => 'AssignShippingType',
				'plural'    => 'AssignShippingTypes',
				'ajax'      => false
    		)
		);
    }

	public function column_default( $item, $column_name )
	{
		switch( $column_name )
		{ 
			case 'id':
			case 'method':
			case 'charge_code':
				return (is_object($item) ? $item->$column_name : $item[$column_name]);
			default:
				return print_r( $item, true );
		}
	}
	
	public function column_charge_code($item) {
		$chargeCodes = LinksynceparcelHelper::getChargeCodeValues(true);
		$chargeCode = $chargeCodes[$item->charge_code];
		return $item->charge_code .' - '. $chargeCode['name'];
	}

	public function get_columns()
	{
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'method' => 'Shipping Method',
			'charge_code' => 'Charge Code'
        );
		return $columns;
    }
	
	public function prepare_items()
	{
		global $wpdb;
		$assignShippingType = new AssignShippingType();
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $assignShippingType->get_all();
	}
	
	public function column_method($item)
	{
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&subpage=%s&action=%s&id=%s">Edit</a>',$_REQUEST['page'],$_REQUEST['subpage'],'edit',(is_object($item) ? $item->id : $item['id'])),
			'delete' => sprintf('<a href="?page=%s&subpage=%s&action=%s&id=%s">Delete</a>',$_REQUEST['page'],$_REQUEST['subpage'],'delete',(is_object($item) ? $item->id : $item['id'])),
		);
		
		$methodname = (is_object($item) ? ucwords(str_replace('_',' ',$item->method)) : $item['method']);
		if(is_numeric($item->method)) {
			$instanceid = $item->method;
			$method_arr = LinksynceparcelHelper::get_shipping_method_name($instanceid);
			$methodname = !empty($method_arr['title'])?$method_arr['title']:'Shipping Instance '.$instanceid;
		} else {
			$pass = LinksynceparcelHelper::requiredWooVersion();
			if(is_object($item) && $pass) {
				$method = explode(':', $item->method);
				if(isset($method[1])) {
					$name = LinksynceparcelHelper::getShippingMethodName($method[1], $method[0]);
					if($name != false) {
						$methodname = $method[0] .' => '. $name;
					}
				}
			}
		}
		 
		return sprintf('%1$s %2$s',
			$methodname,
			$this->row_actions($actions)
		);
	}
	
	public function column_cb($item)
	{
	 	return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], (is_object($item) ? $item->id : $item['id']));
	}
	
	public function get_bulk_actions()
	{
		$actions = array(
			'delete' => 'Delete'
		);
		return $actions;
	}
}
$myListTable = new AssignShippingTypeList();
?>