<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelAdminConfiguration
{
	public static function output()
	{
		global $is_greater_than_21;
		if (isset($_POST['submitConfiguration']) && check_admin_referer( 'linksync_configuration_action', 'linksync_configuration_nonce_field' ) ) {
			if(!isset($_POST['declared_value'])) {
				$_POST['declared_value'] = 0;
			}
			if(!isset($_POST['has_commercial_value'])) {
				$_POST['has_commercial_value'] = 0;
			}
			if(!isset($_POST['product_classification'])) {
				$_POST['product_classification'] = 991;
			}
			$errors = LinksynceparcelValidator::validateConfiguration($_POST);
			if($errors)
			{
				$error = implode('<br/>',$errors);
			}
			else
			{
				LinksynceparcelHelper::saveConfiguration($_POST);
				
				$result = __( 'Configuration updated successfully.', 'linksynceparcel' );
				
				$openManifest = LinksynceparcelHelper::checkOpenManifest();
				if(!empty($openManifest)) {
					$error = 'You have current open manifest <strong>'. $openManifest .'</strong>';
				} else {
					
					try
					{
						LinksynceparcelApi::seteParcelMerchantDetails();
						$result .= '<br/>'.__( 'eParcel Merchant Details updated successfully.', 'linksynceparcel' );
					}
					catch(Exception $e)
					{
						$message = 'Updating Merchant Details, Error:'.$e->getMessage();
						$error = $message;
						LinksynceparcelHelper::log($message);
					}
				}
			}
		}
				
		$statuses = LinksynceparcelHelper::getOrderStatuses();
		$states = LinksynceparcelHelper::getStates();
		$countries = LinksynceparcelHelper::getWooCountries();
		$formats = LinksynceparcelHelper::getLabelFormats();
		
		include_once(linksynceparcel_DIR.'views/admin/configuration.php');
	}
}
?>