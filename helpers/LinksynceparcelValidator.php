<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelValidator
{
    public static $capping_size = 50;

	public static function validateConfiguration($data)
	{
		$errors = array();
		if(empty($data['laid']))
			$errors[] = 'LAID is a required field.';
		if(empty($data['post_charge_to_account']))
			$errors[] = 'eParcel Post Charge to Account is a required field.';
		if(empty($data['st_apikey']))
			$errors[] = 'Shipping and Tracking API Key is a required field.';
		if(empty($data['st_password']))
			$errors[] = 'Shipping and Tracking password is a required field.';
		if(!empty($data['default_insurance_value']) && (!is_numeric($data['default_insurance_value']) || $data['default_insurance_value'] < 0))
			$errors[] = 'Default Insurance value is invalid.';

		if(isset($data['allowance_value']) && !empty($data['allowance_value']) && (!is_numeric($data['allowance_value']) || $data['allowance_value'] < 0))
			$errors[] = 'Packaging Allowance Value is invalid.';

		if(!empty($data['default_article_weight']) && (!is_numeric($data['default_article_weight']) || $data['default_article_weight'] < 0))
			$errors[] = 'Default Article Weight is invalid.';
		if(!empty($data['default_article_height']) && (!is_numeric($data['default_article_height']) || $data['default_article_height'] < 0))
			$errors[] = 'Default Article Height is invalid.';
		if(!empty($data['default_article_width']) && (!is_numeric($data['default_article_width']) || $data['default_article_width'] < 0))
			$errors[] = 'Default Article Width is invalid.';
		if(!empty($data['default_article_length']) && (!is_numeric($data['default_article_length']) || $data['default_article_length'] < 0))
			$errors[] = 'Default Article Length is invalid.';
		if(!empty($data['from_email_address']) && !is_email($data['from_email_address']))
			$errors[] = 'From email address is invalid.';
		if(isset($data['declared_value']) && $data['declared_value']==0) {
			if(!empty($data['declared_value_text']) && (!is_numeric($data['declared_value_text']) || $data['declared_value_text'] < 0))
				$errors[] = 'Order value as Declared Value is invalid.';
		}
		if(!empty($data['has_commercial_value']) && $data['has_commercial_value']==1) {
			if(empty($data['product_classification_text']))
				$errors[] = 'Classification Explanation is a required field.';
		}
		if(!empty($data['hs_tariff']))
			if(is_numeric($data['hs_tariff'])) {
				$count_digits = strlen($data['hs_tariff']);
				if($count_digits < 6 || $count_digits > 12)
					$errors[] = 'HS Tariffs must be between 6 - 12 digits.';
			} else {
				$errors[] = 'HS Tariffs must be a number.';
			}

		if(empty($data['default_contents']))
			$errors[] = 'Default Contents is a required field.';

		if(count($errors) > 0)
			return $errors;
		return false;
	}

	public static function validateArticlePresets($data)
	{
		$errors = array();
		if(empty($data['name']))
			$errors[] = 'Preset Name is a required field.';
		if(empty($data['weight']))
			$errors[] = 'Weight is a required field.';
		else if(!is_numeric($data['weight']) || $data['weight'] < 0)
			$errors[] = 'Weight is invalid.';
		if(!is_numeric($data['height']) || $data['height'] < 0)
			$errors[] = 'Height is invalid.';
		if(!is_numeric($data['width']) || $data['width'] < 0)
			$errors[] = 'Width is invalid.';
		if(!is_numeric($data['length']) || $data['length'] < 0)
			$errors[] = 'Length is invalid.';
		if(count($errors) > 0)
			return $errors;
		return false;
	}

	public static function validateAssignShippingTypes($data)
	{
		$errors = array();
		if(empty($data['method']))
			$errors[] = 'Method is a required field.';
		if(empty($data['charge_code']))
			$errors[] = 'Charge code is a required field.';
		if(count($errors) > 0)
			return $errors;
		return false;
	}

	public static function requiredConsignmentsField() {
		$errors = array();
		if((isset($_POST['has_commercial_value']) && $_POST['has_commercial_value'] == 1) || (isset($_POST['product_classification']) && $_POST['product_classification'] == 991)) {
			if(empty($_POST['product_classification_text']))
				$errors[] = '<strong>Product Classification text field</strong> is a required field.';
		}
		$country_origin = sanitize_text_field($_POST['country_origin']);
		if(!isset($country_origin)) {
			$country_origin = get_option('linksynceparcel_country_origin');
		}
		if(empty($country_origin))
			$errors[] = '<strong>Country of Origin</strong> is a required field.';

		if((isset($_POST['has_commercial_value']) && $_POST['has_commercial_value'] == 1) && empty($_POST['hs_tariff']))
			$errors[] = '<strong>HS Tarrif Number</strong> is a required field.';

		if(!empty($_POST['hs_tariff']))
			if(is_numeric($_POST['hs_tariff'])) {
				$count_digits = strlen($_POST['hs_tariff']);
				if($count_digits < 6 || $count_digits > 12)
					$errors[] = '<strong>HS Tariffs</strong> must be between 6 - 12 digits.';
			} else {
				$errors[] = '<strong>HS Tariffs</strong> must be a number.';
			}

		if(count($errors) > 0)
			return $errors;

		return false;
	}

	public static function validateInternationalCosignmentsValue($data, $chargecodedata, $country=false, $weight = false, $totalcost = false) {

		if($chargecodedata['serviceType'] == 'international' && $country == 'AU') {
			return array('error_msg' => 'International chargecode could not be use for domestic country. Please check and try again.');
		}

		if($chargecodedata['serviceType'] != 'international' && $country != 'AU') {
			return array('error_msg' => 'Domestic chargecode could not be use for international. Please check and try again.');
		}

		if(strlen($data['delivery_instruction']) > 128) {
			return array('error_msg' => 'Delivery Instructions should not reached the limit of 128 characters.');
		}

		if($country != 'AU') {
			if($data['number_of_articles'] > 1) {
				return array('error_msg' => 'International article cannot be more/less than 1.');
			}

			// All validated International Articles
			$intArticle = array(
				'Int. Economy Air' 	=> array('insurance' => 5000),
				'Int. Express Courier' => array('insurance' => 5000),
				'Int. Express Courier Document' => array('insurance' => 5000),
				'Int. Express Post' => array('insurance' => 5000),
				'Int. Pack & Track' => array('insurance' => 500),
				'Int. Registered' 	=> array('insurance' => 5000),
			);

			$label = $chargecodedata['labelType'];
			if($data['insurance'] == 1 && $intArticle[$label]['insurance'] < $data['insurance_value']) {
				return array('error_msg' => $chargecodedata['name'] .' reached the maximum insurance value of $'. number_format($intArticle[$label]['insurance'], 2) .'.');
			}
		}
		return true;
	}

	public static function validCountry() {
		$countries = array(
			'BE' => 'Belgium',
			'CA' => 'Canada',
			'CN' => 'China',
			'HR' => 'Croatia',
			'DK' => 'Denmark',
			'EE' => 'Estonia',
			'FR' => 'France',
			'DE' => 'Germany',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IE' => 'Ireland',
			'IL' => 'Israel',
			'KR' => 'Korea, Republic of (South Korea)',
			'LT' => 'Lithuania',
			'MY' => 'Malaysia',
			'MT' => 'Malta',
			'NL' => 'Netherlands',
			'NZ' => 'New Zealand',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'SG' => 'Singapore',
			'SI' => 'Slovenia',
			'ES' => 'Spain',
			'SE' => 'Sweden',
			'GB' => 'United Kingdom',
			'US' => 'USA'
		);

		return $countries;
	}

	public static function validateCombination($data, $combinations, $chargecode)
	{
		$delivery_signature = $data['delivery_signature_allowed'];
		$partial_delivery = $data['partial_delivery_allowed'];
		if($combinations) {
			if($combinations['delivery_signature_allowed'] != $delivery_signature || $combinations['partial_delivery_allowed'] != $partial_delivery) {
				$pda = ($combinations['partial_delivery_allowed']==1)?'Yes':'No';
				$dsa = ($combinations['delivery_signature_allowed']==1)?'Yes':'No';
				return array('error_msg' => 'You current chargecode <strong>'. $chargecode .'</strong> has invalid combination of data. Please make the <strong>Partial Delivery allowed?</strong> to <strong>'. $pda .'</strong> value and <strong>Delivery signature required?</strong> to <strong>'. $dsa .'</strong> value' );
			}
		}
		return true;
	}

	public static function validateArticlePresetDimensions($data, $bulk=false)
	{
		$start_index = $data['start_index'];
		$end_index = $data['end_index'];
		$error_message = '';
		for ($i=$start_index; $i <= $end_index; $i++) {
			if($data['articles_type'] == 'order_weight' && $bulk == true) {
				$article['description'] = "Order weight";
                $article['height'] = 5;
                $article['length'] = 5;
                $article['width'] = 5;
			} else {
				if($data['articles_type'] == 'Custom') {
	        		$article = $data['article'.$i];
				} else {
					$articles_type = $data['articles_type'];
	                $articles = self::get_article_preset($articles_type);

	                $article['description'] = $articles[0];
	                $article['height'] = trim($articles[2]);
	                $article['length'] = trim($articles[3]);
	                $article['width'] = trim($articles[4]);
	            }
			}

            $description = $article['description'];
            $height = $article['height'];
            $length = $article['length'];
            $width = $article['width'];

       		$notpass = array();
       		$limitdimension = array();
            if($height < 4) {
            	$notpass[] = $height;
            }
            if($height > 105) {
            	$limitdimension[] = $height;
            }
            if($length < 4) {
            	$notpass[] = $length;
            }
            if($length > 105) {
            	$limitdimension[] = $length;
            }
            if($width < 4) {
            	$notpass[] = $width;
            }
            if($width > 105) {
            	$limitdimension[] = $width;
            }

	        if(count($notpass) > 1) {
	        	$error_message .= $description .' must have atleast 2 dimensions of 5cm and above<br>';
	        }
	        if(count($limitdimension) > 0) {
        		$error_message .= $description .' height, length and width must not exceed the limit of 105cm.<br>';
	        }
		}

		if(!empty($error_message)) {
			return array('error_msg' => $error_message);
		}
		return true;
	}

	public static function get_article_preset($id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "linksynceparcel_article_preset";

        $query = "SELECT * FROM {$table_name} WHERE id = {$id}";

        $result = $wpdb->get_row($query);

        $data = array();
        if(!empty($result)) {
	        $data[] = $result->name;
	        $data[] = $result->weight;
	        $data[] = $result->height;
	        $data[] = $result->width;
	        $data[] = $result->length;
        } else {
        	$data[] = 0;
	        $data[] = 0;
	        $data[] = 0;
	        $data[] = 0;
	        $data[] = 0;
        }

        return $data;
    }

    public static function validateConsignmentLimit($message = null)
    {
        if(self::consignmentLessThan50()) {
            update_option('linksync_is_reached_capping_limit', false);
            update_option('linksync_capping_limit_message', '');
            update_option('linksync_capping_limit_kb', "");
            return false;
        }
        $laidinfo = LinksyncApiController::get_current_laid_info();
        $isFreeTrial = false;
        $laidinfo_data = array();
        if (isset($laidinfo['message'])) {
            $laidinfo_data = explode(',', $laidinfo['message']);
        } elseif (isset($laidinfo['userMessage'])) {
            $laidinfo_data = explode(',', $laidinfo['userMessage']);
        }
        $laid_arg = !empty($laidinfo_data[2])?$laidinfo_data[2]:'';
        $isFreeTrial = LinksyncUserHelper::isFreeTrial($laid_arg);

        if(! $isFreeTrial) {
            // var_dump("trial pa bes");
            return false;
        }
        $expectedMessage = 'Trial account exceed consignment limit - please upgrade to Premium plan';
        if($message != null && $message == $expectedMessage) {
            update_option('linksync_is_reached_capping_limit', true);
            update_option('linksync_capping_limit_message', $expectedMessage);
            update_option('linksync_capping_limit_kb', "https://help.linksync.com/hc/en-us/articles/115001095050-Free-Trial-Subscription");
            return array('error_msg' => $expectedMessage);
        }

        if(get_option('linksync_is_reached_capping_limit', false) == true) {
            return array('error_msg' => $expectedMessage);
        }

        return false;
    }

    public static function consignmentLessThan50()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "linksynceparcel_consignment";
        $sql = "select count(*) as count from $table_name";
        $result = $wpdb->get_row( $sql );
        return $result->count < self::$capping_size;
    }
}
?>
