<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class LinksynceparcelAdminMenu
{
	public static function output($obj)
	{
		global $current_user,$linksynceparcel_consignment_menu;
		$user_roles = $current_user->roles;
		$role = trim($user_roles[0]);
		if($role == 'shop_manager')
		{
			$main_page = add_menu_page( __('eParcel Consignment View', 'linksynceparcel'),__('linksync eParcel', 'linksynceparcel'), 'shop_manager', 'linksynceparcel', array(&$obj,'consignments'), linksynceparcel_URL.'assets/images/logo.png', '55.6' );
			$linksynceparcel_consignment_menu = $main_page;
			add_submenu_page( 'linksynceparcel', __( 'eParcel Consignments Search', 'linksynceparcel' ),  __( 'Consignments Search', 'linksynceparcel' ) , 'shop_manager','admin.php?page=linksynceparcel&subpage=consignments-search', null );
			add_submenu_page( 'linksynceparcel', __( 'eParcel Manifests View', 'linksynceparcel' ),  __( 'Manifests', 'linksynceparcel' ) , 'shop_manager','admin.php?page=linksynceparcel&subpage=manifests', null );
			add_submenu_page( 'linksynceparcel', __( 'eParcel Article Presets', 'linksynceparcel' ),  __( 'Article Presets', 'linksynceparcel' ) , 'shop_manager','admin.php?page=linksynceparcel&subpage=article-presets', null );
			add_submenu_page( 'linksynceparcel', __( 'Assign Shipping Types', 'linksynceparcel' ),  __( 'Assign Shipping Types', 'linksynceparcel' ) , 'shop_manager','admin.php?page=linksynceparcel&subpage=assign-shipping-types', null );
			add_submenu_page( 'linksynceparcel', __( 'eParcel Configuration', 'linksynceparcel' ),  __( 'Configuration', 'linksynceparcel' ) , 'shop_manager','admin.php?page=linksynceparcel&subpage=configuration', null );
		}
		else
		{
			 $main_page = add_menu_page( __('eParcel Consignment View', 'linksynceparcel'),__('linksync eParcel', 'linksynceparcel'), 'administrator', 'linksynceparcel', array(&$obj,'consignments'), linksynceparcel_URL.'assets/images/logo.png', '55.6' );
			$linksynceparcel_consignment_menu = $main_page;
			
			add_submenu_page( 'linksynceparcel', __( 'eParcel Consignments Search', 'linksynceparcel' ),  __( 'Consignments Search', 'linksynceparcel' ) , 'administrator','admin.php?page=linksynceparcel&subpage=consignments-search', null );
			add_submenu_page( 'linksynceparcel', __( 'eParcel Manifests View', 'linksynceparcel' ),  __( 'Manifests', 'linksynceparcel' ) , 'administrator','admin.php?page=linksynceparcel&subpage=manifests', null );
			add_submenu_page( 'linksynceparcel', __( 'eParcel Article Presets', 'linksynceparcel' ),  __( 'Article Presets', 'linksynceparcel' ) , 'administrator','admin.php?page=linksynceparcel&subpage=article-presets', null );
			add_submenu_page( 'linksynceparcel', __( 'Assign Shipping Types', 'linksynceparcel' ),  __( 'Assign Shipping Types', 'linksynceparcel' ) , 'administrator','admin.php?page=linksynceparcel&subpage=assign-shipping-types', null );
			add_submenu_page( 'linksynceparcel', __( 'eParcel Configuration', 'linksynceparcel' ),  __( 'Configuration', 'linksynceparcel' ) , 'administrator','admin.php?page=linksynceparcel&subpage=configuration', null );
		}
	}
}
?>