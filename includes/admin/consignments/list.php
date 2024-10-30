<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelAdminConsignmentsList
{
	public static function output()
	{
		include_once(linksynceparcel_DIR.'model/Consignment/List.php');
		include_once(linksynceparcel_DIR.'views/admin/consignments/list.php');
	}
}
?>