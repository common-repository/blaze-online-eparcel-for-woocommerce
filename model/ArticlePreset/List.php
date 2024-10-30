<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once(linksynceparcel_DIR.'model/ArticlePreset/Model.php' );

class ArticlePresetList extends WP_List_Table
{
	public function __construct()
	{
    	global $status, $page;

        parent::__construct( 
			array(
				'singular'  => 'ArticlePreset',
				'plural'    => 'ArticlePresets',
				'ajax'      => false
    		)
		);
    }

	public function column_default( $item, $column_name )
	{
		switch( $column_name )
		{ 
			case 'id':
			case 'name':
			case 'status':
			case 'weight':
			case 'height':
			case 'width':
			case 'length':
				return (is_object($item) ? $item->$column_name : $item[$column_name]);
			default:
				return print_r( $item, true );
		}
	}

	public function get_columns()
	{
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => 'Preset',
            'status' => 'Status'
        );
		return $columns;
    }
	
	public function prepare_items()
	{
		global $wpdb;
		$articlePreset = new ArticlePreset();
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $articlePreset->get_all();
	}
	
	public function column_status($item)
	{
		return ($item->status == 1 ? 'Enabled' : 'Disabled');
	}
	
	public function column_name($item)
	{
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&subpage=%s&action=%s&id=%s">Edit</a>',$_REQUEST['page'],$_REQUEST['subpage'],'edit',(is_object($item) ? $item->id : $item['id'])),
			'delete' => sprintf('<a href="?page=%s&subpage=%s&action=%s&id=%s">Delete</a>',$_REQUEST['page'],$_REQUEST['subpage'],'delete',(is_object($item) ? $item->id : $item['id'])),
		);
		 
		return sprintf('%1$s (%2$skg - %3$sx%4$sx%5$s) %6$s',
			$item->name,
			$item->weight,
			$item->height,
			$item->width,
			$item->length,
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
$myListTable = new ArticlePresetList();
?>