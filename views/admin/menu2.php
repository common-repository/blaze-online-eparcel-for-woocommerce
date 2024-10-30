<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$titles = array();
$titles['consignments-search'] = 'eParcel Consignments Search';
$titles['manifests'] = 'eParcel Manifests View';
$titles['article-presets'] = 'eParcel Article Presets';
$titles['assign-shipping-types'] = 'eParcel Assign Shipping Types';
$titles['configuration'] = 'eParcel Configuration';
$titles['add-article'] = 'eParcel Add Article';
$titles['edit-consignment'] = 'eParcel Edit Consignment';
$titles['edit-article'] = 'eParcel Edit Article';

$page = isset($_REQUEST['page']) ? $_REQUEST['page']: '';
$subPage = isset($_REQUEST['subpage']) ? $_REQUEST['subpage']: '';
$title = !empty($subPage) ? $titles[$subPage] : '';

$currentPage = '';

if(!empty($page) && $page == 'linksynceparcel')
{
	$currentPage = 'admin.php?page=linksynceparcel';
	if(!empty($subPage))
	{
		$currentPage .= '&subpage='.$subPage;
	}
}
?>
<div id="menu-linksync-holder" data-title="<?php echo $title; ?>" data-currentpage="<?php echo $currentPage; ?>"></div>