<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelAdminConsignmentsCreate
{
	public static function save()
	{
		$order_id = (int)sanitize_text_field($_POST['post_ID']);
		$shipping_country = get_post_meta($order_id,'_shipping_country',true);
		if($shipping_country != 'AU') {
			$_POST['number_of_articles'] = 1;
		}
		$number_of_articles = (int)sanitize_text_field($_POST['number_of_articles']);
		$data = $_POST;
		$order = new WC_Order( $order_id );
		$tempCanConsignments = (int)($number_of_articles/20);
		$canConsignments = $tempCanConsignments;
		$remainArticles = $number_of_articles % 20;
		
		// Check if already create consignment
		$isCreated = LinksynceparcelHelper::checkConsignmentProcessExist($data['date_process']);
		if(!$isCreated) {
			// Validate consignments fields
			$validateFields = LinksynceparcelValidator::requiredConsignmentsField();
			if($validateFields != false && $shipping_country != 'AU') {
				$errors = implode('<br>', $validateFields);
				update_option('linksynceparcel_order_view_error',$errors);
			} else {
				if( $remainArticles > 0)
				{
					$canConsignments++;
				}
				
				for($i=0;$i<$canConsignments;$i++)
				{
					$data['start_index'] = ($i * 20 ) + 1;
					if( ($i+1) <= $tempCanConsignments)
					{
						$data['end_index'] = ($i * 20 ) + 20;
					}
					else
					{
						$data['end_index'] = ($i * 20 ) + $remainArticles;
					}
					
					try
					{
                        $isCappingReachedLimit = LinksynceparcelValidator::validateConsignmentLimit();
                        if(!empty($isCappingReachedLimit) && isset($isCappingReachedLimit['error_msg'])) {
                            $error = $isCappingReachedLimit['error_msg'];
                            update_option('linksynceparcel_order_view_error',$error);
                        } else {
                            $articleData = LinksynceparcelHelper::prepareArticleData($data, $order, '', $shipping_country);
                            if(!empty($articleData) && isset($articleData['error_msg'])) {
                                $error = $articleData['error_msg'];
                                update_option('linksynceparcel_order_view_error',$error);
                            } else {
                                $content = $articleData['content'];
                                $chargeCode = $articleData['charge_code'];
                                $consignmentData = LinksynceparcelApi::createConsignment($content, 0, $chargeCode);
                                $total_weight = $articleData['total_weight'];
                                if($consignmentData)
                                {
                                    $consignmentNumber = $consignmentData->consignmentNumber;
                                    $manifestNumber = $consignmentData->manifestNumber;
                                    $consignmentPrice = $consignmentData->consignmentPriceSummary->total_cost;
                                    LinksynceparcelHelper::insertConsignment($order_id,$consignmentNumber,$data,$manifestNumber,$chargeCode,$total_weight,$shipping_country,$consignmentPrice);
                                    LinksynceparcelHelper::updateArticles($order_id,$consignmentNumber,$consignmentData->articles,$data,$content);
                                    LinksynceparcelHelper::insertManifest($manifestNumber);
                                    
                                    $labelContent = $consignmentData->lpsLabels->labels->label;
                                    LinksynceparcelHelper::generateDocument($consignmentNumber,$labelContent,'label');
                                    
                                    update_option('linksynceparcel_order_view_success','The consignment has been created successfully.');
                                }
                                else
                                {
                                    throw new Exception("createConsignment returned empty result");
                                }
                            }
                        }
					}
					catch(Exception $e)
					{

                        LinksynceparcelValidator::validateConsignmentLimit($e->getMessage());
						$error = 'Cannot create consignment, Error: '.$e->getMessage();
						update_option('linksynceparcel_order_view_error',$error);
						LinksynceparcelHelper::log($error);
					}
				}
			}
		}
	}
	
	public static function saveOrderWeight()
	{
		$order_id = (int)sanitize_text_field($_POST['post_ID']);
        $shipping_country = get_post_meta($order_id,'_shipping_country',true);
        if($shipping_country != 'AU') {
            $_POST['number_of_articles'] = 1;
        }
        $number_of_articles = (int)sanitize_text_field($_POST['number_of_articles']);
        $data = $_POST;
        $order = new WC_Order( $order_id );
        $tempCanConsignments = (int)($number_of_articles/20);
        $canConsignments = $tempCanConsignments;
        $remainArticles = $number_of_articles % 20;
        
        // Check if already create consignment
        $isCreated = LinksynceparcelHelper::checkConsignmentProcessExist($data['date_process']);
        if(!$isCreated) {
            if( $remainArticles > 0)
            {
                $canConsignments++;
            }
            
            for($i=0;$i<$canConsignments;$i++)
            {
                $data['start_index'] = ($i * 20 ) + 1;
                if( ($i+1) <= $tempCanConsignments)
                {
                    $data['end_index'] = ($i * 20 ) + 20;
                }
                else
                {
                    $data['end_index'] = ($i * 20 ) + $remainArticles;
                }
                
                try
                {
                    $isCappingReachedLimit = LinksynceparcelValidator::validateConsignmentLimit();
                    if(!empty($isCappingReachedLimit) && isset($isCappingReachedLimit['error_msg'])) {
                        $error = $isCappingReachedLimit['error_msg'];
                        update_option('linksynceparcel_order_view_error',$error);
                    } else {
                        $articleData = LinksynceparcelHelper::prepareOrderWeightArticleData($data, $order, '', $shipping_country);
                        if(!empty($articleData) && isset($articleData['error_msg'])) {
                            $error = $articleData['error_msg'];
                            update_option('linksynceparcel_order_view_error',$error);
                        } else {
	                        $content = $articleData['content'];
	                        $chargeCode = $articleData['charge_code'];
	                        $total_weight = $articleData['total_weight'];
	                        $consignmentData = LinksynceparcelApi::createConsignment($content, 0, $chargeCode);

	                        if($consignmentData)
	                        {
	                            $consignmentNumber = $consignmentData->consignmentNumber;
	                            $manifestNumber = $consignmentData->manifestNumber;
	                            $consignmentPrice = $consignmentData->consignmentPriceSummary->total_cost;
	                            LinksynceparcelHelper::insertConsignment($order_id,$consignmentNumber,$data,$manifestNumber,$chargeCode,$total_weight,$shipping_country,$consignmentPrice);
	                            LinksynceparcelHelper::updateArticles($order_id,$consignmentNumber,$consignmentData->articles,$data,$content);
	                            LinksynceparcelHelper::insertManifest($manifestNumber);
	                    
	                            $labelContent = $consignmentData->lpsLabels->labels->label;
	                            LinksynceparcelHelper::generateDocument($consignmentNumber,$labelContent,'label');
	                                
	                            update_option('linksynceparcel_order_view_success','The consignment has been created successfully.');
	                        }
	                        else
	                        {
	                            throw new Exception("createConsignment returned empty result");
	                        }
                        }
                    }
				}
				catch(Exception $e)
				{
                    LinksynceparcelValidator::validateConsignmentLimit($e->getMessage());
					$error = 'Cannot create consignment, Error: '.$e->getMessage();
					update_option('linksynceparcel_order_view_error',$error);
					LinksynceparcelHelper::log($error);
				}
			}
		}
	}
	
	public static function saveDefaultWeight()
	{
		$order_id = (int)sanitize_text_field($_POST['post_ID']);
		$shipping_country = get_post_meta($order_id,'_shipping_country',true);
		if($shipping_country != 'AU') {
			$_POST['number_of_articles'] = 1;
		}
		$number_of_articles = (int)sanitize_text_field($_POST['number_of_articles']);
		$data = $_POST;
		$order = new WC_Order( $order_id );
		$tempCanConsignments = (int)($number_of_articles/20);
		$canConsignments = $tempCanConsignments;
		$remainArticles = $number_of_articles % 20;
		
		// Check if already create consignment
		$isCreated = LinksynceparcelHelper::checkConsignmentProcessExist($data['date_process']);
		if(!$isCreated) {
			if( $remainArticles > 0)
			{
				$canConsignments++;
			}
			
			for($i=0;$i<$canConsignments;$i++)
			{
				$data['start_index'] = ($i * 20 ) + 1;
				if( ($i+1) <= $tempCanConsignments)
				{
					$data['end_index'] = ($i * 20 ) + 20;
				}
				else
				{
					$data['end_index'] = ($i * 20 ) + $remainArticles;
				}
				
				try
				{
                    $isCappingReachedLimit = LinksynceparcelValidator::validateConsignmentLimit();
                    if(!empty($isCappingReachedLimit) && isset($isCappingReachedLimit['error_msg'])) {
                        $error = $isCappingReachedLimit['error_msg'];
                        update_option('linksynceparcel_order_view_error',$error);
                    } else {
                        $articleData = LinksynceparcelHelper::prepareOrderWeightArticleData($data, $order, '', $shipping_country);
                        if(!empty($articleData) && isset($articleData['error_msg'])) {
                            $error = $articleData['error_msg'];
                            update_option('linksynceparcel_order_view_error',$error);
                        } else {
	                        $content = $articleData['content'];
	                        $chargeCode = $articleData['charge_code'];
	                        $total_weight = $articleData['total_weight'];
	                        $consignmentData = LinksynceparcelApi::createConsignment($content, 0, $chargeCode);

	                        if($consignmentData)
	                        {
	                            $consignmentNumber = $consignmentData->consignmentNumber;
	                            $manifestNumber = $consignmentData->manifestNumber;
	                            $consignmentPrice = $consignmentData->consignmentPriceSummary->total_cost;
	                            LinksynceparcelHelper::insertConsignment($order_id,$consignmentNumber,$data,$manifestNumber,$chargeCode,$total_weight,$shipping_country,$consignmentPrice);
	                            LinksynceparcelHelper::updateArticles($order_id,$consignmentNumber,$consignmentData->articles,$data,$content);
	                            LinksynceparcelHelper::insertManifest($manifestNumber);
	                    
	                            $labelContent = $consignmentData->lpsLabels->labels->label;
	                            LinksynceparcelHelper::generateDocument($consignmentNumber,$labelContent,'label');
	                            
	                            update_option('linksynceparcel_order_view_success','The consignment has been created successfully.');
	                        }
	                        else
	                        {
	                            throw new Exception("createConsignment returned empty result");
	                        }
                        }
                    }
				}
				catch(Exception $e)
				{
                    LinksynceparcelValidator::validateConsignmentLimit($e->getMessage());
					$error = 'Cannot create consignment, Error: '.$e->getMessage();
					update_option('linksynceparcel_order_view_error',$error);
					LinksynceparcelHelper::log($error);
				}
			}
		}
	}
}
?>