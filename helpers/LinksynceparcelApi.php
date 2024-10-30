<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
set_time_limit(0);
ini_set( "soap.wsdl_cache_enabled", 0 );

if(!defined('LINKSYNC_EPARCEL_URL1'))
	define('LINKSYNC_EPARCEL_URL1','api.linksync.com');
if(!defined('LINKSYNC_EPARCEL_URL2'))
	define('LINKSYNC_EPARCEL_URL2','stg-api.linksync.com');
if(!defined('LINKSYNC_WSDL'))
	define('LINKSYNC_WSDL','/linksync/linksyncService');
if(!defined('LINKSYNC_DEBUG'))
	define('LINKSYNC_DEBUG',1);

class LinksynceparcelApi
{
	public static function getWebserviceUrl($next = false)
	{
		$url = 'https://';
		$url .= LINKSYNC_EPARCEL_URL1;
		$url .= LINKSYNC_WSDL;
		return $url;
	}

	public static function seteParcelMerchantDetails()
	{
		try
		{
			$arguments = array(
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');
			$merchant_location_id = get_option('linksynceparcel_merchant_location_id');
			$post_charge_to_account = get_option('linksynceparcel_post_charge_to_account');
			$sftp_username = '';
			$sftp_password = '';
			$lps_username = '';
			$lps_password = '';
			$operation_mode = get_option('linksynceparcel_operation_mode');
			$merchant_id = get_option('linksynceparcel_merchant_id');
			$lodgement_facility = get_option('linksynceparcel_lodgement_facility');

			if($operation_mode == 1)
			{
				$operation_mode = 'live';
			}
			else
			{
				$operation_mode = 'test';
			}

			$label_logo = '';
			$st_apikey = get_option('linksynceparcel_st_apikey');
			$st_password = get_option('linksynceparcel_st_password');
			$siteurl = LinksynceparcelHelper::get_linksync_site_url();
			$stdClass = $client->seteParcelMerchantDetails($laid,$merchant_location_id, $post_charge_to_account,$sftp_username,$sftp_password, $operation_mode, '', $merchant_id, $lodgement_facility, $label_logo, $lps_username, $lps_password, $siteurl, $st_apikey, $st_password );

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('seteParcelMerchantDetails Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('seteParcelMerchantDetails Response: '.$client->__getLastResponse());
				}

				// Account information
				$location_id = '';
				if(!empty($stdClass->MerchantDetails->MerchantLocationId)) {
					$location_id = $stdClass->MerchantDetails->MerchantLocationId;
				}
				$facility = '';
				if(!empty($stdClass->MerchantDetails->LodgementFacility)) {
					$facility = $stdClass->MerchantDetails->LodgementFacility;
				}

				// Return address
				$address_name = '';
				if(!empty($stdClass->MerchantDetails->ReturnAddress->ReturnName)) {
					$address_name = $stdClass->MerchantDetails->ReturnAddress->ReturnName;
				}
				$business_name = '';
				if(!empty($stdClass->MerchantDetails->ReturnAddress->ReturnCompanyName)) {
					$business_name = $stdClass->MerchantDetails->ReturnAddress->ReturnCompanyName;
				}
				$email_address = '';
				if(!empty($stdClass->MerchantDetails->ReturnAddress->ReturnEmailAddress)) {
					$email_address = $stdClass->MerchantDetails->ReturnAddress->ReturnEmailAddress;
				}
				$phone_number = '';
				if(!empty($stdClass->MerchantDetails->ReturnAddress->ReturnPhoneNumber)) {
					$phone_number = $stdClass->MerchantDetails->ReturnAddress->ReturnPhoneNumber;
				}
				$address_line1 = '';
				if(!empty($stdClass->MerchantDetails->ReturnAddress->ReturnAddressLine1)) {
					$address_line1 = $stdClass->MerchantDetails->ReturnAddress->ReturnAddressLine1;
				}
				$address_line2 = '';
				if(!empty($stdClass->MerchantDetails->ReturnAddress->ReturnAddressLine2)) {
					$address_line2 = $stdClass->MerchantDetails->ReturnAddress->ReturnAddressLine2;
				}
				$address_line3 = '';
				if(!empty($stdClass->MerchantDetails->ReturnAddress->ReturnAddressLine3)) {
					$address_line3 = $stdClass->MerchantDetails->ReturnAddress->ReturnAddressLine3;
				}
				$address_line4 = '';
				if(!empty($stdClass->MerchantDetails->ReturnAddress->ReturnAddressLine4)) {
					$address_line4 = $stdClass->MerchantDetails->ReturnAddress->ReturnAddressLine4;
				}
				$suburb = '';
				if(!empty($stdClass->MerchantDetails->ReturnAddress->ReturnSuburb)){
					$suburb = $stdClass->MerchantDetails->ReturnAddress->ReturnSuburb;
				}
				$postcode = '';
				if(!empty($stdClass->MerchantDetails->ReturnAddress->ReturnPostcode)) {
					$postcode = $stdClass->MerchantDetails->ReturnAddress->ReturnPostcode;
				}
				$statecode = '';
				if(!empty($stdClass->MerchantDetails->ReturnAddress->ReturnStateCode)) {
					$statecode = $stdClass->MerchantDetails->ReturnAddress->ReturnStateCode;
				}
				$countrycode = '';
				if(!empty($stdClass->MerchantDetails->ReturnAddress->ReturnCountryCode)) {
					$countrycode = $stdClass->MerchantDetails->ReturnAddress->ReturnCountryCode;
				}

				// Chargecodes
				$chargecodes = array();
				if(!empty($stdClass->MerchantDetails->ChargeCodes)) {
					$chargecodes = json_encode($stdClass->MerchantDetails->ChargeCodes);
				}

				$apiconfig = array(
					'merchant_location_id' => $location_id,
					'merchant_id' => '',
					'lodgement_facility' => $facility,
					'return_address_name' => $address_name,
					'return_business_name' => $business_name,
					'return_email_address' => $email_address,
					'return_phone_number' => $phone_number,
					'return_address_line1' => $address_line1,
					'return_address_line2' => $address_line2,
					'return_address_line3' => $address_line3,
					'return_address_line4' => $address_line4,
					'return_address_suburb' => $suburb,
					'return_address_postcode' => $postcode,
					'return_address_statecode' => $statecode,
					'return_address_countrycode' => $countrycode,
					'chargecodes' => $chargecodes,
				);

				LinksynceparcelHelper::log($apiconfig);
				LinksynceparcelHelper::saveConfiguration($apiconfig);

				return $stdClass;
			}
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('seteParcelMerchantDetails Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('seteParcelMerchantDetails Response: '.$client->__getLastResponse());
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('seteParcelMerchantDetails Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('seteParcelMerchantDetails Response: '.$client->__getLastResponse());
			}
			throw $e;
		}
	}

	public static function setReturnAddress()
	{
		try
		{
			$arguments = array(
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$returnAddress = array();
			$returnAddress['returnAddressLine1'] = trim(get_option('linksynceparcel_return_address_line1'));
			$returnAddress['returnAddressLine2'] = trim(get_option('linksynceparcel_return_address_line2'));
			$returnAddress['returnAddressLine3'] = trim(get_option('linksynceparcel_return_address_line3'));
			$returnAddress['returnAddressLine4'] = trim(get_option('linksynceparcel_return_address_line4'));
			$returnAddress['returnCountryCode'] = 'AU';
			$returnAddress['returnName'] = trim(get_option('linksynceparcel_return_address_name'));
			$returnAddress['returnPostcode'] = trim(get_option('linksynceparcel_return_address_postcode'));
			$returnAddress['returnStateCode'] = trim(get_option('linksynceparcel_return_address_statecode'));
			$returnAddress['returnSuburb'] = trim(get_option('linksynceparcel_return_address_suburb'));
			$returnAddress['returnCompanyName'] = trim(get_option('linksynceparcel_return_business_name'));
			$returnAddress['returnEmailAddress'] = trim(get_option('linksynceparcel_return_email_address'));
			$returnAddress['returnPhoneNumber'] = trim(get_option('linksynceparcel_return_phone_number'));
			$laid = get_option('linksynceparcel_laid');

			$stdClass = $client->setReturnAddress($laid,$returnAddress);

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('setReturnAddress Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('setReturnAddress Response: '.$client->__getLastResponse());
				}
				return $stdClass;
			}
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('setReturnAddress Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('setReturnAddress Response: '.$client->__getLastResponse());
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('setReturnAddress Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('setReturnAddress Response: '.$client->__getLastResponse());
			}
			throw $e;
		}
	}

	public static function sendLog()
	{
		try
		{
			$arguments = array(
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');
			$filename = linksynceparcel_LOG_DIR.'linksynceparcel_log_'.date('Ymdhis').'.zip';

			if(LinksynceparcelHelper::createZip(linksynceparcel_LOG_DIR.'linksynceparcel.log',$filename))
			{
				$stdClass = $client->sendLogFile($laid,file_get_contents($filename));

				if($stdClass)
				{
					if(LINKSYNC_DEBUG == 1)
					{
						LinksynceparcelHelper::log('Send Log File  Response: '.$client->__getLastResponse());
					}
					return $stdClass;
				}

				if(LINKSYNC_DEBUG == 1 && $client)
				{
					LinksynceparcelHelper::log('Send Log File  Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('Send Log File  Response: '.$client->__getLastResponse());
				}
			}
			else
			{
				throw new Exception('Failed to create archive file');
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('Send Log File  Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('Send Log File  Response: '.$client->__getLastResponse());
			}
			throw $e;
		}
	}

	public static function isAddressValid($address)
	{
		try
		{
			$city = trim($address['city']);
			$state = trim($address['state']);
			$postcode = trim($address['postcode']);
			$arguments = array(
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');
			$addressParams = array('suburb' => $city, 'postcode' => $postcode, 'stateCode' => $state);
			$stdClass = $client->isAddressValid($laid,$addressParams);
			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('isAddressValid Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('isAddressValid Response: '.$client->__getLastResponse());
				}
				return 1;
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('isAddressValid Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('isAddressValid Response: '.$client->__getLastResponse());
			}
			return $e->getMessage();
		}
	}

	public static function getConsignmentPrice($article)
	{
		try
		{
			$arguments = array(
				'connection_timeout' => 500000,
				'cache_wsdl' => WSDL_CACHE_BOTH,
				'keep_alive' => false,
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			LinksynceparcelHelper::log('Articles: '.preg_replace('/\s+/', ' ', trim($article)));
			
			$laid = get_option('linksynceparcel_laid');
			$siteurl = LinksynceparcelHelper::get_linksync_site_url();
			$stdClass = $client->getConsignmentPrice($laid,$article,$siteurl);

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('getConsignmentPrice Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('getConsignmentPrice Response: '.$client->__getLastResponse());
				}
				return $stdClass;
			}

		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('getConsignmentPrice Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('getConsignmentPrice Response: '.$client->__getLastResponse());
			}
			return $e->getMessage();
		}
	}

	public static function createConsignment($article,$loop=0,$chargeCode=false,$bulk=false)
	{
		if($loop < 2)
		{
			try
			{
				$arguments = array(
					'connection_timeout' => 500000,
					'cache_wsdl' => WSDL_CACHE_BOTH,
					'keep_alive' => false,
					"stream_context" => stream_context_create(
			            array(
			                'ssl' => array(
			                    'verify_peer'       => false,
			                    'verify_peer_name'  => false,
			                )
			            )
			        )
				);
				if(LINKSYNC_DEBUG == 1)
				{
					$arguments['trace'] = 1;
				}
				$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

				LinksynceparcelHelper::log('Articles: '.preg_replace('/\s+/', ' ', trim($article)));

				$chargeCodeData = LinksynceparcelHelper::getEParcelChargeCodes();
				$codeData = $chargeCodeData[$chargeCode];
				if($codeData['serviceType'] == 'international') {
					$arg3 = 'A4-1pp';
					$arg4 = 'true';
					$arg5 = 0;
					$arg6 = 0;
				} else {
					$service = get_option('linksynceparcel_'. $codeData['key'] .'_label');
					$labelType = explode('_', $service);
					$arg3 = $labelType[0];
					$arg4 = ($labelType[1]==0)?'false':'true';
					$arg5 = get_option('linksynceparcel_'. $codeData['key'] .'_left_offset');
					$arg6 = get_option('linksynceparcel_'. $codeData['key'] .'_right_offset');
				}

				$laid = get_option('linksynceparcel_laid');

				if($bulk) {
					$bulk = 'true';
				}
				
				$siteurl = LinksynceparcelHelper::get_linksync_site_url();
				$stdClass = $client->createConsignment2($laid,$article,$siteurl,$arg3,$arg4,$arg5,$arg6,$bulk);

				if($stdClass)
				{
					if(LINKSYNC_DEBUG == 1)
					{
						LinksynceparcelHelper::log('createConsignment Request: '.$client->__getLastRequest());
						LinksynceparcelHelper::log('createConsignment Response: '.$client->__getLastResponse());
					}
					return $stdClass;
				}
			}
			catch(Exception $e)
			{
				if(LINKSYNC_DEBUG == 1 && $client)
				{
					LinksynceparcelHelper::log('createConsignment Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('createConsignment Response: '.$client->__getLastResponse());
				}
				LinksynceparcelHelper::log('createConsignment Error catch from API class: '.$e->getMessage());
				throw $e;
			}
		}
	}

	public static function modifyConsignment($article,$consignmentNumber,$chargeCode)
	{
		try
		{
			$arguments = array(
				'connection_timeout' => 500000,
				'cache_wsdl' => WSDL_CACHE_BOTH,
				'keep_alive' => false,
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			LinksynceparcelHelper::log('Modified Articles: '.preg_replace('/\s+/', ' ', trim($article)));

			$chargeCodeData = LinksynceparcelHelper::getEParcelChargeCodes();
			$codeData = $chargeCodeData[$chargeCode];
			if($codeData['serviceType'] == 'international') {
				$arg4 = 'A4-1pp';
				$arg5 = 'true';
				$arg6 = 0;
				$arg7 = 0;
			} else {
				$service = get_option('linksynceparcel_'. $codeData['key'] .'_label');
				$labelType = explode('_', $service);
				$arg4 = $labelType[0];
				$arg5 = ($labelType[1]==0)?'false':'true';
				$arg6 = get_option('linksynceparcel_'. $codeData['key'] .'_left_offset');
				$arg7 = get_option('linksynceparcel_'. $codeData['key'] .'_right_offset');
			}
			
			$laid = get_option('linksynceparcel_laid');
			$siteurl = LinksynceparcelHelper::get_linksync_site_url();
			$stdClass = $client->modifyConsignment2($laid,$consignmentNumber,$article,$siteurl,$arg4,$arg5,$arg6,$arg7);

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					//LinksynceparcelHelper::log('modifyConsignment Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('modifyConsignment Response: '.$client->__getLastResponse());
				}
				return $stdClass;
			}
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('modifyConsignment Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('modifyConsignment Response: '.$client->__getLastResponse());
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('modifyConsignment Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('modifyConsignment Response: '.$client->__getLastResponse());
			}
			throw $e;
		}
	}

	public static function unAssignConsignment($consignmentNumber)
	{
		try
		{
			$arguments = array(
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');

			$stdClass = $client->unAssignConsignment($laid,$consignmentNumber);

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('unAssignConsignment Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('unAssignConsignment Response: '.$client->__getLastResponse());
				}
				return $stdClass;
			}
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('unAssignConsignment Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('unAssignConsignment Response: '.$client->__getLastResponse());
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('unAssignConsignment Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('unAssignConsignment Response: '.$client->__getLastResponse());
			}
			throw $e;
		}
	}

	public static function deleteConsignment($consignmentNumber)
	{
		try
		{
			$arguments = array(
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');
			$siteurl = LinksynceparcelHelper::get_linksync_site_url();
			$stdClass = $client->deleteConsignment($laid,$consignmentNumber,$siteurl);

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('deleteConsignment Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('deleteConsignment Response: '.$client->__getLastResponse());
				}
				return $stdClass;
			}
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('deleteConsignment Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('deleteConsignment Response: '.$client->__getLastResponse());
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('deleteConsignment Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('deleteConsignment Response: '.$client->__getLastResponse());
			}
			throw $e;
		}
	}

	public static function getLabelsByConsignments($consignments,$chargeCode)
	{
		try
		{
			$arguments = array(
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');

			$chargeCodeData = LinksynceparcelHelper::getEParcelChargeCodes();
			$codeData = $chargeCodeData[$chargeCode];
			if($codeData['serviceType'] == 'international') {
				$arg3 = 'A4-1pp';
				$arg4 = 'true';
				$arg5 = 0;
				$arg6 = 0;
			} else {
				$service = get_option('linksynceparcel_'. $codeData['key'] .'_label');
				$labelTypeService = explode('_', $service);
				$arg3 = $labelTypeService[0];
				$arg4 = ($labelTypeService[1]==0)?'false':'true';
				$arg5 = get_option('linksynceparcel_'. $codeData['key'] .'_left_offset');
				$arg6 = get_option('linksynceparcel_'. $codeData['key'] .'_right_offset');
			}

			$stdClass = $client->getLabelsByConsignments($laid,explode(',',$consignments),$arg3,$arg4,$arg5,$arg6);

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('getLabelsByConsignments  Request: '.$client->__getLastRequest());
					//LinksynceparcelHelper::log('getLabelsByConsignments  Response: '.$client->__getLastResponse());
				}
				return $stdClass;
			}

			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('getLabelsByConsignments  Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('getLabelsByConsignments  Response: '.$client->__getLastResponse());
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('getLabelsByConsignments  Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('getLabelsByConsignments  Response: '.$client->__getLastResponse());
			}
			throw $e;
		}
	}

	public static function getReturnLabelsByConsignments($consignments)
	{
		try
		{
			$arguments = array(
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');
			$labelType = get_option('linksynceparcel_label_format');

			$stdClass = $client->getReturnLabelsByConsignments($laid,explode(',',$consignments),$labelType);

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('getReturnLabelsByConsignments  Request: '.$client->__getLastRequest());
					//LinksynceparcelHelper::log('getReturnLabelsByConsignments  Response: '.$client->__getLastResponse());
				}
				return $stdClass;
			}

			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('getReturnLabelsByConsignments  Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('getReturnLabelsByConsignments  Response: '.$client->__getLastResponse());
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('getReturnLabelsByConsignments  Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('getReturnLabelsByConsignments  Response: '.$client->__getLastResponse());
			}
			throw $e;
		}
	}

	public static function getManifest()
	{
		try
		{
			$arguments = array(
				'connection_timeout' => 500000,
				'cache_wsdl' => WSDL_CACHE_BOTH,
				'keep_alive' => false,
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');

			$stdClass = $client->getManifest($laid);

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('getManifest Request: '.$client->__getLastRequest());
					//LinksynceparcelHelper::log('getManifest Response: '.$client->__getLastResponse());
				}
				return $stdClass;
			}

			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('getManifest Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('getManifest Response: '.$client->__getLastResponse());
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('getManifest Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('getManifest Response: '.$client->__getLastResponse());
			}
			throw $e;
		}
	}

	public static function printManifest($manifestNumber)
	{
		try
		{
			$arguments = array(
				'connection_timeout' => 500000,
				'cache_wsdl' => WSDL_CACHE_BOTH,
				'keep_alive' => false,
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');
			$stdClass = $client->printManifest($laid,$manifestNumber);
			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('printManifest  Request: '.$client->__getLastRequest());
					//LinksynceparcelHelper::log('printManifest  Response: '.$client->__getLastResponse());
				}
				return $stdClass;
			}

			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('printManifest  Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('printManifest  Response: '.$client->__getLastResponse());
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('printManifest  Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('printManifest  Response: '.$client->__getLastResponse());
			}
			throw $e;
		}
	}

	public static function assignConsignmentToManifest($consignmentNumber)
	{
		try
		{
			$arguments = array(
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');

			$stdClass = $client->assignConsignmentToManifest($laid,$consignmentNumber);

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('assignConsignmentToManifest Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('assignConsignmentToManifest Response: '.$client->__getLastResponse());
				}
				return $stdClass;
			}
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('assignConsignmentToManifest Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('assignConsignmentToManifest Response: '.$client->__getLastResponse());
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('assignConsignmentToManifest Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('assignConsignmentToManifest Response: '.$client->__getLastResponse());
			}
			throw $e;
		}
	}

	public static function getVersionNumber()
	{
		try
		{
			$arguments = array(
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');
			$stdClass = $client->getVersionNumber($laid);

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('getVersionNumber Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('getVersionNumber Response: '.$client->__getLastResponse());
				}
				return $stdClass;
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('getVersionNumber Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('getVersionNumber Response: '.$client->__getLastResponse());
			}
			return $e->getMessage();
		}
	}

	public static function getNotDespatchedConsignments()
	{
		try
		{
			$arguments = array(
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');

			$stdClass = $client->getNotDespatchedConsignments($laid);

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('getNotDespatchedConsignments  Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('getNotDespatchedConsignments  Response: '.$client->__getLastResponse());
				}
				return $stdClass->consignments;
			}

			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('getNotDespatchedConsignments  Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('getNotDespatchedConsignments  Response: '.$client->__getLastResponse());
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				@LinksynceparcelHelper::log('getNotDespatchedConsignmentsResponse  Request: '.$client->__getLastRequest());
				@LinksynceparcelHelper::log('getNotDespatchedConsignmentsResponse  Response: '.$client->__getLastResponse());
			}
			return $e->getMessage();
		}
	}

	public static function despatchManifest()
	{
		try
		{
			$arguments = array(
				'connection_timeout' => 500000,
				'cache_wsdl' => WSDL_CACHE_BOTH,
				'keep_alive' => false,
				"stream_context" => stream_context_create(
		            array(
		                'ssl' => array(
		                    'verify_peer'       => false,
		                    'verify_peer_name'  => false,
		                )
		            )
		        )
			);
			if(LINKSYNC_DEBUG == 1)
			{
				$arguments['trace'] = 1;
			}
			$client = new SoapClient(self::getWebserviceUrl(true).'?WSDL',$arguments);

			$laid = get_option('linksynceparcel_laid');
			$siteurl = LinksynceparcelHelper::get_linksync_site_url();
			$stdClass = $client->despatchManifest($laid,$siteurl);

			if($stdClass)
			{
				if(LINKSYNC_DEBUG == 1)
				{
					LinksynceparcelHelper::log('despatchManifest  Request: '.$client->__getLastRequest());
					LinksynceparcelHelper::log('despatchManifest  Response: '.$client->__getLastResponse());
				}
				return $stdClass;
			}

			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('despatchManifest  Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('despatchManifest  Response: '.$client->__getLastResponse());
			}
		}
		catch(Exception $e)
		{
			if(LINKSYNC_DEBUG == 1 && $client)
			{
				LinksynceparcelHelper::log('despatchManifest  Request: '.$client->__getLastRequest());
				LinksynceparcelHelper::log('despatchManifest  Response: '.$client->__getLastResponse());
			}
			throw $e;
		}
	}

	public static function removeEncodedData($string,$tags)
	{
		return preg_replace('#<(' . implode( '|', $tags) . ')(?:[^>]+)?>.*?</\1>#s', '', $string);
	}
}
?>
