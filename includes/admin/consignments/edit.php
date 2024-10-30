<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelAdminConsignmentsEdit
{
	public static function save()
	{
		$order_id = (int)($_REQUEST['order_id']);
		$data = $_REQUEST;
		$order = new WC_Order( $order_id );
		$consignmentNumber = sanitize_text_field($_GET['consignment_number']);
		$number_of_articles = sanitize_text_field($_REQUEST['number_of_articles']);
		$shipping_country = get_post_meta($order_id,'_shipping_country',true);
		$data['start_index'] = 1;
		$data['end_index'] = $number_of_articles;
		
		// Validate consignments fields
		$validateFields = LinksynceparcelValidator::requiredConsignmentsField();
		if($validateFields != false && $shipping_country != 'AU') {
			$errors = implode('<br>', $validateFields);
			update_option('linksynceparcel_order_view_error',$errors);
		} else {
			try
			{
				$old_consignmentNumber = $consignmentNumber;	
				$articleData = LinksynceparcelHelper::prepareArticleData($data, $order, $consignmentNumber, $shipping_country);
				if(!empty($articleData) && isset($articleData['error_msg'])) {
                    $error = $articleData['error_msg'];
                    update_option('linksynceparcel_order_view_error',$error);
                } else {
					$content = $articleData['content'];
					$chargeCode = $articleData['charge_code'];
					$total_weight = $articleData['total_weight'];
					$consignmentData = LinksynceparcelApi::modifyConsignment($content, $consignmentNumber, $chargeCode);
					if($consignmentData)
					{
						$new_consignmentNumber = $consignmentData->consignmentNumber;
						$manifestNumber = $consignmentData->manifestNumber;
						$consignmentPrice = $consignmentData->consignmentPriceSummary->total_cost;
						LinksynceparcelHelper::updateConsignment($order_id,$new_consignmentNumber,$data,$manifestNumber,$chargeCode,$total_weight,$shipping_country,$old_consignmentNumber,$consignmentPrice);
						LinksynceparcelHelper::updateArticles($order_id,$new_consignmentNumber,$consignmentData->articles,$data,$content,$old_consignmentNumber);
						LinksynceparcelHelper::insertManifest($manifestNumber);
						
						$labelContent = $consignmentData->lpsLabels->labels->label;
						LinksynceparcelHelper::generateDocument($new_consignmentNumber,$labelContent,'label');
						
						update_option('linksynceparcel_order_view_success',$data['consignment_number'].': consignment has been updated successfully.');
						wp_redirect(admin_url('post.php?post='.$order_id.'&action=edit'));
					}
					else
					{
						throw new Exception("modifyConsignment returned empty result");
					}
				}
			}
			catch(Exception $e)
			{
				$error = 'Cannot update consignment, Error: '.$e->getMessage();
				update_option('linksynceparcel_order_view_error',$error);
				LinksynceparcelHelper::log($error);
				wp_redirect(admin_url('post.php?post='.$order_id.'&action=edit'));
			}
		}
	}
	
	public static function output()
	{
		$order_id = (int)($_GET['order_id']);
		$order = new WC_Order( $order_id );
		$consignmentNumber = sanitize_text_field($_GET['consignment_number']);
		$consignment = LinksynceparcelHelper::getConsignment($consignmentNumber);
		$int_fields = LinksynceparcelHelper::getInternationFields($order_id, $consignmentNumber);
		$shipping_country = get_post_meta($order_id,'_shipping_country',true);
		$shipCountry = $consignment->delivery_country;
		if(empty($shipCountry)) {
			$shipCountry = $shipping_country;
		}
		$countries = WC()->countries->countries;
		include_once(linksynceparcel_DIR.'views/admin/consignments/edit.php');
	}
}
?>