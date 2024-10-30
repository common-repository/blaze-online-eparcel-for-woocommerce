<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once(linksynceparcel_DIR.'model/Manifest/Model.php' );

class ManifestList extends WP_List_Table
{
	public function __construct()
	{
    	global $status, $page;

        parent::__construct( 
			array(
				'singular'  => 'Manifest',
				'plural'    => 'Manifests',
				'ajax'      => false
    		)
		);
    }

	public function column_default( $item, $column_name )
	{
		switch( $column_name )
		{ 
			case 'manifest_id':
			case 'manifest_number':
			case 'despatch_date':
			case 'label':
			case 'number_of_articles':
			case 'number_of_consignments':
				return (is_object($item) ? $item->$column_name : $item[$column_name]);
			default:
				return print_r( $item, true );
		}
	}
	
	public function column_manifest_number($item)
	{
		$manifest_number = $item->manifest_number;
		$despatch_date = $item->despatch_date;
		$despatch_mode = $item->despatch_mode;

		$status = '';
		if($despatch_mode == 0 && !empty($despatch_date)) {
			$status = ' (Test Mode)';
		}
		$html = '<a href="'.admin_url('admin.php?page=linksynceparcel&subpage=manifests&action=list-consignments&manifest_number='.$manifest_number).'">'.$manifest_number.'</a>'. $status;
		return $html;
	}

	public function column_despatch_date($item)
	{
		return $item->despatch_date;
	}
	
	public function column_label($item)
	{
		if($item->label)
		{
			$manifestpdf = linksynceparcel_UPLOAD_BASEURL .'manifest/'. $item->label;
			$manifestpdf_check = linksynceparcel_UPLOAD_DIR .'manifest/'. $item->label;
			if(!file_exists($manifestpdf_check)) {
				$manifestpdf = linksynceparcel_URL .'assets/label/manifest/'. $item->label;
			}
			$html = '<a class="print_label" lang="'.$item->label.'" href="'. admin_url() .'?f_key='. $item->manifest_number .'&f_type=manifest" target="_blank" >View</a>';
		}
		else
		{
			$html = '&nbsp;';
		}
		return $html;
	}

	public function column_total_cost($item)
	{
		global $wpdb;
		$manifest_number = $item->manifest_number;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$query = "SELECT SUM(shipping_cost) as total_cost FROM {$table_name} WHERE manifest_number = '{$manifest_number}'";
		$result = $wpdb->get_row($query);
		return '<p>$'. number_format($result->total_cost, 2) .'</p>';
	}
	
	public function column_cb($item)
	{
	 	return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], (is_object($item) ? $item->manifest_number : $item['id']));
	}
	
	public function get_bulk_actions()
	{
		$actions = array(
			'generateLabel' => 'Generate Manifest Summary'
		);
		return $actions;
	}
	
	public function get_columns()
	{
        $columns = array(
			'cb' => '<input type="checkbox" />',
            'manifest_number' => 'Manifest Number',
			'despatch_date' => 'Despatch Date',
			'number_of_consignments' => 'No. of Consignments',
			'number_of_articles' => 'No. of Articles',
			'label' => 'Manifest Summary',
			'total_cost' => 'Total Manifest Cost',
        );
		return $columns;
    }
	
	public function prepare_items()
	{
		global $wpdb;
		
		if(get_option('linksynceparcel_manifest_sync') == 1)
		{
			LinksynceparcelHelper::getManifestNumber();
		}
		
		$manifest = new Manifest();
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		
		$where = '';
		if(isset($_REQUEST['manifest_number']) && !empty($_REQUEST['manifest_number']))
		{
			$manifest_number = ($_REQUEST['manifest_number']);
			$where .= ' AND main_table.manifest_number like "%'.$manifest_number.'%"';
		}
		if(isset($_REQUEST['despatch_date_from']) && !empty($_REQUEST['despatch_date_from']))
		{
			$despatch_date_from = ($_REQUEST['despatch_date_from']);
			$where .= ' AND main_table.despatch_date >= "'.$despatch_date_from.' 00:00:00"';
		}
		if(isset($_REQUEST['despatch_date_to']) && !empty($_REQUEST['despatch_date_to']))
		{
			$despatch_date_to = ($_REQUEST['despatch_date_to']);
			$where .= ' AND main_table.despatch_date <= "'.$despatch_date_to.' 23:59:59"';
		}
		$join = $joinFields = '';
		$data = $manifest->get_all(NULL,$join,$joinFields,$where);

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
	
	function get_sortable_columns()
	{
	  $sortable_columns = array(
		'manifest_number'  => array('manifest_number',false),
		'despatch_date' => array('despatch_date',false)
	  );
	  return $sortable_columns;
	}
	
	function usort_reorder( $a, $b ) 
	{
	  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'manifest_number';
	  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
	  $result = ( isset($a->$orderby) && isset($b->$orderby) ) ? strcmp( $a->$orderby, $b->$orderby ) : '';
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
				echo '<a href="'.admin_url('admin.php?page=linksynceparcel&subpage=manifests').'" class="button">Reset Filter</a>';
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
				if($column_key != 'number_of_consignments' && $column_key != 'label' && $column_key != 'number_of_articles' && $column_key != 'cb' && $column_key != 'total_cost')
				{
					if($column_key == 'despatch_date')
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
$myListTable = new ManifestList();
?>