<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelAdminConsignmentsCreateMass
{
	public static function save()
	{
		$ids = $_REQUEST['order'];
		$data = $_REQUEST;
		try
		{
			if(is_array($ids))
			{
				$success = 0;
				foreach ($ids as $id)
				{
					$values = explode('_',$id);
					$orderId = (int)($values[0]);
					$order = new WC_Order($orderId);

					// Render all defaults
					if(get_option('linksynceparcel_copy_order_notes') == 1)
					{
						$ordernotes = $order->customer_message;
						$data['delivery_instruction'] = $ordernotes;
					}
					$data['order_value_declared_value'] = get_option('linksynceparcel_order_value_declared_value');
					$data['maximum_declared_value'] = get_option('linksynceparcel_maximum_declared_value');
					$data['fixed_declared_value'] = get_option('linksynceparcel_fixed_declared_value');

					$shipping_country = get_post_meta($orderId,'_shipping_country',true);
					$consignmentNumber = $values[1];
					$incrementId = $orderId;

					// Validate consignments fields
					$validateFields = LinksynceparcelValidator::requiredConsignmentsField();
					if($validateFields != false && $shipping_country != 'AU') {
						$errors = implode('<br>', $validateFields);
						update_option('linksynceparcel_consignment_error',$errors);
					} else {
						if($shipping_country == 'AU' && !LinksynceparcelHelper::getAddressValid($orderId))
						{
							$error = sprintf('Order #%s: Please validate the address before creating consignment', $incrementId);
							LinksynceparcelHelper::addMessage('linksynceparcel_consignment_error',$error);
						}
						else
						{
							try
							{
								if($data['partial_delivery_allowed'])
								{
									if(LinksynceparcelHelper::isDisablePartialDeliveryMethod($orderId))
									{
										$data['partial_delivery_allowed'] = 0;
									}
								}
								$isCappingReachedLimit = LinksynceparcelValidator::validateConsignmentLimit();
		                        if (!empty($isCappingReachedLimit) && isset($isCappingReachedLimit['error_msg'])) {
		                            $error = $isCappingReachedLimit['error_msg'];
		                            update_option('linksynceparcel_order_view_error',$error);
		                        } else {
		                        	$articleData = LinksynceparcelHelper::prepareArticleDataBulk($data, $order, $shipping_country);
									if(!empty($articleData) && isset($articleData['error_msg'])) {
										$error = $articleData['error_msg'];
										update_option('linksynceparcel_consignment_error',$error);
									} else {
										$content = $articleData['content'];
										$chargeCode = $articleData['charge_code'];
										$total_weight = $articleData['total_weight'];
										$consignmentData = LinksynceparcelApi::createConsignment($content, 0, $chargeCode, true);
										if($consignmentData)
										{
											$consignmentNumber = $consignmentData->consignmentNumber;
											$manifestNumber = $consignmentData->manifestNumber;
										 	$consignmentPrice = $consignmentData->consignmentPriceSummary->total_cost;
											LinksynceparcelHelper::insertConsignment($orderId,$consignmentNumber,$data,$manifestNumber,$chargeCode,$total_weight,$shipping_country,$consignmentPrice);
											LinksynceparcelHelper::updateArticles($orderId,$consignmentNumber,$consignmentData->articles,$data,$content);
											LinksynceparcelHelper::insertManifest($manifestNumber);
											
											$successmsg = sprintf('Order #%s: Consignment #%s created successfully', $incrementId,$consignmentNumber);
											LinksynceparcelHelper::addMessage('linksynceparcel_consignment_success',$successmsg);
										}
										else
										{
											$error = sprintf('Order #%s: Failed to create consignment',$incrementId);
											LinksynceparcelHelper::addMessage('linksynceparcel_consignment_error',$error);
										}
									}
		                        }
							}
							catch (Exception $e)
							{
								LinksynceparcelValidator::validateConsignmentLimit($e->getMessage());
								$error = sprintf('Consignment #%s, Error: %s', $consignmentNumber, $e->getMessage());
								LinksynceparcelHelper::addMessage('linksynceparcel_consignment_error',$error);
							}
						}
					}
				}
			}
			else
			{
				throw new Exception("Please select items");
			}
		}
		catch (Exception $e)
		{
			LinksynceparcelHelper::addMessage('linksynceparcel_consignment_error',$e->getMessage());
			LinksynceparcelHelper::deleteManifest();
		}
		echo 1;
	}
}
?>
