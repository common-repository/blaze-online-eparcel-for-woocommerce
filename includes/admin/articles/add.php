<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelAdminArticlesAdd
{
	public static function save()
	{
		$order_id = (int)($_REQUEST['order_id']);
		$number_of_articles = (int)sanitize_text_field($_POST['number_of_articles']);
		$data = $_POST;
		$order = new WC_Order( $order_id );
		$shipping_country = get_post_meta($order_id,'_shipping_country',true);
		$consignmentNumber = sanitize_text_field($_GET['consignment_number']);
		try
		{
			$old_consignmentNumber = $consignmentNumber;
			$articleData = LinksynceparcelHelper::prepareAddArticleData($data, $order, $consignmentNumber);
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
						
					update_option('linksynceparcel_order_view_success','The article has been added successfully.');
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
			$error = 'Failed to add article, Error: '.$e->getMessage();
			update_option('linksynceparcel_order_view_error',$error);
			LinksynceparcelHelper::log($error);
			wp_redirect(admin_url('post.php?post='.$order_id.'&action=edit'));
		}
	}
	
	public static function output()
	{
		global $is_greater_than_21;
		
		require_once(linksynceparcel_DIR.'model/ArticlePreset/Model.php' );
		$articlePreset = new ArticlePreset();
		$presets = $articlePreset->get_by(array('status' => 1));
		$order_id = (int)($_GET['order_id']);
		$order = new WC_Order( $order_id );
		if($is_greater_than_21)
		{
            $or_status = method_exists($order, 'get_status') ? $order->get_status() : $order->post_status;
			$order_status = substr($or_status,3);
		}
		else
		{
			$order_status = method_exists($order, 'get_status') ? $order->get_status() : $order->status;;
		}
		$consignmentNumber = sanitize_text_field($_GET['consignment_number']);
		$consignment = LinksynceparcelHelper::getConsignment($consignmentNumber);
		include_once(linksynceparcel_DIR.'views/admin/consignments/add_article.php');
	}
}
?>