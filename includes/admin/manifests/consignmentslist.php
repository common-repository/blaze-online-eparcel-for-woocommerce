<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelAdminManifestsConsignmentsList
{
	public static function output()
	{
		include_once(linksynceparcel_DIR.'model/Manifest/ConsignmentsList.php');
		include_once(linksynceparcel_DIR.'views/admin/manifests/consignmentslist.php');
	}
}
?>