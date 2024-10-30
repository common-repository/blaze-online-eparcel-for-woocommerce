<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
require(linksynceparcel_DIR.'helpers/LinksynceparcelValidator.php');
require(linksynceparcel_DIR.'helpers/LinksynceparcelApi.php');
class LinksynceparcelHelper
{
	public static function createTables()
	{
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

   		$table_name = $wpdb->prefix . "linksynceparcel_address_valid";
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			  order_id int(10) PRIMARY KEY,
			  is_address_valid TINYINT(1) DEFAULT '0'
		  );";
		dbDelta( $sql );

		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$sql = "CREATE TABLE $table_name (
			 	`order_id` int(11) NOT NULL default '0',
				`consignment_number` varchar(128) CHARACTER SET utf8 NOT NULL,
				`add_date` varchar(40) CHARACTER SET utf8 NOT NULL,
				`modify_date` varchar(40) CHARACTER SET utf8 NOT NULL,
				`delivery_signature_allowed` varchar(255) CHARACTER SET utf8 ,
				`print_return_labels` varchar(255) CHARACTER SET utf8 NOT NULL,
				`contains_dangerous_goods` varchar(255) CHARACTER SET utf8 NOT NULL,
				`partial_delivery_allowed` varchar(255) CHARACTER SET utf8 NOT NULL,
				`cash_to_collect` varchar(255) CHARACTER SET utf8 NOT NULL,
				`despatched` tinyint(1) NOT NULL DEFAULT '0',
				`label` varchar(255) CHARACTER SET utf8,
				`manifest_number` varchar(255) CHARACTER SET utf8,
				`is_next_manifest` tinyint(1) NOT NULL DEFAULT '0',
				`is_label_printed` tinyint(1) NOT NULL DEFAULT '0',
				`is_label_created` tinyint(1) NOT NULL DEFAULT '0',
				`email_notification` tinyint(1) DEFAULT '0',
				`notify_customers` tinyint(1) DEFAULT '0',
				`is_return_label_printed` tinyint(1) DEFAULT '0',
				`chargecode` varchar(255) CHARACTER SET utf8
		  );";
		dbDelta( $sql );

		$table_name = $wpdb->prefix . "linksynceparcel_article";
		$sql = "CREATE TABLE $table_name (
			 	`order_id` int(11) NOT NULL default '0',
				`consignment_number` varchar(255) CHARACTER SET utf8 NOT NULL,
				`article_number` varchar(255) CHARACTER SET utf8 NOT NULL,
				`actual_weight` varchar(255) CHARACTER SET utf8 NOT NULL,
				`article_description` varchar(255) CHARACTER SET utf8 NOT NULL,
				`cubic_weight` varchar(255) CHARACTER SET utf8 NOT NULL,
				`height` varchar(255) CHARACTER SET utf8 NOT NULL,
				`is_transit_cover_required` varchar(255) CHARACTER SET utf8 NOT NULL,
				`transit_cover_amount` varchar(255) CHARACTER SET utf8 NOT NULL,
				`length` varchar(40) CHARACTER SET utf8 NOT NULL,
				`width` varchar(255) CHARACTER SET utf8
		  );";
		dbDelta( $sql );

		$table_name = $wpdb->prefix . "linksynceparcel_manifest";
		$sql = "CREATE TABLE $table_name (
			 	`manifest_id` int(11) NOT NULL auto_increment primary key,
				`manifest_number` varchar(255) CHARACTER SET utf8 NOT NULL,
				`despatch_date` varchar(40) CHARACTER SET utf8 NOT NULL,
				`label` varchar(255) CHARACTER SET utf8 NOT NULL,
				`number_of_articles` int(11) NOT NULL,
				`number_of_consignments` int(11) NOT NULL
		  );";
		dbDelta( $sql );

		$table_name = $wpdb->prefix . "linksynceparcel_article_preset";
		$sql = "CREATE TABLE $table_name (
			 	`id` int(11) NOT NULL auto_increment,
				`name` varchar(255) CHARACTER SET utf8 NOT NULL,
				`weight` varchar(40) CHARACTER SET utf8 NOT NULL,
				`width` varchar(40) CHARACTER SET utf8 NOT NULL,
				`height` varchar(40) CHARACTER SET utf8 NOT NULL,
				`length` varchar(40) CHARACTER SET utf8 NOT NULL,
				`status` tinyint(1) NOT NULL DEFAULT '1',
				PRIMARY KEY  (`id`)
		  );";
		dbDelta( $sql );

		$table_name = $wpdb->prefix . "linksynceparcel_nonlinksync";
		$sql = "CREATE TABLE $table_name (
			 	`id` int(11) NOT NULL auto_increment,
				`method` varchar(255) CHARACTER SET utf8 NOT NULL,
				`charge_code` varchar(255) CHARACTER SET utf8 NOT NULL,
				PRIMARY KEY  (`id`)
		  );";

		dbDelta( $sql );

		self::upgradeTables();
	}

	public static function upgradeTables()
	{
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$table_name = $wpdb->prefix . "linksynceparcel_consignment";

		$sql = "SHOW INDEX FROM $table_name WHERE Key_name = 'con_order_id_despatched'";
		$indexes = $wpdb->query( $sql );
		if(!$indexes)
		{
			$sql = "ALTER TABLE $table_name ADD INDEX `con_order_id_despatched` ( `order_id` , `despatched` ),  ADD INDEX `con_consignment_number` ( `consignment_number`), ADD INDEX `con_manifest_number` ( `manifest_number`)";
			$wpdb->query( $sql );
		}

		$table_name = $wpdb->prefix . "linksynceparcel_article";
		$sql = "SHOW INDEX FROM $table_name WHERE Key_name = 'art_consignment_number'";
		$indexes = $wpdb->query( $sql );
		if(!$indexes)
		{
			$sql = "ALTER TABLE $table_name ADD INDEX `art_consignment_number` ( `consignment_number`)";
			$wpdb->query( $sql );
		}

		$table_name = $wpdb->prefix . "linksynceparcel_manifest";
		$sql = "SHOW INDEX FROM $table_name WHERE Key_name = 'con_manifest_number'";
		$indexes = $wpdb->query( $sql );
		if(!$indexes)
		{
			$sql = "ALTER TABLE $table_name ADD INDEX `con_manifest_number` ( `manifest_number`)";
			$wpdb->query( $sql );
		}

		$table_name = $wpdb->prefix . "linksynceparcel_consignment";

		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'weight'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD weight varchar(20)";
			$wpdb->query( $sql );
		}

		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'delivery_country'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD delivery_country varchar(10)";
			$wpdb->query( $sql );
		}

		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'customdocs'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD `customdocs` varchar(255) CHARACTER SET utf8 AFTER `label`";
			$wpdb->query( $sql );
		}

		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'is_customdocs_printed'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD `is_customdocs_printed` tinyint(1) NOT NULL DEFAULT '0' AFTER `is_label_printed`";
			$wpdb->query( $sql );
		}

		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'delivery_instruction'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD `delivery_instruction` varchar(300)";
			$wpdb->query( $sql );
		}

		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'safe_drop'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD `safe_drop` tinyint(1)";
			$wpdb->query( $sql );
		}

		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'date_process'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD `date_process` varchar(250)";
			$wpdb->query( $sql );
		}

		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'shipping_cost'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD `shipping_cost` varchar(250)";
			$wpdb->query( $sql );
		}

		$table_name = $wpdb->prefix . "linksynceparcel_nonlinksync";
		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'shipping_type'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD shipping_type varchar(20)";
			$wpdb->query( $sql );
		}

		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'service_type'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD service_type varchar(100)";
			$wpdb->query( $sql );
		}

		$table_name = $wpdb->prefix . "linksynceparcel_international_fields";
		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'insurance_value'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD insurance_value varchar(255) CHARACTER SET utf8 NOT NULL AFTER `modify_date`";
			$wpdb->query( $sql );
		}

		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'insurance'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD insurance tinyint(1) NOT NULL DEFAULT '0' AFTER `modify_date`";
			$wpdb->query( $sql );
		}

		$table_name = $wpdb->prefix . "linksynceparcel_manifest";
		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'despatch_complete'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD despatch_complete tinyint(1) NOT NULL DEFAULT '0'";
			$wpdb->query( $sql );
		}

		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'despatch_mode'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD despatch_mode tinyint(1) NOT NULL DEFAULT '1'";
			$wpdb->query( $sql );
		}

		$sql = "SELECT * FROM information_schema.COLUMNS WHERE  TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '$table_name' AND COLUMN_NAME = 'despatch_status'";
		$records = $wpdb->query( $sql );
		if(!$records)
		{
			$sql = "ALTER TABLE $table_name ADD despatch_status tinyint(1) NOT NULL DEFAULT '0'";
			$wpdb->query( $sql );
		}
	}

	public static function createNewTables() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

   		$table_name = $wpdb->prefix . "linksynceparcel_international_fields";
		$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
			`order_id` int(11) NOT NULL DEFAULT '0',
			`consignment_number` varchar(128) NOT NULL,
			`add_date` varchar(40) NOT NULL,
			`modify_date` varchar(40) NOT NULL,
			`export_declaration_number` varchar(255) NOT NULL,
			`declared_value` tinyint(1) NOT NULL DEFAULT '0',
			`declared_value_text` varchar(255) NOT NULL,
			`has_commercial_value` tinyint(1) NOT NULL DEFAULT '0',
			`product_classification` int(11) NOT NULL DEFAULT '991',
			`product_classification_text` varchar(255) NOT NULL,
			`country_origin` varchar(255) DEFAULT NULL,
			`hs_tariff` varchar(255) DEFAULT NULL,
			`default_contents` varchar(255) DEFAULT NULL,
			`ship_country` varchar(255) DEFAULT NULL
		  );";

		dbDelta( $sql );

		$table_name = $wpdb->prefix . "linksynceparcel_order_statuses";
		$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
			`id` int(11) NOT NULL auto_increment,
			`status` varchar(255) NOT NULL,
			`status_name` varchar(255) NOT NULL,
			PRIMARY KEY  (`id`)
		  );";
		 dbDelta( $sql );

		$table_name = $wpdb->prefix . "linksynceparcel_consignment_temp_cost";
		$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
			`order_id` int(11) NOT NULL DEFAULT '0',
			`consignment_cost` varchar(128) NOT NULL
		  );";

		dbDelta( $sql );
	}

	public static function dropTables()
	{
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "linksynceparcel_address_valid" );
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "linksynceparcel_consignment" );
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "linksynceparcel_article" );
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "linksynceparcel_manifest" );
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "linksynceparcel_article_preset" );
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "linksynceparcel_nonlinksync" );
	}

	public static function getAddressValid($order_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_address_valid";
		$sql = "SELECT * FROM $table_name WHERE order_id = $order_id";
		$results = $wpdb->get_results($sql);
		foreach($results as $result)
		{
			return $result;
		}
		return false;
	}

	public static function updateAddressValid($order_id, $is_address_valid)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_address_valid";
		if(self::getAddressValid($order_id))
		{
			$wpdb->update(
				$table_name,
				array(
					'is_address_valid' => $is_address_valid
				),
				array( 'order_id' => $order_id )
			);
		}
		else
		{
			$wpdb->insert(
				$table_name,
				array(
					'order_id' => $order_id,
					'is_address_valid' => $is_address_valid
				)
			);
		}
	}

	public static function isOrderAddressValid($order_id, $force=false,$address=false)
	{
		$valid = LinksynceparcelHelper::getAddressValid($order_id);
		if(!$force && isset($valid->is_address_valid) && $valid->is_address_valid)
			return 1;

		$data = array();
		if($address)
		{
			$data['city'] = $address['_shipping_city'];
			$data['state'] = $address['_shipping_state'];
			$data['postcode'] = $address['_shipping_postcode'];
		}
		else
		{
			$address = get_post_meta($order_id);
			$data['city'] = $address['_shipping_city'][0];
			$data['state'] = $address['_shipping_state'][0];
			$data['postcode'] = $address['_shipping_postcode'][0];
		}

		$status = LinksynceparcelApi::isAddressValid($data);
		if($status == 1)
		{
			self::updateAddressValid($order_id, 1);
		}
		else
		{
			self::updateAddressValid($order_id, 0);
		}
		return $status;
	}

	public static function saveDefaultConfiguration()
	{
		global $is_greater_than_21;

		$defaults = array();

		$laid = get_option( 'linksynceparcel_laid');
		if(empty($laid))
		{
			$defaults['active'] = 1;
			$defaults['mode'] = 0;
			$defaults['insurance'] = 0;
			$defaults['signature_required'] = 1;
			$defaults['print_return_labels'] = 0;
			$defaults['partial_delivery_allowed'] = 0;
			$defaults['post_email_notification'] = 0;
			$defaults['label_format'] = 'mpp';
			$defaults['apply_to_all'] = 0;
			$defaults['notify_customers'] = 0;
			$defaults['subject'] = 'Order [OrderNumber] tracking number';
			$defaults['email_body'] = "<p>Hello [CustomerFirstname],</p><p>&nbsp;</p><p>Your order [OrderNumber] will be despatched today, and once shipped, you'll be able to use the following link to track it:&nbsp;</p><p>&nbsp;</p><p><a href='http://auspost.com.au/track/track.html?id=[TrackingNumber]'>http://auspost.com.au/track/track.html?id=[TrackingNumber]</a></p>";
			$defaults['mark_despatch'] = 0;
			$defaults['use_order_weight'] = 0;
			$defaults['display_order_status'] = 0;
			$defaults['display_choosen_status'] = 0;
			$defaults['use_dimension'] = 1;
			$defaults['product_unit'] = 'kgs';
			$defaults['copy_order_notes'] = 0;
		}

		$international_checker = get_option( 'linksynceparcel_international_checker');
		if(empty($international_checker)) {
			$defaults['int_insurance'] = 0;
			$defaults['safe_drop'] = 1;
			$defaults['declared_value'] = 1;
			$defaults['order_value_insurance'] = 1;
			$defaults['show_order_items'] = 1;
			$defaults['product_classification_text'] = 'Merchandise';
			$defaults['default_contents'] = 'Merchandise';
			$defaults['international_checker'] = 1;
		}

		if(!empty($defaults)) {
			if($is_greater_than_21)
			{
				$defaults['chosen_statuses'] = array('wc-pending','wc-processing','wc-on-hold');
			}
			else
			{
				$defaults['chosen_statuses'] = array('pending','processing','on-hold');
			}

			foreach($defaults as $key => $val)
			{
				update_option( 'linksynceparcel_'.$key, $val);
			}
		}
	}

	public static function checkOpenManifest()
	{
		global $wpdb;
		$table = $wpdb->prefix ."linksynceparcel_manifest";
		$sql = "SELECT * FROM ". $table ." ORDER BY manifest_id DESC LIMIT 1";
		$result = $wpdb->get_row($sql);
		return (empty($result->despatch_date))?$result->manifest_number:0;
	}

	public static function saveConfiguration($values)
	{
		foreach($values as $key => $val)
		{
			update_option( 'linksynceparcel_'.$key, $val);
		}
	}

	public static function updateShippingChargecode($data)
	{
		global $wpdb;
		$table = $wpdb->prefix ."linksynceparcel_nonlinksync";

		$services = self::eParcelServices();

		foreach($services as $k => $service) {
			$v = self::getSingleChargeCode($k);
			$data = array('charge_code' => $v);
			$where = array('service_type' => $k);

			$wpdb->update( $table, $data, $where);
		}
	}

	public static function getStates()
	{
		$states = array();
		$states['ACT'] = 'ACT';
		$states['NSW'] = 'NSW';
		$states['NT'] = 'NT';
		$states['QLD'] = 'QLD';
		$states['SA'] = 'SA';
		$states['TAS'] = 'TAS';
		$states['VIC'] = 'VIC';
		$states['WA'] = 'WA';
		return $states;
	}

	public static function getWooCountries() {
		return WC()->countries->countries;
	}

	public static function getCountries() {
		$countries_url = 'http://data.okfn.org/data/core/country-list/r/data.json';
		$json_data = self::fetchDataTags($countries_url);
		$countries_data = self::decodeString($json_data);
		if(!empty($countries_data)) {
			$countries = array();
			foreach($countries_data as $country_key => $country) {
				$countries[$country->Code] = $country->Name;
			}
			return $countries;
		}
		return false;
	}

	public static function fetchDataTags($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	public static function decodeString($string) {
		return json_decode($string);
	}

	public static function wpbo_get_woo_version_number()
	{
		if (!function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file = 'woocommerce.php';

		if ( isset( $plugin_folder[$plugin_file]['Version'] ) )
		{
			return $plugin_folder[$plugin_file]['Version'];
		}
		else
		{
			return NULL;
		}
	}

	public static function woocommerce_version_check($current_version,$version = '2.2' )
	{
		if( version_compare($current_version, $version, ">=" ) )
		{
			return true;
		}
		return false;
	}

	public static function getOrderStatuses()
	{
		$current_version = self::wpbo_get_woo_version_number();
		if(self::woocommerce_version_check($current_version))
		{
			return wc_get_order_statuses();
		}
		else
		{
			return (array)get_terms('shop_order_status', array('hide_empty' => 0, 'orderby' => 'id'));
		}
	}

	public static function saveLabelLogo()
	{
		if(is_uploaded_file($_FILES['label_logo']['tmp_name']))
		{
			$filename = linksynceparcel_DIR.'assets/images/label_logo.png';
			$image = wp_get_image_editor($_FILES['label_logo']['tmp_name']);
			if (!is_wp_error($image))
			{
				$image->resize(160, 65, true);
				$image->save($filename);
				update_option('linksynceparcel_label_logo', 'label_logo.png');
			}
		}
	}

	public static function log($content, $date = true)
	{
		$filename = linksynceparcel_LOG_DIR .'linksynceparcel.log';
		$fp = fopen($filename, 'a+');
		if($date)
		{
			fwrite($fp, date("Y-m-d H:i:s").": ");
		}
		fwrite($fp, print_r($content, TRUE));
		fwrite($fp, "\n");
		fclose($fp);
	}

	public static function isZipArchiveInstalled()
	{
		if(class_exists('ZipArchive'))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	public static function createZip($file,$archiveFile)
	{
		$ziph = new ZipArchive();
		if(file_exists($archiveFile))
		{
			if($ziph->open($archiveFile, ZIPARCHIVE::CHECKCONS) !== TRUE)
			{
				throw new Exception("Unable to Open $archiveFile");
			}
		}
		else
		{
		  	if($ziph->open($archiveFile, ZIPARCHIVE::CM_PKWARE_IMPLODE) !== TRUE)
		  	{
				throw new Exception("Could not Create $archiveFile");
		  	}
		}

		if(file_exists($file))
		{
			if(is_readable($file))
			{
			  	if(!$ziph->addFile($file,'linksynceparcel.log'))
				{
				  throw new Exception("Error archiving $file in $archiveFile");
				}
			}
			else
			{
				throw new Exception("Error archiving $file is not readable");
			}
		}
		else
		{
			throw new Exception("Error archiving $file is not exist");
		}
		$ziph->close();
		return true;
	}


	public static function downloadLogfile($filepath)
	{
	    if(file_exists($filepath)) {
	        header("Cache-Control: public");
	        header('Content-Description: File Transfer');
	        header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
	        header("Content-Type: application/force-download");
	        header('Expires: 0');
	        header('Cache-Control: must-revalidate');
	        header('Content-Length: ' . filesize($filepath));
	        flush(); // Flush system output buffer
	        readfile($filepath);
	        exit;
	    }
	}

	public static function getFormValue($index, $default='', $main='linksynceparcel')
	{
		$value = '';
		if(!empty($main))
		{
			$data = isset($_REQUEST[$main]) ? $_REQUEST[$main] : array();
			$value = isset($data[$index]) ? trim($data[$index]) : '';
		}
		else
		{
			$value = isset($_REQUEST[$index]) ? trim($_REQUEST[$index]) : '';
		}

		$value = sanitize_text_field($value);

		if($value === 0)
			return $value;
		else
			return stripslashes(empty($value) ? $default : $value);
	}

	public static function getEParcelChargeCodes() {
		$chargeCode = array(
			'B1' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'B2' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'B3' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'

			),
			'B4' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'B5' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'B96' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'B97' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'B98' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'D1' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'DE1' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'DE2' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'DE4' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'DE5' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'DE6' 	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'MED1'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'MED2'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'S1'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'S10'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'S2'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'S3'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'S4'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'S5'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'S6'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'S7'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'S8'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'S9'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'SV1'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'SV2'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'W5'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'W6'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'eParcel Standard',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 60,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'X1'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'X2'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'X5'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'X6'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'XB1'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'XB2'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'XB3'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'XB4'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'XB5'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'XDE5'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'XW5'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'XW6'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'XS'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post eParcel',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 61,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3E03'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7E03'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3E05'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7E05'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3E33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7E33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3E35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7E35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3E53'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7E53'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3E55'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7E55'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3E83'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7E83'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3E85'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7E85'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'2A33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '500g Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'2A35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '500g Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'2B33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '500g Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'2B35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '500g Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'2C33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '500g Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'2C35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '500g Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'2D33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '500g Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'2D35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '500g Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'2G33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '500g Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'2G35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '500g Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'2H33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '500g Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'2H35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '500g Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'2I33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '500g Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'2I35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '500g Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'2J33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '500g Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'2J35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '500g Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3B03'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3B05'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3C03'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3C05'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3C33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3C35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3C53'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3C55'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3C83'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3C85'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3D03'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3D05'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3D33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3D35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3D53'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3D55'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3D83'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3D85'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'3H03'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3H05'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3I03'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3I05'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3I33'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3I35'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3I53'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3I55'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3I83'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3I85'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3J03'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3J05'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3J33'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3J35'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3J53'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3J55'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3J83'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3J85'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3K03'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3K05'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3K33'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3K35'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3K53'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3K55'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3K83'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'3K85'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'4A33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '1kg Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'4A35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '1kg Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'4B33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '1kg Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'4B35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '1kg Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'4C33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '1kg Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'4C35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '1kg Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'4D33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '1kg Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'4D35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '1kg Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'4I33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '1kg Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'4I35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '1kg Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'4J33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '1kg Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'4J35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '1kg Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7B03'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7B05'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7B33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7B35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7B53'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7B55'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7B83'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7B85'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7C03'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7C05'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7C33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7C35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7C53'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7C55'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7C83'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7C85'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7D03'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7D05'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7D33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7D35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7D53'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7D55'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7D83'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7D85'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7H03'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7H05'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7H33'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7H35'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7H53'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7H55'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7H83'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7H85'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7I03'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7I05'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7I33'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7I35'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7I53'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7I55'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7I83'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7I85'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7J03'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7J05'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7J33'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7J35'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7J53'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7J55'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7J83'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7J85'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7K03'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7K05'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7K33'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7K35'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7K53'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7K55'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7K83'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7K85'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7N33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post Wine',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7N35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post Wine + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7N83'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post Wine',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7N85'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post Wine + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7O33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post Wine',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7O35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post Wine + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7O83'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post Wine',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7O85'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post Wine + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7P33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post Wine',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7P35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post Wine + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7P83'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post Wine',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7P85'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> 'Parcel Post Wine + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'7T33'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post Wine',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7T35'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post Wine + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7T83'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post Wine',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7T85'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post Wine + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7U33'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post Wine',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7U35'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post Wine + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7U83'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post Wine',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7U85'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post Wine + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7V33'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post Wine',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7V35'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post Wine + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7V83'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post Wine',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'7V85'	=> array(
				'key'			=> 'express_post',
				'name'			=> 'Express Post Wine + Signature',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'8A33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '3kg Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'8A35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '3kg Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'8B33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '3kg Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'8B35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '3kg Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'8C33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '3kg Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'8C35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '3kg Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'8D33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '3kg Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'8D35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '3kg Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'8G33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '3kg Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'8G35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '3kg Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'8H33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '3kg Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'8H35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '3kg Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'8I33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '3kg Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'8I35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '3kg Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'8J33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '3kg Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'8J35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '3kg Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'9A33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '5kg Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'9A35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '5kg Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'9B33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '5kg Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'9B35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '5kg Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'9C33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '5kg Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'9C35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '5kg Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'9D33'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '5kg Parcel Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 91,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'9D35'	=> array(
				'key'			=> 'parcel_post',
				'name'			=> '5kg Parcel Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 93,
				'labelType' 	=> 'Parcel Post',
				'template' 		=> 'EPARCEL',
				'serviceType'	=> 'standard',
				'service'		=> 'Std.'
			),
			'9G33' 	=> array(
				'key'			=> 'express_post',
				'name'			=> '5kg Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'9G35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '5kg Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'9H33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '5kg Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'9H35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '5kg Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'9I33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '5kg Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'9I35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '5kg Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'9J33'	=> array(
				'key'			=> 'express_post',
				'name'			=> '5kg Express Post Satchel',
				'serviceCode' 	=> 9,
				'prodCode' 		=> 87,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'9J35'	=> array(
				'key'			=> 'express_post',
				'name'			=> '5kg Express Post Satchel + Sig',
				'serviceCode' 	=> 2,
				'prodCode' 		=> 96,
				'labelType' 	=> 'Express Post',
				'template' 		=> 'EPARCEL EXPRESS',
				'serviceType'	=> 'express',
				'service'		=> 'Exp.'
			),
			'AIR1' 	=> array(
				'key'			=> 'int_economy',
				'name'			=> 'Int. Economy Air',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Economy Air',
				'template' 		=> 'ECONOMY AIRMAIL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'AIR2'	=> array(
				'key'			=> 'int_economy',
				'name'			=> 'Int. Economy Air',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Economy Air',
				'template' 		=> 'ECONOMY AIRMAIL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'AIR3'	=> array(
				'key'			=> 'int_economy',
				'name'			=> 'Int. Economy Air',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Economy Air',
				'template' 		=> 'ECONOMY AIRMAIL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'AIR4'	=> array(
				'key'			=> 'int_economy',
				'name'			=> 'Int. Economy Air',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Economy Air',
				'template' 		=> 'ECONOMY AIRMAIL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'AIR5'	=> array(
				'key'			=> 'int_economy',
				'name'			=> 'Int. Economy Air',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Economy Air',
				'template' 		=> 'ECONOMY AIRMAIL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'AIR6'	=> array(
				'key'			=> 'int_economy',
				'name'			=> 'Int. Economy Air',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Economy Air',
				'template' 		=> 'ECONOMY AIRMAIL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'AIR7'	=> array(
				'key'			=> 'int_economy',
				'name'			=> 'Int. Economy Air',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Economy Air',
				'template' 		=> 'ECONOMY AIRMAIL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'AIR8'	=> array(
				'key'			=> 'int_economy',
				'name'			=> 'Int. Economy Air',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Economy Air',
				'template' 		=> 'ECONOMY AIRMAIL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'AIR9'	=> array(
				'key'			=> 'int_economy',
				'name'			=> 'Int. Economy Air',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Economy Air',
				'template' 		=> 'ECONOMY AIRMAIL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECD1'	=> array(
				'key'			=> 'int_express_courier',
				'name'			=> 'Int. Express Courier Document',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier Document',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECD2'	=> array(
				'key'			=> 'int_express_courier',
				'name'			=> 'Int. Express Courier Document',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier Document',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECD3'	=> array(
				'key'			=> 'int_express_courier',
				'name'			=> 'Int. Express Courier Document',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier Document',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECD4'	=> array(
				'key'			=> 'int_express_courier',
				'name'			=> 'Int. Express Courier Document',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier Document',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECD5'	=> array(
				'key'			=> 'int_express_courier',
				'name'			=> 'Int. Express Courier Document',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier Document',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECD6'	=> array(
				'key'			=> 'int_express_courier',
				'name'			=> 'Int. Express Courier Document',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier Document',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECD7'	=> array(
				'key'			=> 'int_express_courier',
				'name'			=> 'Int. Express Courier Document',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier Document',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECD8'	=> array(
				'key'			=> 'int_express_courier',
				'name'			=> 'Int. Express Courier Document',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier Document',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECD9'	=> array(
				'key'			=> 'int_express_courier',
				'name'			=> 'Int. Express Courier Document',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier Document',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECM1'	=> array(
				'key'			=> 'int_express_courier_mer',
				'name'			=> 'Int. Express Courier',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECM2'	=> array(
				'key'			=> 'int_express_courier_mer',
				'name'			=> 'Int. Express Courier',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECM3'	=> array(
				'key'			=> 'int_express_courier_mer',
				'name'			=> 'Int. Express Courier',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECM4'	=> array(
				'key'			=> 'int_express_courier_mer',
				'name'			=> 'Int. Express Courier',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECM5'	=> array(
				'key'			=> 'int_express_courier_mer',
				'name'			=> 'Int. Express Courier',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECM6'	=> array(
				'key'			=> 'int_express_courier_mer',
				'name'			=> 'Int. Express Courier',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECM7'	=> array(
				'key'			=> 'int_express_courier_mer',
				'name'			=> 'Int. Express Courier',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECM8'	=> array(
				'key'			=> 'int_express_courier_mer',
				'name'			=> 'Int. Express Courier',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'ECM9'	=> array(
				'key'			=> 'int_express_courier_mer',
				'name'			=> 'Int. Express Courier',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Courier',
				'template' 		=> 'EXPRESS COURIER INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'EPI1'	=> array(
				'key'			=> 'int_express_post',
				'name'			=> 'Int. Express Post',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Post',
				'template' 		=> 'EXPRESS POST INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'EPI2'	=> array(
				'key'			=> 'int_express_post',
				'name'			=> 'Int. Express Post',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Post',
				'template' 		=> 'EXPRESS POST INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'EPI3'	=> array(
				'key'			=> 'int_express_post',
				'name'			=> 'Int. Express Post',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Post',
				'template' 		=> 'EXPRESS POST INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'EPI4'	=> array(
				'key'			=> 'int_express_post',
				'name'			=> 'Int. Express Post',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Post',
				'template' 		=> 'EXPRESS POST INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'EPI5'	=> array(
				'key'			=> 'int_express_post',
				'name'			=> 'Int. Express Post',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Post',
				'template' 		=> 'EXPRESS POST INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'EPI6'	=> array(
				'key'			=> 'int_express_post',
				'name'			=> 'Int. Express Post',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Post',
				'template' 		=> 'EXPRESS POST INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'EPI7'	=> array(
				'key'			=> 'int_express_post',
				'name'			=> 'Int. Express Post',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Post',
				'template' 		=> 'EXPRESS POST INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'EPI8'	=> array(
				'key'			=> 'int_express_post',
				'name'			=> 'Int. Express Post',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Post',
				'template' 		=> 'EXPRESS POST INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'EPI9'	=> array(
				'key'			=> 'int_express_post',
				'name'			=> 'Int. Express Post',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Express Post',
				'template' 		=> 'EXPRESS POST INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'PTI1'	=> array(
				'key'			=> 'int_pack_track',
				'name'			=> 'Int. Pack & Track',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Pack & Track',
				'template' 		=> 'PACK AND TRACK INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'PTI2'	=> array(
				'key'			=> 'int_pack_track',
				'name'			=> 'Int. Pack & Track',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Pack & Track',
				'template' 		=> 'PACK AND TRACK INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'PTI3'	=> array(
				'key'			=> 'int_pack_track',
				'name'			=> 'Int. Pack & Track',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Pack & Track',
				'template' 		=> 'PACK AND TRACK INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'PTI4'	=> array(
				'key'			=> 'int_pack_track',
				'name'			=> 'Int. Pack & Track',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Pack & Track',
				'template' 		=> 'PACK AND TRACK INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'PTI5'	=> array(
				'key'			=> 'int_pack_track',
				'name'			=> 'Int. Pack & Track',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Pack & Track',
				'template' 		=> 'PACK AND TRACK INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'PTI6'	=> array(
				'key'			=> 'int_pack_track',
				'name'			=> 'Int. Pack & Track',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Pack & Track',
				'template' 		=> 'PACK AND TRACK INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'PTI7'	=> array(
				'key'			=> 'int_pack_track',
				'name'			=> 'Int. Pack & Track',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Pack & Track',
				'template' 		=> 'PACK AND TRACK INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'PTI8'	=> array(
				'key'			=> 'int_pack_track',
				'name'			=> 'Int. Pack & Track',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Pack & Track',
				'template' 		=> 'PACK AND TRACK INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'PTI9'	=> array(
				'key'			=> 'int_pack_track',
				'name'			=> 'Int. Pack & Track',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Pack & Track',
				'template' 		=> 'PACK AND TRACK INTERNATIONAL',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'RPI1'	=> array(
				'key'			=> 'int_registered',
				'name'			=> 'Int. Registered',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Registered',
				'template' 		=> 'INTERNATIONAL REGISTERED',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'RPI2'	=> array(
				'key'			=> 'int_registered',
				'name'			=> 'Int. Registered',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Registered',
				'template' 		=> 'INTERNATIONAL REGISTERED',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'RPI3'	=> array(
				'key'			=> 'int_registered',
				'name'			=> 'Int. Registered',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Registered',
				'template' 		=> 'INTERNATIONAL REGISTERED',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'RPI4'	=> array(
				'key'			=> 'int_registered',
				'name'			=> 'Int. Registered',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Registered',
				'template' 		=> 'INTERNATIONAL REGISTERED',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'RPI5'	=> array(
				'key'			=> 'int_registered',
				'name'			=> 'Int. Registered',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Registered',
				'template' 		=> 'INTERNATIONAL REGISTERED',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'RPI6'	=> array(
				'key'			=> 'int_registered',
				'name'			=> 'Int. Registered',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Registered',
				'template' 		=> 'INTERNATIONAL REGISTERED',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'RPI7'	=> array(
				'key'			=> 'int_registered',
				'name'			=> 'Int. Registered',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Registered',
				'template' 		=> 'INTERNATIONAL REGISTERED',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'RPI8'	=> array(
				'key'			=> 'int_registered',
				'name'			=> 'Int. Registered',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Registered',
				'template' 		=> 'INTERNATIONAL REGISTERED',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			),
			'RPI9'	=> array(
				'key'			=> 'int_registered',
				'name'			=> 'Int. Registered',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> 'Int. Registered',
				'template' 		=> 'INTERNATIONAL REGISTERED',
				'serviceType'	=> 'international',
				'service'		=> 'Int.'
			)
		);
		return $chargeCode;
	}

	public static function getCombination($chargecode)
	{
		$chargeCodes = self::getEParcelChargeCodes();
		$cc = $chargeCodes[$chargecode];
		if($cc['serviceCode'] != 0 && $cc['prodCode'] != 0) {
			$sc_pc = $cc['serviceCode'] .'_'. $cc['prodCode'];
			return self::combinationData($sc_pc);
		}
		return false;
	}

	public static function combinationData($sc_pc)
	{
		$combinations = array(
			'2_93' => array(
				'delivery_signature_allowed' => 1,
				'partial_delivery_allowed' => 0
			),
			'2_96' => array(
				'delivery_signature_allowed' => 1,
				'partial_delivery_allowed' => 0
			),
			'9_91' => array(
				'delivery_signature_allowed' => 0,
				'partial_delivery_allowed' => 1
			),
			'9_87' => array(
				'delivery_signature_allowed' => 0,
				'partial_delivery_allowed' => 1
			)
		);
		if(isset($combinations[$sc_pc])) {
			return $combinations[$sc_pc];
		}
		return false;
	}

	public static function getChargeCodeValues($none=false)
	{
		$chargeCodes = self::getEParcelChargeCodes();
		$options = array();
		if($none)
		{
			$options['None'] = array(
				'name'			=> 'None',
				'serviceCode' 	=> 0,
				'prodCode' 		=> 0,
				'labelType' 	=> '',
				'template' 		=> ''
			);
		}

		foreach($chargeCodes as $chargeCode => $codeLabels)
		{
			$options[$chargeCode] = $codeLabels;
		}
		return $options;
	}

	public static function getExpressPostCodes()
	{
		$codes1 = array('X1','X2','X5','X6','XB1','XB2','XB3','XB4','XB5','XDE5','XW5','XW6','XS','7J55');
		$codes2 = array('2G33','2G35','2H33','2H35','2I33','2I35','2J33','2J35','3H03','3H05','3I03','3I05','3I33','3I35','3I53','3I55','3I83','3I85','3J03','3J05','3J33','3J35','3J53','3J55','3J83','3J85','3K03','3K05','3K33','3K35','3K53','3K55','3K83','3K85','4I33','4I35','4J33','4J35','7H03','7H05','7H33','7H35','7H53','7H55','7H83','7H85','7I03','7I05','7I33','7I35','7I53','7I55','7I83','7I85','7J03','7J05','7J33','7J35','7J53','7J55','7J83','7J85','7K03','7K05','7K33','7K35','7K53','7K55','7K83','7K85','7T33','7T35','7T83','7T85','7U33','7U35','7U83','7U85','7V33','7V35','7V83','7V85','8G33','8G35','8H33','8H35','8I33','8I35','8J33','8J35','9G33','9G35','9H33','9H35','9I33','9I35','9J33','9J35');
		return array_merge($codes1,$codes2);
	}

	public static function getLinksynceparcelStandardCodes()
	{
		$codes1 = array('B1','B2','B3','B4','B5','B96','B97','B98','D1','DE1','DE2','DE4','DE5','DE6','MED1','MED2','S1','S10','S2','S3','S4','S5','S6','S7','S8','S9','SV1','SV2','W5','W6','7D55');
		$codes2 = array('3E03','7E03','3E05','7E05','3E33','7E33','3E35','7E35','3E53','7E53','3E55','7E55','3E83','7E83','3E85','7E85','2A33','2A35','2B33','2B35','2C33','2C35','2D33','2D35','3B03','3B05','3C03','3C05','3C33','3C35','3C53','3C55','3C83','3C85','3D03','3D05','3D33','3D35','3D53','3D55','3D83','3D85','4A33','4A35','4B33','4B35','4C33','4C35','4D33','4D35','7B03','7B05','7B33','7B35','7B53','7B55','7B83','7B85','7C03','7C05','7C33','7C35','7C53','7C55','7C83','7C85','7D03','7D05','7D33','7D35','7D53','7D55','7D83','7D85','7N33','7N35','7N83','7N85','7O33','7O35','7O83','7O85','7P33','7P35','7P83','7P85','8A33','8A35','8B33','8B35','8C33','8C35','8D33','8D35','9A33','9A35','9B33','9B35','9C33','9C35','9D33','9D35');
		return array_merge($codes1,$codes2);
	}

	public static function isExpressPostCode($code)
	{
		$codes = self::getExpressPostCodes();
		return in_array($code,$codes);
	}

	public static function isLinksynceparcelStandardCode($code)
	{
		$codes = self::getLinksynceparcelStandardCodes();
		return in_array($code,$codes);
	}

	public static function getShippingMethod($id_order)
	{
		$order = new WC_Order( $id_order );
		$methods = $order->get_shipping_methods();
		foreach($methods as $method)
		{
			return $method['method_id'];
		}
	}

	public static function getAssignChargeCode($methodid)
	{
		global $wpdb;
		$table = $wpdb->prefix ."linksynceparcel_nonlinksync";
		$row = $wpdb->get_row("SELECT charge_code FROM ". $table ." WHERE method='". $methodid ."'");
		if(!empty($row)) {
			return $row->charge_code;
		}
		return false;
	}

	public static function getMethodIDOrderMeta($orderitemid, $key='method_id')
	{
		global $wpdb;
		$table = $wpdb->prefix ."woocommerce_order_itemmeta";
		$row = $wpdb->get_row("SELECT meta_value FROM ". $table ." WHERE order_item_id=". $orderitemid ." AND meta_key='". $key ."'");
		return $row->meta_value;
	}

	public static function getShippingMethodByOrder($orderid)
	{
		global $wpdb;
		$table = $wpdb->prefix ."woocommerce_order_items";
		$row = $wpdb->get_row("SELECT order_item_id FROM ". $table ." WHERE order_id=". $orderid ." AND order_item_type='shipping'");
		return $row->order_item_id;
	}

	public static function getShippingMethodNameByOrder($orderid)
	{
		global $wpdb;
		$table = $wpdb->prefix ."woocommerce_order_items";
		$row = $wpdb->get_row("SELECT order_item_name FROM ". $table ." WHERE order_id=". $orderid ." AND order_item_type='shipping'");
		return $row->order_item_name;
	}

	public static function getOrderChargeCode($id_order,$consignment_number='')
	{
		$woo_version = self::wpbo_get_woo_version_number();
		$req_version = '3.5.0';

		$ordermetaid = self::getShippingMethodByOrder($id_order);
		$methodkey = 'method_id';
		if($woo_version >= $req_version) {
			$methodkey = 'instance_id';
		}
		$methodid = self::getMethodIDOrderMeta($ordermetaid, $methodkey);
		if(!empty($methodid)) {
			$chargecode = self::getAssignChargeCode($methodid);
			if(!empty($chargecode)) {
				return $chargecode;
			} else {
				$methodid = self::getMethodIDOrderMeta($ordermetaid);
				$chargecode2 = self::getAssignChargeCode($methodid);
				if(!empty($chargecode2)) {
					return $chargecode2;
				}
			}
		} else {
			$methodid = self::getShippingMethodNameByOrder($id_order);
			$chargecode = self::getAssignChargeCode($methodid);
			if(!empty($chargecode)) {
				return $chargecode;
			}
		}
		return false;
	}

	public static function getShippingZone()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "woocommerce_shipping_zone_methods";
		$sql = "SELECT * FROM $table_name";
		$results = $wpdb->get_results($sql);
		return $results;
	}

	public static function availableShippingMethods()
	{
		$arr = array();
		$methods = self::getShippingZone();
		foreach($methods as $code => $method) {
			if($method->instance_id) {
				$name = self::getShippingMethodName($method->instance_id, $method->method_id);
				if($name == false) {
					$name = ucfirst(str_replace('_', ' ', $method->method_id));
				}
				$arr[$method->method_id.':'.$method->instance_id] = array(
					'method_id' => $method->method_id,
					'title' => $name
				);
			}
		}
		return $arr;
	}

	public static function getShippingMethodName($instanceid, $option_name)
	{
		$option = 'woocommerce_'. $option_name .'_'. $instanceid .'_settings';
		$optionval = get_option($option, true);
		if($optionval) {
			return $optionval['title'];
		}
		return false;
	}

	public static function getChargeCode($order, $consignmentNumber='')
	{
        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
		$chargeCode = self::getOrderChargeCode($order_id, $consignmentNumber);
		return $chargeCode;
	}

	public static function getNonlinksyncShippingTypeChargecode($method)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_nonlinksync";
		$sql = "SELECT * FROM $table_name WHERE method = '". self::mres($method) ."'";
		$results = $wpdb->get_results($sql);
		foreach($results as $result)
		{
			return $result->charge_code;
		}
		return false;
	}

	public static function serviceChargeCodeFilter($service)
	{
		$allowedChargeCodes = self::getEParcelChargeCodes();
		$chargeCodes = array();
		foreach($allowedChargeCodes as $chargeCode => $chargeValues) {
			if($chargeValues['serviceType'] == $service) {
				$chargeCodes[] = $chargeCode;
			}
		}
		return $chargeCodes;
	}

	public static function getConsignments($id_order, $orderdate=false)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$query = "SELECT * FROM {$table_name} WHERE order_id = ". $id_order ."";
		if($orderdate) {
			$query .= ' ORDER BY `add_date` DESC';
		}
		return $wpdb->get_results($query);
	}

	public static function getOpenConsignments($id_order)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$query = "SELECT * FROM {$table_name} WHERE order_id = '{$id_order}' AND despatched != 1";
		return $wpdb->get_results($query);
	}

	public static function getConsignment($consignmentNumber)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$sql = "SELECT * FROM $table_name WHERE consignment_number = '$consignmentNumber'";
		$results = $wpdb->get_results($sql);
		foreach($results as $result)
		{
			return $result;
		}
		return false;
	}

	public static function getConsignmentSpecific($consignmentNumber,$field)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$sql = "SELECT `". $field ."` FROM ". $table_name ." WHERE `consignment_number` = '$consignmentNumber'";
		$results = $wpdb->get_results($sql);
		foreach($results as $result)
		{
			return $result->$field;
		}
		return false;
	}

	public static function isCashToCollect($id_order)
	{
		$allowedChargeCodes = array('CS1', 'CS2', 'CS3', 'CS4', 'CS5', 'CS6', 'CS7', 'CS8', 'CX1', 'CX2');

		$chargeCode = self::getOrderChargeCode($id_order);
		if(in_array($chargeCode,$allowedChargeCodes))
		{
			return true;
		}
		return false;
	}

	public static function isDisablePartialDeliveryMethod($id_order)
	{
		$allowedChargeCodes = array('PR', 'XPR');

		$chargeCode = self::getOrderChargeCode($id_order);
		if(in_array($chargeCode,$allowedChargeCodes))
		{
			return true;
		}
		return false;
	}

	public static function getLabelFormats()
	{
		$formats = array();
		$formats['sp'] = 'sp';
		$formats['spp'] = 'spp';
		$formats['mp'] = 'mp';
		$formats['mpp'] = 'mpp';
		return $formats;
    }

	public static function isSoapInstalled()
	{
		if(class_exists('SoapClient'))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	public static function addSuccess($message)
	{
		echo success_notice($message, '');
	}

	public static function addError($message)
	{
		error_notice('',$message);
	}

	public static function prepareArticle($data,$order,$consignment_number='')
	{
		$articleData = self::prepareArticleData($data,$order,$consignment_number);
		if(!isset($articleData['error_msg'])) {
			$content = $articleData['content'];
			$chargeCode = $articleData['charge_code'];
			return array('content' => $content, 'charge_code' => $chargeCode);
		} else {
			return $articleData;
		}
	}

	public static function prepareArticleBulk($data,$order)
	{
		$articleData = self::prepareArticleDataBulk($data,$order);
		if(!isset($articleData['error_msg'])) {
			$content = $articleData['content'];
			$chargeCode = $articleData['charge_code'];
			return array('content' => $content, 'charge_code' => $chargeCode);
		} else {
			return $articleData;
		}
	}

	public static function getTempConsignmentPrice($order_id)
	{
		$shipping_country = get_post_meta($order_id,'_shipping_country',true);
		$number_of_articles = 1;
		$order = new WC_Order( $order_id );
		$tempCanConsignments = (int)($number_of_articles/20);
		$canConsignments = $tempCanConsignments;
		$remainArticles = $number_of_articles % 20;
		$data = array();
		// Render all defaults
		if(get_option('linksynceparcel_copy_order_notes') == 1)
		{
			$ordernotes = $order->get_customer_note();
			$data['delivery_instruction'] = $ordernotes;
		}
		$data['order_value_declared_value'] = get_option('linksynceparcel_order_value_declared_value');
		$data['maximum_declared_value'] = get_option('linksynceparcel_maximum_declared_value');
		$data['fixed_declared_value'] = get_option('linksynceparcel_fixed_declared_value');
		$data['partial_delivery_allowed'] = get_option('linksynceparcel_partial_delivery_allowed');
		$data['delivery_signature_allowed'] = get_option('linksynceparcel_signature_required');
		$data['articles_type'] = 'order_weight';
		$data['safe_drop'] = get_option('linksynceparcel_safe_drop');
		$data['insurance'] = get_option('linksynceparcel_insurance');
		$data['order_value_insurance'] = get_option('order_value_insurance');
		$data['default_insurance_value'] = get_option('linksynceparcel_default_insurance_value');
		$data['export_declaration_number'] = "";
		$data['declared_value'] = get_option('linksynceparcel_declared_value');
		$data['declared_value_text'] = get_option('linksynceparcel_declared_value_text');
		$data['has_commercial_value'] = get_option('linksynceparcel_has_commercial_value');
		$data['product_classification'] = get_option('linksynceparcel_product_classification');
		$data['product_classification_text'] = get_option('linksynceparcel_product_classification_text');
		$data['country_origin'] = get_option('linksynceparcel_country_origin');
		$data['hs_tariff'] = get_option('linksynceparcel_hs_tariff');
		$data['default_contents'] = get_option('linksynceparcel_default_contents');
		$data['transit_cover_required'] = get_option('linksynceparcel_insurance');
		$data['transit_cover_amount'] = get_option('linksynceparcel_default_insurance_value');
		$data['contains_dangerous_goods'] = 0;
		if($shipping_country == 'AU') {
			$articleData = self::prepareArticleDataBulk($data, $order, $shipping_country);
			$content = $articleData['content'];
			$chargeCode = $articleData['charge_code'];
			$total_weight = $articleData['total_weight'];
			$consignmentData = LinksynceparcelApi::getConsignmentPrice($content);
			error_log("Ship Country => ". $shipping_country);
			if(!empty($consignmentData) && is_array($consignmentData)) {
				$consignmentPrice = $consignmentData->consignmentPriceSummary->total_cost;
				self::saveTempConsignmentCost($order_id, $consignmentPrice);
				error_log("Consignment ID => ". $order_id ." Consignment Price => ". $consignmentPrice );
				return $consignmentPrice;
			}
		}
		return 0;
	}

	public static function saveTempConsignmentCost($orderid, $cost)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment_temp_cost";
		$query = "INSERT ". $table_name ." SET order_id = ". $orderid .", consignment_cost='". $cost ."'";
		$wpdb->query($query);
	}

	public static function getTempConsignmentCost($orderid)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment_temp_cost";
		$query = "SELECT consignment_cost FROM ". $table_name ." WHERE order_id=". $orderid;
		$result = $wpdb->get_row($query);
		if(!empty($result)) {
			return $result->consignment_cost;
		}
		return 0;
	}

	public static function getConsignmentCost($orderid)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$query = "SELECT shipping_cost FROM ". $table_name ." WHERE order_id=". $orderid;
		$result = $wpdb->get_row($query);
		$cost = 0;
		if(!empty($result)) {
			$cost = $result->shipping_cost;
			if(empty($cost)) {
				$cost = self::getTempConsignmentCost($orderid);
				if(empty($cost)) {
					$cost = 0;
				}
			}
		}
		return $cost;
	}

	public static function prepareArticleData($data,$order,$consignment_number='',$shipCountry=false)
	{
		$isInternational = ($shipCountry != 'AU')?true:false;

        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
		$address = get_post_meta($order_id);
		$chargeCode = self::getChargeCode($order,$consignment_number);
		$notifyCustomerOption = get_option('linksynceparcel_notify_customers');

		$use_dimension = (int)get_option('linksynceparcel_use_dimension');
		if($use_dimension == 1) {
			$validateDimensions = LinksynceparcelValidator::validateArticlePresetDimensions($data);
			if(is_array($validateDimensions)) {
				return $validateDimensions;
			}
		}

		$returnAddress = self::prepareReturnAddress();
		$deliveryInfo = self::prepareDeliveryAddress($address,$order,$data);
		if(is_array($deliveryInfo)) {
			return $deliveryInfo;
		}
		$articlesInfo = self::prepareArticles($data, $order);

		if($isInternational) {
			$returnInternationalAddress = self::prepareInternationalReturnAddress();
			$deliveryInternationalInfo = self::prepareInternationalDeliveryAddress($address,$order,$data);
			if(is_array($deliveryInternationalInfo)) {
				return $deliveryInternationalInfo;
			}
			$articlesInternationalInfo = self::prepareInternationalArticles($data, $order);

			// Validate order weight and insurance value
			$allowedChargeCodes = self::getEParcelChargeCodes();
			$chargeCodeData = $allowedChargeCodes[$chargeCode];

			$validateIntVal = LinksynceparcelValidator::validateInternationalCosignmentsValue($data, $chargeCodeData, $shipCountry, $articlesInfo['total_weight'], $articlesInternationalInfo['totalcost']);
			if(is_array($validateIntVal)) {
				return $validateIntVal;
			}

			$search = array(
				'[[articles]]',
				'[[DELIVERY-ADDRESS]]',
				'[[RETURN-ADDRESS]]',
				'[[ORDER-ID]]',
				'[[CHARGE-CODE]]',
				'[[DANGER-GOODS]]'
			);

			$replace = array(
				$articlesInternationalInfo['content'],
				$deliveryInternationalInfo,
				$returnInternationalAddress,
				self::getIncrementId($order),
				$chargeCode,
				(($data['contains_dangerous_goods']==1) ? 'true' : 'false'),
			);

			$template = file_get_contents(linksynceparcel_DIR.'assets/xml/international-articles-template.xml');
		} else {
			$search = array(
				'[[articles]]',
				'[[RETURN-ADDRESS]]',
				'[[DELIVERY-ADDRESS]]',
				'[[CUSTOMER-EMAIL]]',
				'[[DELIVERY-SIGNATURE]]',
				'[[ORDER-ID]]',
				'[[CHARGE-CODE]]',
				'[[SHIPMENT-ID]]',
				'[[DANGER-GOODS]]',
				'[[printReturnLabels]]',
				'[[deliverPartConsignment]]',
				'[[cashToCollect]]',
				'[[cashToCollectAmount]]',
				'[[emailNotification]]',
				'[[safeDrop]]'
			);

			$replace = array(
				$articlesInfo['info'],
				$returnAddress,
				$deliveryInfo,
				$address['_billing_email'][0],
				(($data['delivery_signature_allowed']==1) ? 'true' : 'false'),
				self::getIncrementId($order),
				$chargeCode,
				self::getIncrementId($order),
				(($data['contains_dangerous_goods']==1) ? 'true' : 'false'),
				'false',
				(($data['partial_delivery_allowed']==1) ? 'Y' : 'N'),
				(isset($data['cash_to_collect']) ? '<cashToCollect>Y</cashToCollect>' : '<cashToCollect>N</cashToCollect>'),
				(isset($data['cash_to_collect']) ? '<cashToCollectAmount>'.number_format($data['cash_to_collect'],2).'</cashToCollectAmount>' : ''),
				(($notifyCustomerOption==1) ? 'Y' : 'N'),
				(($data['safe_drop']==1) ? 'yes' : 'no')
			);

			$template = file_get_contents(linksynceparcel_DIR.'assets/xml/articles-template.xml');
		}

		$articleData = str_replace($search, $replace, $template);
		return array('content' => $articleData, 'charge_code' => $chargeCode, 'total_weight' => $articlesInfo['total_weight'], 'articles' => $articlesInfo['articles']);
	}

	public static function prepareOrderWeightArticleData($data,$order,$consignment_number='',$shipCountry=false)
	{
		$isInternational = ($shipCountry != 'AU')?true:false;

        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
		$address = get_post_meta($order_id);
		$chargeCode = self::getChargeCode($order,$consignment_number);
		$notifyCustomerOption = get_option('linksynceparcel_notify_customers');

		$use_dimension = (int)get_option('linksynceparcel_use_dimension');
		if($use_dimension == 1) {
			$validateDimensions = LinksynceparcelValidator::validateArticlePresetDimensions($data);
			if(is_array($validateDimensions)) {
				return $validateDimensions;
			}
		}

		$returnAddress = self::prepareReturnAddress();
		$deliveryInfo = self::prepareDeliveryAddress($address,$order,$data);
		if(is_array($deliveryInfo)) {
			return $deliveryInfo;
		}
		$articlesInfo = self::prepareOrderWeightArticles($data, $order);

		$returnInternationalAddress = self::prepareInternationalReturnAddress();
		$deliveryInternationalInfo = self::prepareInternationalDeliveryAddress($address,$order);
		if(is_array($deliveryInternationalInfo)) {
			return $deliveryInternationalInfo;
		}
		$articlesInternationalInfo = self::prepareInternationalOrderWeightArticles($data, $order);

		// Validate order weight and insurance value
		$allowedChargeCodes = self::getEParcelChargeCodes();
		$chargeCodeData = $allowedChargeCodes[$chargeCode];

		$validateIntVal = LinksynceparcelValidator::validateInternationalCosignmentsValue($data, $chargeCodeData, $shipCountry, $articlesInfo['total_weight'], $articlesInternationalInfo['totalcost']);
		if(is_array($validateIntVal)) {
			return $validateIntVal;
		}

		if($isInternational) {
			$search = array(
				'[[articles]]',
				'[[DELIVERY-ADDRESS]]',
				'[[RETURN-ADDRESS]]',
				'[[ORDER-ID]]',
				'[[CHARGE-CODE]]',
				'[[DANGER-GOODS]]'
			);

			$replace = array(
				$articlesInternationalInfo['content'],
				$deliveryInternationalInfo,
				$returnInternationalAddress,
				self::getIncrementId($order),
				$chargeCode,
				(($data['contains_dangerous_goods']==1) ? 'true' : 'false')
			);

			$template = file_get_contents(linksynceparcel_DIR.'assets/xml/international-articles-template.xml');
		} else {
			$search = array(
				'[[articles]]',
				'[[RETURN-ADDRESS]]',
				'[[DELIVERY-ADDRESS]]',
				'[[CUSTOMER-EMAIL]]',
				'[[DELIVERY-SIGNATURE]]',
				'[[ORDER-ID]]',
				'[[CHARGE-CODE]]',
				'[[SHIPMENT-ID]]',
				'[[DANGER-GOODS]]',
				'[[printReturnLabels]]',
				'[[deliverPartConsignment]]',
				'[[cashToCollect]]',
				'[[cashToCollectAmount]]',
				'[[emailNotification]]',
				'[[safeDrop]]'
			);

			$replace = array(
				$articlesInfo['info'],
				$returnAddress,
				$deliveryInfo,
				$address['_billing_email'][0],
				(($data['delivery_signature_allowed']==1) ? 'true' : 'false'),
				self::getIncrementId($order),
				$chargeCode,
				self::getIncrementId($order),
				(($data['contains_dangerous_goods']==1) ? 'true' : 'false'),
				'false',
				(($data['partial_delivery_allowed']==1) ? 'Y' : 'N'),
				(isset($data['cash_to_collect']) ? '<cashToCollect>Y</cashToCollect>' : '<cashToCollect>N</cashToCollect>'),
				(isset($data['cash_to_collect']) ? '<cashToCollectAmount>'.number_format($data['cash_to_collect'],2).'</cashToCollectAmount>' : ''),
				(($notifyCustomerOption==1) ? 'Y' : 'N'),
				(($data['safe_drop']==1) ? 'yes' : 'no')
			);
			$template = file_get_contents(linksynceparcel_DIR.'assets/xml/articles-template.xml');
		}

		$articleData = str_replace($search, $replace, $template);
		return array('content' => $articleData, 'charge_code' => $chargeCode, 'total_weight' => $articlesInfo['total_weight']);
	}

	public static function prepareArticleDataBulk($data,$order,$shipCountry=false)
	{
		$isInternational = ($shipCountry != 'AU')?true:false;
        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
		$address = get_post_meta($order_id);
		$chargeCode = self::getChargeCode($order);
		$notifyCustomerOption = get_option('linksynceparcel_notify_customers');

		$use_dimension = (int)get_option('linksynceparcel_use_dimension');
		if($use_dimension == 1 && $data['articles_type'] != "order_weight") {
			$validateDimensions = LinksynceparcelValidator::validateArticlePresetDimensions($data, true);
			if(is_array($validateDimensions)) {
				return $validateDimensions;
			}
		}

		$returnAddress = self::prepareReturnAddress();
		$deliveryInfo = self::prepareDeliveryAddress($address,$order,$data);
		if(is_array($deliveryInfo)) {
			return $deliveryInfo;
		}
		$articlesInfo = self::prepareArticlesBulk($data, $order);


		if($isInternational) {
			$returnInternationalAddress = self::prepareInternationalReturnAddress();
			$deliveryInternationalInfo = self::prepareInternationalDeliveryAddress($address,$order,$data);
			if(is_array($deliveryInternationalInfo)) {
				return $deliveryInternationalInfo;
			}
			$articlesInternationalInfo = self::prepareInternationalArticles($data, $order, true);

			// Validate order weight and insurance value
			$allowedChargeCodes = self::getEParcelChargeCodes();
			$chargeCodeData = $allowedChargeCodes[$chargeCode];

			$validateIntVal = LinksynceparcelValidator::validateInternationalCosignmentsValue($data, $chargeCodeData, $shipCountry, $articlesInfo['total_weight'], $articlesInternationalInfo['totalcost']);
			if(is_array($validateIntVal)) {
				return $validateIntVal;
			}

			$search = array(
				'[[articles]]',
				'[[DELIVERY-ADDRESS]]',
				'[[RETURN-ADDRESS]]',
				'[[ORDER-ID]]',
				'[[CHARGE-CODE]]',
				'[[DANGER-GOODS]]'
			);

			$replace = array(
				$articlesInternationalInfo['content'],
				$deliveryInternationalInfo,
				$returnInternationalAddress,
				self::getIncrementId($order),
				$chargeCode,
				(($data['contains_dangerous_goods']==1) ? 'true' : 'false')
			);

			$template = file_get_contents(linksynceparcel_DIR.'assets/xml/international-articles-template.xml');
		} else {
			$search = array(
				'[[articles]]',
				'[[RETURN-ADDRESS]]',
				'[[DELIVERY-ADDRESS]]',
				'[[CUSTOMER-EMAIL]]',
				'[[DELIVERY-SIGNATURE]]',
				'[[ORDER-ID]]',
				'[[CHARGE-CODE]]',
				'[[SHIPMENT-ID]]',
				'[[DANGER-GOODS]]',
				'[[printReturnLabels]]',
				'[[deliverPartConsignment]]',
				'[[cashToCollect]]',
				'[[cashToCollectAmount]]',
				'[[emailNotification]]',
				'[[safeDrop]]'
			);

			$replace = array(
				$articlesInfo['info'],
				$returnAddress,
				$deliveryInfo,
				$address['_billing_email'][0],
				(($data['delivery_signature_allowed']==1) ? 'true' : 'false'),
				self::getIncrementId($order),
				$chargeCode,
				self::getIncrementId($order),
				(($data['contains_dangerous_goods']==1) ? 'true' : 'false'),
				'false',
				(($data['partial_delivery_allowed']==1) ? 'Y' : 'N'),
				'<cashToCollect>N</cashToCollect>',
				'<cashToCollectAmount/>',
				(($notifyCustomerOption==1) ? 'Y' : 'N'),
				(($data['safe_drop']==1) ? 'yes' : 'no')
			);
			$template = file_get_contents(linksynceparcel_DIR.'assets/xml/articles-template.xml');
		}
		$articleData = str_replace($search, $replace, $template);
		return array('content' => $articleData, 'charge_code' => $chargeCode, 'total_weight' => $articlesInfo['total_weight']);
	}

	public static function prepareReturnAddress()
	{
		$returnAddressLine2 = trim(get_option('linksynceparcel_return_address_line2'));
		if(!empty($returnAddressLine2))
		{
			$returnAddressLine2 = '<returnAddressLine2>'.self::xmlData($returnAddressLine2).'</returnAddressLine2>';
		}
		else
		{
			$returnAddressLine2 = '<returnAddressLine2 />';
		}

		$returnAddressLine3 = trim(get_option('linksynceparcel_return_address_line3'));
		if(!empty($returnAddressLine3))
		{
			$returnAddressLine3 = '<returnAddressLine3>'.self::xmlData($returnAddressLine3).'</returnAddressLine3>';
		}
		else
		{
			$returnAddressLine3 = '<returnAddressLine3 />';
		}

		$returnAddressLine4 = trim(get_option('linksynceparcel_return_address_line4'));
		if(!empty($returnAddressLine4))
		{
			$returnAddressLine4 = '<returnAddressLine4>'.self::xmlData($returnAddressLine4).'</returnAddressLine4>';
		}
		else
		{
			$returnAddressLine4 = '<returnAddressLine4 />';
		}

		$search = array(
			'[[returnAddressLine1]]',
			'[[returnName]]',
			'[[returnPostcode]]',
			'[[returnStateCode]]',
			'[[returnSuburb]]',
			'[[returnAddressLine2]]',
			'[[returnAddressLine3]]',
			'[[returnAddressLine4]]'
		);

		$replace = array(
			self::xmlData(trim(get_option('linksynceparcel_return_address_line1'))),
			self::xmlData(trim(get_option('linksynceparcel_return_address_name'))),
			trim(get_option('linksynceparcel_return_address_postcode')),
			trim(get_option('linksynceparcel_return_address_statecode')),
			self::xmlData(trim(get_option('linksynceparcel_return_address_suburb'))),
			trim($returnAddressLine2),
			trim($returnAddressLine3),
			trim($returnAddressLine4)
		);

		$template = file_get_contents(linksynceparcel_DIR.'assets/xml/article-return-address-template.xml');
		return str_replace($search, $replace, $template);
	}

	public static function xmlData($text)
	{
		$text = trim($text);
		$text = str_replace('&','&amp;',$text);
		$search = array("<",">",'"',"'");
		$replace = array("&lt;","&gt;","&quot;","&apos;");
		return str_replace($search, $replace, $text);
	}

	public static function getNotes($order_id)
	{
		$note = '';
		$args = array(
			'status' => 'approve',
			'post_id' => $order_id
		);
		$comments = get_comments($args);
		foreach($comments as $comment)
		{
			$note .= $comment->comment_content . '<br />';
		}
		return $note;
	}

	public static function prepareDeliveryAddress($address,$order,$data=false)
	{
		$street1 = '<deliveryAddressLine1>'.self::xmlData($address['_shipping_address_1'][0]).'</deliveryAddressLine1>';
		$street2 = '<deliveryAddressLine2/>';
		$street3 = '<deliveryAddressLine3/>';
		$street4 = '<deliveryAddressLine4/>';

		if(isset($address['_shipping_address_2'][0]) && !empty($address['_shipping_address_2'][0]))
		{
			$street2 = '<deliveryAddressLine2>'.self::xmlData($address['_shipping_address_2'][0]).'</deliveryAddressLine2>';
		}

		$city = $address['_shipping_city'][0];
		$state = 'NA';
		if($address['_shipping_state'][0])
		{
			$state = $address['_shipping_state'][0];
			$state = strtoupper($state);
		}
		$postalCode = $address['_shipping_postcode'][0];
		$company = empty($address['_shipping_company'][0]) ? '<deliveryCompanyName/>' : '<deliveryCompanyName>'.self::xmlData($address['_shipping_company'][0]).'</deliveryCompanyName>';
		$firstname = $address['_shipping_first_name'][0].' '.$address['_shipping_last_name'][0];
		$email = $address['_billing_email'][0];
		$phone = $address['_billing_phone'][0];
		$phonestr = $phone;
		$phone = self::getValidPhoneNumber($phone);
		if(!empty($phone)) {
			$strposphone = strpos($phone, '+61');
			if($strposphone !== false) {
				$phone = str_replace('+61', '', $phone);
			}
			$strposphone2 = strpos($phone, '+');
			if($strposphone2 !== false) {
				$phone = str_replace('+', '', $phone);
			}
			$phone = preg_replace('/[^0-9]/s', '', $phone);
			$phonestr = $phone;
		}
		if(!empty($phonestr)) {
			$phonemessage = "";
			if(strlen($phonestr) == 9 && substr($phonestr, 0, 1) == 0) {
				$phonemessage = "Phone number must be atleast 10 digits.";
			} elseif(strlen($phonestr) < 9) {
				$phonemessage = "Phone number must be atleast 9 digits.";
			}

			if(!empty($phonemessage)) {
				return array('error_msg' => $phonemessage );
			}
		}

		$instructions = !empty($data['delivery_instruction'])?$data['delivery_instruction']:'';

		$search = array(
			'[[deliveryCompanyName]]',
			'[[deliveryName]]',
			'[[deliveryEmailAddress]]',
			'[[deliveryAddressLine1]]',
			'[[deliveryAddressLine2]]',
			'[[deliveryAddressLine3]]',
			'[[deliveryAddressLine4]]',
			'[[deliverySuburb]]',
			'[[deliveryStateCode]]',
			'[[deliveryPostcode]]',
			'[[deliveryPhoneNumber]]',
			'[[deliveryInstructions]]'
		);

		$replace = array(
			trim($company),
			self::xmlData(trim($firstname)),
			trim($email),
			trim($street1),
			trim($street2),
			trim($street3),
			trim($street4),
			self::xmlData(trim($city)),
			trim($state),
			trim($postalCode),
			trim($phonestr),
			(!empty($instructions) ? '<deliveryInstructions>'.self::xmlData(($instructions)).'</deliveryInstructions>' : '<deliveryInstructions />')
		);

		$template = file_get_contents(linksynceparcel_DIR.'assets/xml/article-delivery-address-template.xml');
		return str_replace($search, $replace, $template);
	}

	public static function prepareArticles($data, $order,$consignment_number='',$isInternational=false)
	{
		$articlesInfo = '';
		$total_weight = 0;
		$number_of_articles = !empty($data['number_of_articles'])?$data['number_of_articles']:0;
		$start_index = !empty($data['start_index'])?$data['start_index']:0;
		$end_index = !empty($data['end_index'])?$data['end_index']:0;
		$articles = array();

		for($i=$start_index;$i<=$end_index;$i++)
		{
			if($data['articles_type'] == 'Custom')
			{
        		$article = $data['article'.$i];
			}
			else
			{
				$articles_type = $data['articles_type'];
                $articles = LinksynceparcelValidator::get_article_preset($articles_type);

				$article = array();
				$article['description'] = $articles[0];
				$article['weight'] = $articles[1];

                $use_dimension = (int)get_option('linksynceparcel_use_dimension');
                if($use_dimension != 1) {
                    $article['height'] = 5;
                    $article['length'] = 5;
                    $article['width'] = 5;
                } else {
                    $article['height'] = trim($articles[2]);
                    $article['length'] = trim($articles[3]);
                    $article['width'] = trim($articles[4]);
                }

				$use_order_total_weight = (int)get_option('linksynceparcel_use_order_weight');
				if($use_order_total_weight == 1)
				{
					$weight = LinksynceparcelHelper::getOrderWeight($order);
					$weightPerArticle = LinksynceparcelHelper::getAllowedWeightPerArticle();
					if($weight == 0)
					{
						$default_article_weight = get_option('linksynceparcel_default_article_weight');
						if($default_article_weight)
						{
							$weight = $default_article_weight;
						}
					}
					$exactArticles = (int)($weight / $weightPerArticle);
					$totalArticles = $exactArticles;
					$reminderWeight = fmod ($weight, $weightPerArticle);
					if($reminderWeight > 0)
					{
						$totalArticles++;
					}

					if($totalArticles == 0)
					{
						$totalArticles = 1;
					}

					if($weight > $weightPerArticle)
					{
						$weight = $weightPerArticle;
					}
					if($reminderWeight > 0 && $i == $totalArticles)
					{
						$weight = $reminderWeight;
					}
					$article['weight'] = $weight;
				}
			}

			$article['weight'] = number_format($article['weight'],2,'.', '');
			$total_weight += $article['weight'];

			$article['weight'] = self::calculateWeightDefault($article['weight']);

			if($isInternational) {
				$search = array(
					'[[articleDescription]]',
					'[[actualWeight]]',
					'[[width]]',
					'[[height]]',
					'[[length]]'
				);

				$replace = array(
					self::xmlData(trim($article['description'])),
					trim($article['weight']),
					empty($article['width'])?5:$article['width'],
					empty($article['height'])?5:$article['height'],
					empty($article['length'])?5:$article['length']
				);

				$template = file_get_contents(linksynceparcel_DIR.'assets/xml/international-article-template.xml');
			} else {
				$search = array(
					'[[actualWeight]]',
					'[[articleDescription]]',
					'[[height]]',
					'[[length]]',
					'[[width]]',
					'[[isTransitCoverRequired]]',
					'[[transitCoverAmount]]',
					'[[articleNumber]]'
				);

				$replace = array(
					trim($article['weight']),
					self::xmlData(trim($article['description'])),
					$article['height'],
					$article['length'],
					'<width>'.$article['width'].'</width>',
					($data['transit_cover_required'] ? 'Y' : 'N'),
					($data['transit_cover_required'] ? trim($data['transit_cover_amount']) : 0),
					(isset($article['article_number']) ? '<articleNumber>'.trim($article['article_number']).'</articleNumber>' : '')
				);

				$template = file_get_contents(linksynceparcel_DIR.'assets/xml/article-template.xml');
			}

			$articlesInfo .= str_replace($search, $replace, $template);
		}
		return array('info' => $articlesInfo, 'total_weight' => $total_weight, 'articles' => $article);
	}

	public static function prepareOrderWeightArticles($data, $order,$consignment_number='',$isInternational=false)
	{
		$articlesInfo = '';
		$number_of_articles = $data['number_of_articles'];
		$start_index = $data['start_index'];
		$end_index = $data['end_index'];
		$articles = array();

		$totalWeight = 0;

		for($i=$start_index;$i<=$end_index;$i++)
		{
			if($data['articles_type'] == 'Custom')
			{
        		$article = $data['article'.$i];
			}
			else
			{
				$articles_type = $data['articles_type'];
                $articles = LinksynceparcelValidator::get_article_preset($articles_type);

				$article = array();
				$article['description'] = $articles[0];
				$article['weight'] = $articles[1];

                $use_dimension = (int)get_option('linksynceparcel_use_dimension');
                if($use_dimension != 1) {
                    $article['height'] = 5;
                    $article['length'] = 5;
                    $article['width'] = 5;
                } else {
                    $article['height'] = $articles[2];
                    $article['length'] = $articles[3];
                    $article['width'] = $articles[4];
                }
			}

			$article['weight'] = number_format($article['weight'],2,'.', '');
			$totalWeight += $article['weight'];

			$article['weight'] = self::calculateWeightDefault($article['weight']);

			if($isInternational) {
				$search = array(
					'[[articleDescription]]',
					'[[actualWeight]]',
					'[[width]]',
					'[[height]]',
					'[[length]]'
				);

				$replace = array(
					self::xmlData(trim($article['description'])),
					trim($article['weight']),
					5,
					5,
					5
				);

				$template = file_get_contents(linksynceparcel_DIR.'assets/xml/international-article-template.xml');
			} else {
				$search = array(
					'[[actualWeight]]',
					'[[articleDescription]]',
					'[[height]]',
					'[[length]]',
					'[[width]]',
					'[[isTransitCoverRequired]]',
					'[[transitCoverAmount]]',
					'[[articleNumber]]'
				);

				$replace = array(
					trim($article['weight']),
					self::xmlData(trim($article['description'])),
					5,
					5,
					'<width>5</width>',
					($data['transit_cover_required'] ? 'Y' : 'N'),
					($data['transit_cover_required'] ? trim($data['transit_cover_amount']) : 0),
					(isset($article['article_number']) ? '<articleNumber>'.trim($article['article_number']).'</articleNumber>' : '')
				);

				$template = file_get_contents(linksynceparcel_DIR.'assets/xml/article-template.xml');
			}
			$articlesInfo .= str_replace($search, $replace, $template);
		}
		return array('info' => $articlesInfo, 'total_weight' => $totalWeight, 'articles' => $article);
	}

	public static function prepareArticlesBulk($data, $order, $isInternational=false)
	{
		$articlesInfo = '';
		$total_weight = 0;
		$articles_type = $data['articles_type'];
		if($articles_type == 'order_weight')
		{
			$weight = LinksynceparcelHelper::getOrderWeight($order);
			if($weight == 0)
			{
				$default_article_weight = get_option('linksynceparcel_default_article_weight');

				if($default_article_weight)
				{
					$weight = $default_article_weight;
				}
			}
			$weightPerArticle = LinksynceparcelHelper::getAllowedWeightPerArticle();
			$exactArticles = (int)($weight / $weightPerArticle);
			$totalArticles = $exactArticles;
			$reminderWeight = fmod ($weight, $weightPerArticle);
			if($reminderWeight > 0)
			{
				$totalArticles++;
			}

			if($totalArticles == 0)
			{
				$totalArticles = 1;
			}

			for($i=1;$i<=$totalArticles;$i++)
			{
				$article = array();
				$article['description'] = 'Article '.$i;

				if($reminderWeight > 0 && $i == $totalArticles)
				{
					$article['weight'] = $reminderWeight;
				}
				else
				{
					$article['weight'] = $weightPerArticle;
				}
				$article['height'] = 5;
				$article['length'] = 5;
				$article['width'] = 5;

				$article['weight'] = number_format($article['weight'],2,'.', '');

				$total_weight += $article['weight'];

				$article['weight'] = self::calculateWeightDefault($article['weight']);

				if($isInternational) {
					$search = array(
						'[[articleDescription]]',
						'[[actualWeight]]',
						'[[width]]',
						'[[height]]',
						'[[length]]'
					);

					$replace = array(
						self::xmlData(trim($article['description'])),
						trim($article['weight']),
						trim($article['width']),
						trim($article['height']),
						trim($article['length'])
					);

					$template = file_get_contents(linksynceparcel_DIR.'assets/xml/international-article-template.xml');
				} else {
					$search = array(
						'[[actualWeight]]',
						'[[articleDescription]]',
						'[[height]]',
						'[[length]]',
						'[[width]]',
						'[[isTransitCoverRequired]]',
						'[[transitCoverAmount]]',
						'[[articleNumber]]'
					);

					$replace = array(
						$article['weight'],
						self::xmlData($article['description']),
						$article['height'],
						$article['length'],
						'<width>'.$article['width'].'</width>',
						($data['transit_cover_required'] ? 'Y' : 'N'),
						($data['transit_cover_required'] ? $data['transit_cover_amount'] : 0),
						''
					);

					$template = file_get_contents(linksynceparcel_DIR.'assets/xml/article-template.xml');
				}
				$articlesInfo .= str_replace($search, $replace, $template);
			}
		}
		else
		{
			$article = array();
			$articles = LinksynceparcelValidator::get_article_preset($articles_type);
            
			$article['description'] = $articles[0];
			$article['weight'] = $articles[1];
            $use_dimension = (int)get_option('linksynceparcel_use_dimension');

            if($use_dimension != 1) {
                $article['height'] = 5;
                $article['length'] = 5;
                $article['width'] = 5;
            } else {
                $article['height'] = $articles[2];
                $article['length'] = $articles[3];
                $article['width'] = $articles[4];
            }

			$article['weight'] = number_format($article['weight'],2,'.', '');
			$total_weight += $article['weight'];

			$article['weight'] = self::calculateWeightDefault($article['weight']);

			if($isInternational) {
				$search = array(
					'[[articleDescription]]',
					'[[actualWeight]]',
					'[[width]]',
					'[[height]]',
					'[[length]]'
				);

				$replace = array(
					self::xmlData(trim($article['description'])),
					trim($article['weight']),
					trim($article['width']),
					trim($article['height']),
					trim($article['length'])
				);

				$template = file_get_contents(linksynceparcel_DIR.'assets/xml/international-article-template.xml');
			} else {
				$search = array(
					'[[actualWeight]]',
					'[[articleDescription]]',
					'[[height]]',
					'[[length]]',
					'[[width]]',
					'[[isTransitCoverRequired]]',
					'[[transitCoverAmount]]',
					'[[articleNumber]]'
				);

				$replace = array(
					trim($article['weight']),
					self::xmlData(trim($article['description'])),
					trim($article['height']),
					trim($article['length']),
					'<width>'.trim($article['width']).'</width>',
					($data['transit_cover_required'] ? 'Y' : 'N'),
					($data['transit_cover_required'] ? trim($data['transit_cover_amount']) : 0),
					''
				);

				$template = file_get_contents(linksynceparcel_DIR.'assets/xml/article-template.xml');
			}
			$articlesInfo .= str_replace($search, $replace, $template);
		}
		return array('info' => $articlesInfo, 'total_weight' => $total_weight);
	}

	public static function getIncrementId($order)
	{
		return $order->get_order_number();
	}

	public static function insertConsignment($order_id,$consignmentNumber,$data,$manifestNumber,$chargeCode,$total_weight,$shipCountry,$consignmentcost)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$timestamp = time();
		$date = date('Y-m-d H:i:s', $timestamp);
		$query = "INSERT {$table_name} SET order_id = '{$order_id}', consignment_number='{$consignmentNumber}', add_date='".$date."', delivery_signature_allowed = '".$data['delivery_signature_allowed']."', print_return_labels='".$data['print_return_labels']."', contains_dangerous_goods='".$data['contains_dangerous_goods']."', partial_delivery_allowed = '".$data['partial_delivery_allowed']."', cash_to_collect='".(isset($data['cash_to_collect'])?$data['cash_to_collect']:'')."', email_notification = '".$data['email_notification']."', chargecode = '".$chargeCode."', weight = '".$total_weight."', delivery_country = '". $shipCountry ."', delivery_instruction = '". addslashes($data['delivery_instruction']) ."', safe_drop = '".$data['safe_drop']."', date_process = '".$data['date_process']."', shipping_cost = '".$consignmentcost."'";
		$manifestNumber = trim($manifestNumber);
		if(strtolower($manifestNumber) != 'unassinged')
		{
			$query .= ", manifest_number = '".$manifestNumber."', is_next_manifest = 1";
		}
		$wpdb->query($query);

		if($shipCountry != "AU") {
			self::insertInternationalConsignment($order_id,$consignmentNumber,$data,$shipCountry);
		}
	}

	public static function insertInternationalConsignment($order_id,$consignmentNumber,$data,$shipCountry) {
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_international_fields";
		$timestamp = time();
		$date = date('Y-m-d H:i:s', $timestamp);

		$product_classification = isset($data['product_classification'])?$data['product_classification']:get_option('linksynceparcel_product_classification');
		$declared_value = isset($data['declared_value'])?1:0;
		$has_commercial_value = isset($data['has_commercial_value'])?1:0;
		$country_origin = $data['country_origin'];
		if(!isset($country_origin)) {
			$country_origin = get_option('linksynceparcel_country_origin');
		}
		$hs_tariff = $data['hs_tariff'];
		if(!isset($hs_tariff)) {
			$hs_tariff = get_option('linksynceparcel_hs_tariff');
		}

		$insuranceValue = (isset($data['order_value_insurance']))?self::getOrderProdItems($order_id, $data, true):$data['insurance_value'];

		$query = "INSERT {$table_name} SET order_id = '{$order_id}', consignment_number='{$consignmentNumber}', add_date='".$date."', insurance = '". $data['insurance'] ."', insurance_value = '". $insuranceValue ."', export_declaration_number='".$data['export_declaration_number']."', declared_value='". $declared_value ."', declared_value_text = '".$data['declared_value_text']."', has_commercial_value='". $has_commercial_value ."', product_classification = ". $product_classification .", product_classification_text = '".$data['product_classification_text']."', country_origin = '".$country_origin."', hs_tariff = '". $hs_tariff ."', default_contents = '". $data['default_contents'] ."', ship_country = '". $shipCountry ."'";

		$wpdb->query($query);
	}

	public static function updateConsignment($order_id,$consignmentNumber,$data,$manifestNumber,$chargeCode,$total_weight,$shipCountry=false, $oldconsignmentNumber,$consignmentcost)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$timestamp = time();
		$date = date('Y-m-d H:i:s', $timestamp);

		$query = "DELETE FROM {$table_name} WHERE consignment_number='{$oldconsignmentNumber}'";
		$wpdb->query($query);

		$query = "INSERT {$table_name} SET order_id = '{$order_id}', consignment_number='{$consignmentNumber}', add_date='".$date."', delivery_signature_allowed = '".$data['delivery_signature_allowed']."', print_return_labels='".$data['print_return_labels']."', contains_dangerous_goods='".$data['contains_dangerous_goods']."', partial_delivery_allowed = '".$data['partial_delivery_allowed']."', cash_to_collect='".(isset($data['cash_to_collect'])?$data['cash_to_collect']:'')."', email_notification = '".$data['email_notification']."', chargecode = '".$chargeCode."', weight = '".$total_weight."', delivery_country = '". $shipCountry ."', delivery_instruction = '". addslashes($data['delivery_instruction']) ."', safe_drop = '".$data['safe_drop']."', date_process = '".$data['date_process']."', shipping_cost = '".$consignmentcost."'";

		$manifestNumber = trim($manifestNumber);
		if(strtolower($manifestNumber) != 'unassinged')
		{
			$query .= ", manifest_number = '".$manifestNumber."', is_next_manifest = 1";
		}
		else
		{
			$query .= ", manifest_number = '', is_next_manifest = 0";
		}
		$wpdb->query($query);

		if($shipCountry != false && $shipCountry != "AU") {
			self::updateInternationalConsignment($consignmentNumber,$data,$shipCountry);
		}

		$filename = $consignmentNumber.'.pdf';
		$filepath = linksynceparcel_DIR.'assets/label/consignment/'.$filename;
		if(file_exists($filepath))
		{
			unlink($filepath);
		}
		$filepath_1 = linksynceparcel_UPLOAD_DIR.'consignment/'.$filename;
		if(file_exists($filepath_1))
		{
			unlink($filepath_1);
		}

		$filepath2 = linksynceparcel_DIR.'assets/label/returnlabels/'.$filename;
		if(file_exists($filepath2))
		{
			unlink($filepath2);
		}
		$filepath2_1 = linksynceparcel_UPLOAD_DIR.'returnlabels/'.$filename;
		if(file_exists($filepath2_1))
		{
			unlink($filepath2_1);
		}
	}

	public static function updateInternationalConsignment($consignmentNumber,$data,$shipCountry) {
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_international_fields";
		$timestamp = time();
		$date = date('Y-m-d H:i:s', $timestamp);

		$product_classification = isset($data['product_classification'])?$data['product_classification']:get_option('linksynceparcel_product_classification');
		$declared_value = isset($data['declared_value'])?1:0;
		$has_commercial_value = isset($data['has_commercial_value'])?1:0;
		$country_origin = $data['country_origin'];
		if(!isset($country_origin)) {
			$country_origin = get_option('linksynceparcel_country_origin');
		}
		$hs_tariff = $data['hs_tariff'];
		if(!isset($hs_tariff)) {
			$hs_tariff = get_option('linksynceparcel_hs_tariff');
		}

		$query = "UPDATE {$table_name} SET modify_date='". $date ."', safe_drop = '".$data['safe_drop']."', export_declaration_number='".$data['export_declaration_number']."', declared_value='". $declared_value ."', declared_value_text = '".$data['declared_value_text']."', has_commercial_value='". $has_commercial_value ."', product_classification = ". $product_classification .", product_classification_text = '".$data['product_classification_text']."', country_origin = '".$country_origin."', hs_tariff = '". $hs_tariff ."', default_contents = '". $data['default_contents'] ."'";
		$query .= ' WHERE consignment_number ="'. $consignmentNumber .'"';
		$wpdb->query($query);
	}

	public static function getArticles($id_order, $consignment_number)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_article";
		$query = "SELECT * FROM {$table_name} WHERE order_id = ". $id_order ." AND consignment_number='". $consignment_number ."'";
		return $wpdb->get_results($query);
	}

	public static function getArticle($articleNumber)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_article";
		$query = "SELECT * FROM {$table_name} WHERE article_number='{$articleNumber}'";
		$articles = $wpdb->get_results($query);
		foreach($articles as $article)
		{
			return $article;
		}
	}

	public static function deleteArticle($order_id,$consignmentNumber, $articleNumber)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_article";
		$deleteToBeArticle = self::getArticle($articleNumber);
		$query = "DELETE FROM {$table_name} WHERE order_id = '{$order_id}' AND consignment_number='{$consignmentNumber}' AND article_number='{$articleNumber}'";
		$wpdb->query($query);
		return $deleteToBeArticle;
	}

	public static function updateArticles($order_id, $consignmentNumber, $articles, $data,$content,$oldconsignmentNumber=false)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_article";

		$rm_consignmentNumber = $consignmentNumber;
		if($oldconsignmentNumber) {
			$rm_consignmentNumber = $oldconsignmentNumber;
		}
		$query = "DELETE FROM {$table_name} WHERE consignment_number='{$rm_consignmentNumber}'";
		$wpdb->query($query);

		try
		{
			$query = '';
			$articleNumbers = $articles->article;
			LinksynceparcelHelper::log('articles : '.print_r($articles, true));
			LinksynceparcelHelper::log('articles content : '.print_r($content, true));
			$xml = simplexml_load_string($content);
			if($xml)
			{
				$j = 0;
				foreach($xml->articles->article as $article)
				{
					$articleNumber = (is_array($articleNumbers) ? $articleNumbers[$j++] : $articleNumbers);
					$actualWeight = $article->actualWeight;
					$articleDescription = $article->articleDescription;
					$cubicWeight = $article->cubicWeight;
					$height = $article->height;
					$isTransitCoverRequired = $article->isTransitCoverRequired;
					$length = $article->length;
					$width = $article->width;
					$transitCoverAmount = $article->transitCoverAmount;

					$query = "INSERT {$table_name} SET order_id = '{$order_id}', consignment_number='{$consignmentNumber}',  actual_weight='{$actualWeight}', article_description='". self::mres(self::xmlData($articleDescription)) ."', article_number='{$articleNumber}', cubic_weight='{$cubicWeight}', height='{$height}', width='{$width}', is_transit_cover_required='{$isTransitCoverRequired}', length='{$length}', transit_cover_amount='{$transitCoverAmount}';";
					$wpdb->query($query);
				}
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}

	public static function insertBulkArticles($order_id, $consignmentNumber, $articles, $data)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_article";
		try
		{
			$query = '';
			$articleNumbers = $articles->article;

			$articles_type = $data['articles_type'];
			$articleTemp = explode('<=>',$articles_type);

			$article = array();
			$article['description'] = $articleTemp[0];
			$article['weight'] = $articleTemp[1];

            $use_dimension = (int)get_option('linksynceparcel_use_dimension');

            if($use_dimension != 1) {
                $article['height'] = 5;
                $article['length'] = 5;
                $article['width'] = 5;
            } else {
                $article['height'] = $articleTemp[2];
                $article['length'] = $articleTemp[3];
                $article['width'] = $articleTemp[4];
            }
			$actualWeight = $article['weight'];
			$articleDescription = $article['description'];
			$articleNumber = (is_array($articleNumbers) ? $articleNumbers[$j] : $articleNumbers);
			$cubicWeight = 0;
			$height = $article['height'];
			$length = $article['length'];
			$width = $article['width'];
			$isTransitCoverRequired = ($data['transit_cover_required'] ? 'Y' : 'N');
			$transitCoverAmount = ($data['transit_cover_required'] ? $data['transit_cover_amount'] : 0);

			$query .= "INSERT {$table_name} SET order_id = '{$order_id}', consignment_number='{$consignmentNumber}',  actual_weight='{$actualWeight}', article_description='". self::mres($articleDescription) ."', article_number='{$articleNumber}', cubic_weight='{$cubicWeight}', height='{$height}', width='{$width}', is_transit_cover_required='{$isTransitCoverRequired}', length='{$length}', transit_cover_amount='{$transitCoverAmount}';";

			$wpdb->query($query);
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}



	public static function getManifest($manifest_number)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_manifest";
		$query = "SELECT * FROM {$table_name} WHERE manifest_number = '{$manifest_number}'";
		$results = $wpdb->get_results($query);
		foreach($results as $result)
		{
			return $result;
		}
		return false;
	}

	public static function deleteManifest()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$query = "SELECT * FROM {$table_name} WHERE despatched = 0 and is_next_manifest = 1";
		$results = $wpdb->get_results($query);
		if(count($results) == 0)
		{
			$table_name = $wpdb->prefix . "linksynceparcel_manifest";
			$query = "DELETE FROM {$table_name} WHERE despatch_date is null or despatch_date = '' order by manifest_number desc limit 1";
			$wpdb->query($query);
		}
		update_option('linksynceparcel_manifest_sync', 1);
	}

	public static function deleteManifest2($manifestNumber)
	{
		global $wpdb;
		if(!empty($manifestNumber))
		{
			$table_name = $wpdb->prefix . "linksynceparcel_consignment";
			$query = "SELECT * FROM {$table_name} WHERE manifest_number = '$manifestNumber'";
			$results = $wpdb->get_results($query);
			if(count($results) == 0)
			{
				$table_name = $wpdb->prefix . "linksynceparcel_manifest";
				$query = "DELETE FROM {$table_name} WHERE manifest_number = '$manifestNumber'";
				$wpdb->query($query);
			}
		}
	}

	public static function insertManifest($manifestNumber,$numberOfArticles=0,$numberOfConsignments=0)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_manifest";
		if(!self::getManifest($manifestNumber))
		{
			$manifestNumber = trim($manifestNumber);
			if(strtolower($manifestNumber) != 'unassinged')
			{
				$query = "INSERT {$table_name} SET manifest_number='{$manifestNumber}', number_of_articles = '{$numberOfArticles}', number_of_consignments='{$numberOfConsignments}'";
				$wpdb->query($query);
			}
		}
		update_option('linksynceparcel_manifest_sync', 1);
	}

	public static function updateManifest($manifestNumber,$numberOfArticles,$numberOfConsignments)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_manifest";
		if(self::getManifest($manifestNumber))
		{
			$query = "UPDATE {$table_name} SET number_of_articles = '{$numberOfArticles}', number_of_consignments='{$numberOfConsignments}' WHERE manifest_number='{$manifestNumber}'";
			$wpdb->query($query);
		}
		else
		{
			$query = "INSERT {$table_name} SET number_of_articles = '{$numberOfArticles}', manifest_number='{$manifestNumber}', number_of_consignments='{$numberOfConsignments}'";
			$wpdb->query($query);
		}
	}

	public static function labelCreate($consignmentNumber)
    {
		try
		{
			$labelContent = LinksynceparcelApi::getLabelsByConsignments($consignmentNumber);
			if($labelContent)
			{
				$filename = $consignmentNumber.'.pdf';
				$filepath = linksynceparcel_UPLOAD_DIR.'consignment/'.$filename;
				$handle = fopen($filepath,'wb');
				fwrite($handle, $labelContent);
				fclose($handle);
				self::updateConsignmentTable($consignmentNumber,'label',$filename);
				self::updateConsignmentTable($consignmentNumber,'is_label_created',1);
			}
		}
		catch(Exception $e)
		{
			;//log
		}
	}

	public static function returnLabelCreate($consignmentNumber)
    {
		try
		{
			$print_return_labels = self::getConsignmentSpecific($consignmentNumber,'print_return_labels');
			if($print_return_labels)
			{
				$labelContent = LinksynceparcelApi::getReturnLabelsByConsignments($consignmentNumber);
				if($labelContent)
				{
					$filename = $consignmentNumber.'.pdf';
					$filepath = linksynceparcel_UPLOAD_DIR.'returnlabels/'.$filename;
					$handle = fopen($filepath,'wb');
					fwrite($handle, $labelContent);
					fclose($handle);
					self::updateConsignmentTable($consignmentNumber,'is_return_label_printed',0);
				}
			}
		}
		catch(Exception $e)
		{
			;//log
		}
	}

	public static function updateConsignmentTable($consignmentNumber,$columnName, $value)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$query = "UPDATE {$table_name} SET `{$columnName}` = '{$value}' WHERE `consignment_number`='{$consignmentNumber}'";
		$wpdb->query($query);
	}

	public static function updateConsignmentTableByManifest($manifestNumber,$columnName, $value)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$query = "UPDATE {$table_name} SET {$columnName} = '{$value}' WHERE manifest_number='{$manifestNumber}'";
		$wpdb->query($query);
	}

	public static function getOrdersByManifest($manifestNumber)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$query = "SELECT order_id, consignment_number FROM {$table_name} WHERE manifest_number = '{$manifestNumber}' group by order_id";
		return $wpdb->get_results($query);
	}

	public static function deleteConsignment($consignmentNumber)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$query = "DELETE FROM {$table_name} WHERE consignment_number='{$consignmentNumber}'";
		$wpdb->query($query);

		$table_name = $wpdb->prefix . "linksynceparcel_article";
		$query = "DELETE FROM {$table_name} WHERE consignment_number='{$consignmentNumber}'";
		$wpdb->query($query);
	}

	public static function prepareModifiedArticle($order,$consignment_number)
	{
		$articleData = self::prepareModifiedArticleData($order,$consignment_number);
		if(!isset($articleData['error_msg'])) {
			$content = $articleData['content'];
			$chargeCode = $articleData['charge_code'];
			return array('content' => $content, 'charge_code' => $chargeCode);
		} else {
			return $articleData;
		}
	}

	public static function prepareModifiedArticleData($order,$consignment_number)
	{
        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
		$address = get_post_meta($order_id);
		$returnAddress = self::prepareReturnAddress();
		$deliveryInfo = self::prepareDeliveryAddress($address,$order);
		if(is_array($deliveryInfo)) {
			return $deliveryInfo;
		}
		$articlesInfo = self::prepareModifiedArticles($order,$consignment_number);
		$consignment = self::getConsignment($consignment_number);

		$search = array(
			'[[articles]]',
			'[[RETURN-ADDRESS]]',
			'[[DELIVERY-ADDRESS]]',
			'[[CUSTOMER-EMAIL]]',
			'[[DELIVERY-SIGNATURE]]',
			'[[ORDER-ID]]',
			'[[CHARGE-CODE]]',
			'[[SHIPMENT-ID]]',
			'[[DANGER-GOODS]]',
			'[[printReturnLabels]]',
			'[[deliverPartConsignment]]',
			'[[cashToCollect]]',
  			'[[cashToCollectAmount]]',
			'[[emailNotification]]',
			'[[safeDrop]]'
		);

		$chargeCode = self::getChargeCode($order,$consignment_number);

		$replace = array(
			$articlesInfo['info'],
			$returnAddress,
			$deliveryInfo,
			$address['_billing_email'][0],
			($consignment->delivery_signature_allowed ? 'true' : 'false'),
			self::getIncrementId($order),
			$chargeCode,
			self::getIncrementId($order),
			($consignment->contains_dangerous_goods ? 'true' : 'false'),
			($consignment->print_return_labels ? 'true' : 'false'),
			($consignment->partial_delivery_allowed ? 'Y' : 'N'),
			( (isset($consignment->cash_to_collect) && !empty($consignment->cash_to_collect) ) ? '<cashToCollect>Y</cashToCollect>' : '<cashToCollect>N</cashToCollect>'),
			( (isset($consignment->cash_to_collect) && !empty($consignment->cash_to_collect) ) ? '<cashToCollectAmount>'.number_format($consignment->cash_to_collect,2).'</cashToCollectAmount>' : ''),
			($consignment->email_notification ? 'Y' : 'N'),
			($data['safe_drop']==1 ? 'yes' : 'no')
		);
		$template = file_get_contents(linksynceparcel_DIR.'assets/xml/articles-template.xml');
		$content = str_replace($search, $replace, $template);
		return array('content' => $content, 'charge_code' => $chargeCode, 'total_weight' => $articlesInfo['total_weight']);
	}

	public static function prepareModifiedArticles($order,$consignment_number)
	{
		$articlesInfo = '';
		$total_weight = 0;
        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
		$articles = LinksynceparcelHelper::getArticles($order_id, $consignment_number);
		foreach($articles as $article)
		{
			$search = array(
				'[[actualWeight]]',
				'[[articleDescription]]',
				'[[height]]',
				'[[length]]',
				'[[width]]',
				'[[isTransitCoverRequired]]',
				'[[transitCoverAmount]]',
				'[[articleNumber]]'
			);

			$default_width = 0;
			$use_article_dimensions = (int)get_option('linksynceparcel_use_dimension');
			if($use_article_dimensions == 1)
			{
				$default_width = get_option('linksynceparcel_default_article_width');
			}

			$total_weight += $article->actual_weight;

			$article->actual_weight = self::calculateWeightDefault($article->actual_weight);

			$replace = array(
				trim($article->actual_weight),
				trim($article->article_description),
				trim($article->height),
				trim($article->length),
				($article->width ? '<width>'.trim($article->width).'</width>' : '<width>'.trim(get_option('linksynceparcel_default_article_width')).'</width>'),
				$article->is_transit_cover_required,
				(($article->is_transit_cover_required == 'Y') ? trim($article->transit_cover_amount) : 0),
				'<articleNumber>'.trim($article->article_number).'</articleNumber>'
			);

			$template = file_get_contents(linksynceparcel_DIR.'assets/xml/article-template.xml');
			$articlesInfo .= str_replace($search, $replace, $template);
		}
		return array('info' => $articlesInfo, 'total_weight' => $total_weight);
	}

	public static function prepareAddArticle($data,$order,$consignmentNumber)
	{
		$articleData = self::prepareAddArticleData($data,$order,$consignmentNumber);
		if(!isset($articleData['error_msg'])) {
			$content = $articleData['content'];
			$chargeCode = $articleData['charge_code'];
			return array('content' => $content, 'charge_code' => $chargeCode);
		} else {
			return $articleData;
		}
	}

	public static function prepareAddArticleData($data, $order,$consignment_number)
	{
        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
		$address = get_post_meta($order_id);
		$returnAddress = self::prepareReturnAddress();
		$deliveryInfo = self::prepareDeliveryAddress($address,$order,$data);
		if(is_array($deliveryInfo)) {
			return $deliveryInfo;
		}
		$articlesInfo = self::prepareAddArticles($order,$data, $consignment_number);

		$search = array(
			'[[articles]]',
			'[[RETURN-ADDRESS]]',
			'[[DELIVERY-ADDRESS]]',
			'[[CUSTOMER-EMAIL]]',
			'[[DELIVERY-SIGNATURE]]',
			'[[ORDER-ID]]',
			'[[CHARGE-CODE]]',
			'[[SHIPMENT-ID]]',
			'[[DANGER-GOODS]]',
			'[[printReturnLabels]]',
			'[[deliverPartConsignment]]',
			'[[cashToCollect]]',
  			'[[cashToCollectAmount]]',
			'[[emailNotification]]',
			'[[safeDrop]]'
		);

		$chargeCode = self::getChargeCode($order,$consignment_number);

		$replace = array(
			$articlesInfo['info'],
			$returnAddress,
			$deliveryInfo,
			$address['_billing_email'][0],
			(($data['delivery_signature_allowed']==1) ? 'true' : 'false'),
			self::getIncrementId($order),
			$chargeCode,
			self::getIncrementId($order),
			(($data['contains_dangerous_goods']==1) ? 'true' : 'false'),
			($data['print_return_labels'] ? 'true' : 'false'),
			(($data['partial_delivery_allowed']==1) ? 'Y' : 'N'),
			(isset($data['cash_to_collect']) ? '<cashToCollect>Y</cashToCollect>' : '<cashToCollect>N</cashToCollect>'),
			(isset($data['cash_to_collect']) ? '<cashToCollectAmount>'.number_format($data['cash_to_collect'],2).'</cashToCollectAmount>' : ''),
			($data['email_notification'] ? 'Y' : 'N'),
			($data['safe_drop']==1 ? 'yes' : 'no')
		);
		$template = file_get_contents(linksynceparcel_DIR.'assets/xml/articles-template.xml');
		$content = str_replace($search, $replace, $template);
		return array('content' => $content, 'charge_code' => $chargeCode, 'total_weight' => $articlesInfo['total_weight']);
	}

	public static function prepareAddArticles($order,$data, $consignment_number)
	{
		$articlesInfo = '';
		$total_weight = 0;

        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
		$articles = LinksynceparcelHelper::getArticles($order_id, $consignment_number);
		foreach($articles as $article)
		{
			$search = array(
				'[[actualWeight]]',
				'[[articleDescription]]',
				'[[height]]',
				'[[length]]',
				'[[width]]',
				'[[isTransitCoverRequired]]',
				'[[transitCoverAmount]]',
				'[[articleNumber]]'
			);

			$total_weight += $article->actual_weight;

			$article->actual_weight = self::calculateWeightDefault($article->actual_weight);

			$default_width = 0;
			$use_article_dimensions = (int)get_option('linksynceparcel_use_dimension');
			if($use_article_dimensions == 1)
			{
				$default_width = get_option('linksynceparcel_default_article_width');
			}

			$replace = array(
				trim($article->actual_weight),
				trim($article->article_description),
				trim($article->height),
				trim($article->length),
				'<width>'.trim($article->width).'</width>',
				$article->is_transit_cover_required,
				( ($article->is_transit_cover_required == 'Y') ? trim($article->transit_cover_amount) : 0),
				'<articleNumber>'.trim($article->article_number).'</articleNumber>'
			);

			$template = file_get_contents(linksynceparcel_DIR.'assets/xml/article-template.xml');
			$articlesInfo .= str_replace($search, $replace, $template);
		}

		$search = array(
				'[[actualWeight]]',
				'[[articleDescription]]',
				'[[height]]',
				'[[length]]',
				'[[width]]',
				'[[isTransitCoverRequired]]',
				'[[transitCoverAmount]]',
				'[[articleNumber]]'
		);

		if($data['articles_type'] == 'Custom')
		{
			$article = $data['article'];
		}
		else
		{
			$articles_type = $data['articles_type'];
			$articles = explode('<=>',$articles_type);

			$article = array();
			$article['description'] = $articles[0];
			$article['weight'] = $articles[1];
            $use_dimension = (int)get_option('linksynceparcel_use_dimension');
            if($use_dimension != 1) {
                $article['height'] = 5;
                $article['length'] = 5;
                $article['width'] = 5;
            } else {
                $article['height'] = $articles[2];
                $article['length'] = $articles[3];
                $article['width'] = $articles[4];
            }

			$use_order_total_weight = (int)get_option('linksynceparcel_use_order_weight');;
			if($use_order_total_weight == 1)
			{
				$weight = LinksynceparcelHelper::getOrderWeight($order);
				$weightPerArticle = LinksynceparcelHelper::getAllowedWeightPerArticle();
				if($weight == 0)
				{
					$default_article_weight = get_option('linksynceparcel_default_article_width');
					if($default_article_weight)
					{
						$weight = $default_article_weight;
					}
				}
				if($weight > $weightPerArticle)
				{
					$weight = $weightPerArticle;
				}
				$article['weight'] = $weight;
			}
		}

		$article['weight'] = number_format($article['weight'],2,'.', '');
		$total_weight += $article['weight'];

		$article['weight'] = self::calculateWeightDefault($article['weight']);

		$replace = array(
			trim($article['weight']),
			self::xmlData(trim($article['description'])),
			trim($article['height']),
			trim($article['length']),
			'<width>'.trim($article['width']).'</width>',
			($data['transit_cover_required'] ? 'Y' : 'N'),
			($data['transit_cover_required'] ? trim($data['transit_cover_amount']) : 0),
			''
		);

		$template = file_get_contents(linksynceparcel_DIR.'assets/xml/article-template.xml');
		$articlesInfo .= str_replace($search, $replace, $template);

		return array('info' => $articlesInfo, 'total_weight' => $total_weight);
	}

	public static function prepareUpdateArticle($data,$order,$consignmentNumber)
	{
		$articleData = self::prepareUpdateArticleData($data,$order,$consignmentNumber);
		if(!isset($articleData['error_msg'])) {
			$content = $articleData['content'];
			$chargeCode = $articleData['charge_code'];
			return array('content' => $content, 'charge_code' => $chargeCode);
		} else {
			return $articleData;
		}
	}

	public static function prepareUpdateArticleData($data, $order,$consignment_number='')
	{
        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
		$address = get_post_meta($order_id);
		$returnAddress = self::prepareReturnAddress();
		$deliveryInfo = self::prepareDeliveryAddress($address,$order,$data);
		if(is_array($deliveryInfo)) {
			return $deliveryInfo;
		}
		$articlesInfo = self::prepareUpdatedArticles($order,$data);

		$search = array(
			'[[articles]]',
			'[[RETURN-ADDRESS]]',
			'[[DELIVERY-ADDRESS]]',
			'[[CUSTOMER-EMAIL]]',
			'[[DELIVERY-SIGNATURE]]',
			'[[ORDER-ID]]',
			'[[CHARGE-CODE]]',
			'[[SHIPMENT-ID]]',
			'[[DANGER-GOODS]]',
			'[[printReturnLabels]]',
			'[[deliverPartConsignment]]',
			'[[cashToCollect]]',
  			'[[cashToCollectAmount]]',
			'[[emailNotification]]',
			'[[safeDrop]]'
		);

		$chargeCode = self::getChargeCode($order,$consignment_number);

		$replace = array(
			$articlesInfo['info'],
			$returnAddress,
			$deliveryInfo,
			$address['_billing_email'][0],
			(($data['delivery_signature_allowed']==1) ? 'true' : 'false'),
			self::getIncrementId($order),
			$chargeCode,
			self::getIncrementId($order),
			(($data['contains_dangerous_goods']==1) ? 'true' : 'false'),
			($data['print_return_labels'] ? 'true' : 'false'),
			(($data['partial_delivery_allowed']==1) ? 'Y' : 'N'),
			(isset($data['cash_to_collect']) ? '<cashToCollect>Y</cashToCollect>' : '<cashToCollect>N</cashToCollect>'),
			(isset($data['cash_to_collect']) ? '<cashToCollectAmount>'.number_format($data['cash_to_collect'],2).'</cashToCollectAmount>' : ''),
			($data['email_notification'] ? 'Y' : 'N'),
			($data['safe_drop']==1 ? 'yes' : 'no')
		);
		$template = file_get_contents(linksynceparcel_DIR.'assets/xml/articles-template.xml');
		$content = str_replace($search, $replace, $template);
		return array('content' => $content, 'charge_code' => $chargeCode, 'total_weight' => $articlesInfo['total_weight']);
	}

	public static function prepareUpdatedArticles($order,$data)
	{
		$articlesInfo = '';
		$total_weight = 0;
        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
		$articles = LinksynceparcelHelper::getArticles($order_id, $data['consignment_number']);
		foreach($articles as $article)
		{
			$search = array(
				'[[actualWeight]]',
				'[[articleDescription]]',
				'[[height]]',
				'[[length]]',
				'[[width]]',
				'[[isTransitCoverRequired]]',
				'[[transitCoverAmount]]',
				'[[articleNumber]]'
			);

			if($article->article_number == $data['article_number'])
			{
				$article = $data['article'];
				$article['weight'] = number_format($article['weight'],2,'.', '');
				$total_weight += $article['weight'];

				$article['weight'] = self::calculateWeightDefault($article['weight']);

				$replace = array(
					trim($article['weight']),
					self::xmlData(trim($article['description'])),
					trim($article['height']),
					trim($article['length']),
					'<width>'.trim($article['width']).'</width>',
					($data['transit_cover_required'] ? 'Y' : 'N'),
					($data['transit_cover_required'] ? trim($data['transit_cover_amount']) : 0),
				''
				);
			}
			else
			{
				$default_width = 0;
				$use_article_dimensions = (int)get_option('linksynceparcel_use_dimension');
				if($use_article_dimensions == 1)
				{
					$default_width = get_option('linksynceparcel__default_article_width');
				}

				$total_weight += $article->actual_weight;

				$article->actual_weight = self::calculateWeightDefault($article->actual_weight);

				$replace = array(
					trim($article->actual_weight),
					trim($article->article_description),
					trim($article->height),
					trim($article->length),
					'<width>'.trim($article->width).'</width>',
					$article->is_transit_cover_required,
					($article->transit_cover_amount ? trim($article->transit_cover_amount) : 0),
					'<articleNumber>'.trim($article->article_number).'</articleNumber>'
				);
			}

			$template = file_get_contents(linksynceparcel_DIR.'assets/xml/article-template.xml');
			$articlesInfo .= str_replace($search, $replace, $template);
		}
		return array('info' => $articlesInfo, 'total_weight' => $total_weight);
	}
	public static function getManifestNumber()
	{
		try
		{
			$manifestNumber = false;
			$manifests = LinksynceparcelApi::getManifest();
			$xml = simplexml_load_string($manifests);
			LinksynceparcelHelper::log('manifest xml: '.preg_replace('/\s+/', ' ', trim($manifests)));
			$currentManifest = '';
			if($xml)
			{
				foreach($xml->manifest as $manifest)
				{
					$manifestNumber = $manifest->manifestNumber;
					if(empty($currentManifest))
					{
						$currentManifest = $manifestNumber;
					}

					$numberOfArticles = (int)$manifest->numberOfArticles;
					$numberOfConsignments = (int)$manifest->numberOfConsignments;
					LinksynceparcelHelper::updateManifest($manifestNumber,$numberOfArticles,$numberOfConsignments);
				}

				update_option('linksynceparcel_manifest_sync',0);
			}
			return $currentManifest;
		}
		catch(Exception $e)
		{
			LinksynceparcelHelper::log('getManifestNumber: '.$e->getMessage());
			return false;
		}
	}

	public static function updateManifestTable($manifestNumber,$columnName, $value)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_manifest";
		$query = "UPDATE {$table_name} SET {$columnName} = '{$value}' WHERE manifest_number='{$manifestNumber}'";
		$wpdb->query($query);
	}

	public static function isCurrentMainfestHasConsignmentsForDespatch()
	{
		global $wpdb;
	    $table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$query = "SELECT order_id FROM {$table_name} WHERE despatched=0 and is_next_manifest=1";
		$result = $wpdb->get_results($query);
		if(count($result) > 0)
			return true;
		return false;
	}

	public static function addMessage($key,$message)
	{
		$existingMessage = get_option($key);
		if(!empty($existingMessage))
			$existingMessage .= '<br/>';
		update_option($key,$existingMessage.$message);
	}

	public static function getNotDespatchedConsignmentNumbers()
	{
		$notDespatchedConsignmentsArray = array();
		$notDespatchedConsignments = LinksynceparcelApi::getNotDespatchedConsignments();
		if(is_object($notDespatchedConsignments ))
		{
			$notDespatchedConsignmentsArray[] = $notDespatchedConsignments->consignmentNumber;
		}
		else
		{
			foreach($notDespatchedConsignments as $consignment)
			{
				$notDespatchedConsignmentsArray[] = $consignment->consignmentNumber;
			}
		}
		return $notDespatchedConsignmentsArray;
	}

	public static function getNotDespatchedAssignedConsignmentNumbers()
	{
		$notDespatchedConsignmentsArray = array();
		$notDespatchedConsignments = LinksynceparcelApi::getNotDespatchedConsignments();
		if(is_object($notDespatchedConsignments ))
		{
			if($notDespatchedConsignments->status == 'Assigned')
			{
				$notDespatchedConsignmentsArray[] = $notDespatchedConsignments->consignmentNumber;
			}
		}
		else
		{
			foreach($notDespatchedConsignments as $consignment)
			{
				if($consignment->status == 'Assigned')
				{
					$notDespatchedConsignmentsArray[] = $consignment->consignmentNumber;
				}
			}
		}
		return $notDespatchedConsignmentsArray;
	}

	public static function getNotDespatchedUnassignedConsignmentNumbers()
	{
		$notDespatchedConsignmentsArray = array();
		$notDespatchedConsignments = LinksynceparcelApi::getNotDespatchedConsignments();
		if(is_object($notDespatchedConsignments ))
		{
			if($notDespatchedConsignments->status == 'UnAssigned')
			{
				$notDespatchedConsignmentsArray[] = $notDespatchedConsignments->consignmentNumber;
			}
		}
		else
		{
			foreach($notDespatchedConsignments as $consignment)
			{
				if($consignment->status == 'UnAssigned')
				{
					$notDespatchedConsignmentsArray[] = $consignment->consignmentNumber;
				}
			}
		}
		return $notDespatchedConsignmentsArray;
	}

	public static function getNotDespatchedBothConsignmentNumbers()
	{
		$notDespatchedConsignmentsArray = array();
		$notDespatchedConsignments = LinksynceparcelApi::getNotDespatchedConsignments();
		foreach($notDespatchedConsignments as $consignment)
		{
			$notDespatchedConsignmentsArray[$consignment->status][] = $consignment->consignmentNumber;
		}
		return $notDespatchedConsignmentsArray;
	}

	public static function getConsignmentsByNumber($manifestNumber)
	{
		global $wpdb;
	    $table_name = $wpdb->prefix . "linksynceparcel_consignment";
		$query = "SELECT * FROM {$table_name} WHERE manifest_number = '{$manifestNumber}'";
		return $wpdb->get_results($query);
	}

    public static function formatToHtml($content)
    {
        $content = htmlspecialchars($content);
        $content = str_replace(htmlspecialchars('\"'), "", $content);
        return html_entity_decode($content);
    }

	public static function notifyCustomers($manifestNumber)
	{
		$consignments = self::getConsignmentsByNumber($manifestNumber[0]);
		if($consignments){
			foreach($consignments as $consignment)
			{
				$address = get_post_meta($consignment->order_id);
				$toEmail = $address['_billing_email'][0];
				$toName = $address['_billing_first_name'][0].' '.$address['_billing_last_name'][0];
				$fromEmail  = get_option('linksynceparcel_from_email_address');
				$fromName = get_bloginfo('name');
				$subject  = get_option('linksynceparcel_subject');
				$siteUrl = get_bloginfo('url');

                $content = static::formatToHtml(get_option('linksynceparcel_email_body'));
				$content = str_replace('[TrackingNumber]',$consignment->consignment_number,$content);

				$order = new WC_Order( $consignment->order_id );

				$search = array(
					'[TrackingNumber]',
					'[OrderNumber]',
					'[CustomerFirstname]'
				);

				$replace = array(
					$consignment->consignment_number,
					self::getIncrementId($order),
					$address['_billing_first_name'][0]
				);

				$subject = str_replace($search, $replace, $subject);
				$content = str_replace($search, $replace, $content);

				$headers = 'From: '.$fromName;
				if(!empty($fromEmail))
				{
					$headers .= ' <'.$fromEmail.'>';
				}

				add_filter( 'wp_mail_content_type', 'linksyneparcel_set_html_content_type' );
				wp_mail($toEmail, $subject, $content,$headers);
				remove_filter( 'wp_mail_content_type', 'linksyneparcel_set_html_content_type' );
			}
		}
	}

	public static function getOrderWeight($order)
	{
		$weight = 0;
		if ( sizeof( $order->get_items() ) > 0 )
		{
			foreach( $order->get_items() as $item )
			{
				if ( $item['product_id'] > 0 )
				{
					$_product = $order->get_product_from_item( $item );
					if (!empty($_product))
					{
						if ( ! $_product->is_virtual() )
						{
							$weight += (float)$_product->get_weight() * (int)$item['qty'];
						}
					}
				}
			}
		}
		$packaging_allowance_type = trim(get_option('linksynceparcel_allowance_type'));
		$packaging_allowance_value = trim(get_option('linksynceparcel_allowance_value'));
		$product_unit = get_option('woocommerce_weight_unit');

		if($weight > 0 && $product_unit != 'kg')
		{
			$weight = wc_get_weight( $weight, 'kg', $product_unit );
		}

		if($packaging_allowance_value > 0)
		{
			if($packaging_allowance_type == 'F')
			{
				$weight += $packaging_allowance_value;
			}
			else
			{
				$weight += ($weight * ($packaging_allowance_value/100));
			}
		}

		return number_format($weight,2,'.', '');//number_format($weight,2);
	}

	public static function getAllowedWeightPerArticle()
	{
		return 22;
	}

	public static function getOrderWeightTotal($orderid)
	{
		global $wpdb;
		$table = $wpdb->prefix ."linksynceparcel_article";
		$sql = "SELECT actual_weight FROM ". $table ." WHERE order_id=". $orderid;
		$results = $wpdb->get_results($sql);
		$total = 0;
		if($results) {
			foreach($results as $result) {
				$total += $result->actual_weight;
			}
		}
		return $total;
	}

	public static function presetMatch($presets,$weight)
	{
		$selected = false;
		if($presets && count($presets) > 0)
		{
			foreach($presets as $preset)
			{
				$presetWeight = floatval($preset->weight);
				$weight = floatval($weight);

				$presetWeight = ''.$presetWeight.'';
				$weight = ''.$weight.'';

				if($presetWeight === $weight)
				{
					$selected = true;
					break;
				}
			}
		}
		return $selected;
	}

	public static function getOrderedShippingDescriptions()
	{
		global $wpdb;
	    $table_name = $wpdb->prefix . "woocommerce_order_items";
		$query = "SELECT distinct(order_item_name) as code FROM {$table_name} WHERE order_item_type = 'shipping'";
		return $wpdb->get_results($query);
	}

	public static function listOfShippingMethods()
	{
		global $wpdb;
	    $table_name = $wpdb->prefix . "woocommerce_order_items";
		$query = "SELECT order_item_id, order_item_name FROM {$table_name} WHERE order_item_type = 'shipping'";
		$usedMethods = $wpdb->get_results($query);

		$woo_version = self::wpbo_get_woo_version_number();
		$req_version = '3.5.0';

		$arr = array();
		foreach($usedMethods as $key=>$val) {
			$instance_id = self::get_order_shipping_instance($val->order_item_id);
			$methodId = self::getMethodId($val->order_item_id);
			$method = explode(':', $methodId);

			$shippingkey = $methodId;
			if($woo_version >= $req_version) {
				$shippingkey = $instance_id;
			}

			$arr[$shippingkey] = array(
				'instance_id' => $instance_id,
				'method_id' => $methodId,
				'title' => $val->order_item_name
			);
		}

		return $arr;
	}

	public static function getMethodId($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "woocommerce_order_itemmeta";
		$query = "SELECT meta_value FROM {$table_name} WHERE order_item_id = {$id} AND meta_key='method_id'";
		$result = $wpdb->get_row($query);
		return $result->meta_value;
	}

	public static function get_order_shipping_instance($itemid)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "woocommerce_order_itemmeta";
		$query = "SELECT meta_value FROM {$table_name} WHERE order_item_id = {$itemid} AND meta_key='instance_id'";
		$result = $wpdb->get_row($query);
		return $result->meta_value;
	}

	public static function get_shipping_method_name($instanceid) 
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "woocommerce_shipping_zone_methods";
		$query = "SELECT method_id FROM {$table_name} WHERE instance_id={$instanceid}";
		$result = $wpdb->get_row($query);
		$method_id = $result->method_id;

		$option = 'woocommerce_'. $method_id .'_'. $instanceid .'_settings';
		$optionval = get_option($option, true);

		return $optionval;
	}

	/*
     *
     * International Functions
     *
	 */
	public static function prepareInternationalReturnAddress()
	{
		$returnAddressLine2 = trim(get_option('linksynceparcel_return_address_line2'));
		if(!empty($returnAddressLine2))
		{
			$returnAddressLine2 = '<returnAddressLine2>'.self::xmlData($returnAddressLine2).'</returnAddressLine2>';
		}
		else
		{
			$returnAddressLine2 = '<returnAddressLine2/>';
		}

		$returnAddressLine3 = trim(get_option('linksynceparcel_return_address_line3'));
		if(!empty($returnAddressLine3))
		{
			$returnAddressLine3 = '<returnAddressLine3>'.self::xmlData($returnAddressLine3).'</returnAddressLine3>';
		}
		else
		{
			$returnAddressLine3 = '<returnAddressLine3 />';
		}

		$returnAddressLine4 = trim(get_option('linksynceparcel_return_address_line4'));
		if(!empty($returnAddressLine4))
		{
			$returnAddressLine4 = '<returnAddressLine4>'.self::xmlData($returnAddressLine4).'</returnAddressLine4>';
		}
		else
		{
			$returnAddressLine4 = '<returnAddressLine4 />';
		}

		$search = array(
			'[[returnAddressLine1]]',
			'[[returnAddressLine2]]',
			'[[returnAddressLine3]]',
			'[[returnAddressLine4]]',
			'[[returnName]]',
			'[[returnPostcode]]',
			'[[returnStateCode]]',
			'[[returnSuburb]]',
			'[[returnCompanyName]]',
			'[[returnEmailAddress]]',
			'[[returnPhoneNumber]]',
		);

		$replace = array(
			self::xmlData(trim(get_option('linksynceparcel_return_address_line1'))),
			trim($returnAddressLine2),
			trim($returnAddressLine3),
			trim($returnAddressLine4),
			self::xmlData(trim(get_option('linksynceparcel_return_address_name'))),
			trim(get_option('linksynceparcel_return_address_postcode')),
			trim(get_option('linksynceparcel_return_address_statecode')),
			trim(get_option('linksynceparcel_return_address_suburb')),
			trim(get_option('linksynceparcel_return_business_name')),
			trim(get_option('linksynceparcel_return_email_address')),
			trim(get_option('linksynceparcel_return_phone_number')),
		);

		$template = file_get_contents(linksynceparcel_DIR.'assets/xml/international-article-return-address-template.xml');
		return str_replace($search, $replace, $template);
	}

	public static function prepareInternationalDeliveryAddress($address,$order,$data=false)
	{
		$street1 = '<deliveryAddressLine1>'.self::xmlData($address['_shipping_address_1'][0]).'</deliveryAddressLine1>';
		$street2 = '<deliveryAddressLine2/>';
		$street3 = '<deliveryAddressLine3/>';
		$street4 = '<deliveryAddressLine4/>';

		if(isset($address['_shipping_address_2'][0]) && !empty($address['_shipping_address_2'][0]))
		{
			$street2 = '<deliveryAddressLine2>'.self::xmlData($address['_shipping_address_2'][0]).'</deliveryAddressLine2>';
		}

		$city = $address['_shipping_city'][0];
		$state = 'NA';
		if($address['_shipping_state'][0])
		{
			$state = $address['_shipping_state'][0];
		}
		$postalCode = $address['_shipping_postcode'][0];
		$company = empty($address['_shipping_company'][0]) ? '' : self::xmlData($address['_shipping_company'][0]);
		$country = empty($address['_shipping_country'][0]) ? 'AU' : self::xmlData($address['_shipping_country'][0]);
		$firstname = $address['_shipping_first_name'][0].' '.$address['_shipping_last_name'][0];
		$email = $address['_billing_email'][0];
		$phone = $address['_billing_phone'][0];
		$phonestr = $phone;
		$phone = self::getValidPhoneNumber($phone);
		if(!empty($phone)) {
			$strposphone = strpos($phone, '+61');
			if($strposphone !== false) {
				$phone = str_replace('+61', '', $phone);
			}
			$strposphone2 = strpos($phone, '+');
			if($strposphone2 !== false) {
				$phone = str_replace('+', '', $phone);
			}
			$phone = preg_replace('/[^0-9]/s', '', $phone);
			$phonestr = $phone;
		}
		if(!empty($phonestr)) {
			$phonemessage = "";
			if(strlen($phonestr) == 9 && substr($phonestr, 0, 1) == 0) {
				$phonemessage = "Phone number must be atleast 10 digits.";
			} elseif(strlen($phonestr) < 9) {
				$phonemessage = "Phone number must be atleast 9 digits.";
			}

			if(!empty($phonemessage)) {
				return array('error_msg' => $phonemessage );
			}
		}

		$instructions = $data['delivery_instruction'];

		$importerCustomsReference = '';
		$senderCustomsReference = '';

		$search = array(
			'[[deliveryAddressLine1]]',
			'[[deliveryAddressLine2]]',
			'[[deliveryAddressLine3]]',
			'[[deliveryAddressLine4]]',
			'[[deliveryPhoneNumber]]',
			'[[deliveryCompanyName]]',
			'[[deliveryCountryCode]]',
			'[[deliveryEmailAddress]]',
			'[[deliveryInstructions]]',
			'[[deliveryName]]',
			'[[deliveryPostcode]]',
			'[[deliveryStateCode]]',
			'[[deliverySuburb]]',
			'[[importerCustomsReference]]',
			'[[senderCustomsReference]]',
		);

		$replace = array(
			trim($street1),
			trim($street2),
			trim($street3),
			trim($street4),
			trim($phonestr),
			trim($company),
			trim($country),
			trim($email),
			(!empty($instructions) ? '<deliveryInstructions>'. self::xmlData(($instructions)) .'</deliveryInstructions>' : '<deliveryInstructions/>'),
			self::xmlData(trim($firstname)),
			trim($postalCode),
			trim($state),
			self::xmlData(trim($city)),
			(!empty($importerCustomsReference) ? '<importerCustomsReference>'. $importerCustomsReference .'<importerCustomsReference>' : '<importerCustomsReference/>' ),
			(!empty($senderCustomsReference) ? '<senderCustomsReference>'. $senderCustomsReference .'<senderCustomsReference>' : '<senderCustomsReference/>' )
		);

		$template = file_get_contents(linksynceparcel_DIR.'assets/xml/international-article-delivery-address-template.xml');
		return str_replace($search, $replace, $template);
	}

	public static function articleContents($order,$data, $total_weight) {
        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
		$order_item = self::getOrderProdItems($order_id, $data, false, $total_weight);
		return $order_item;
	}

	public static function getOrderProdItems($order_id, $data, $totalonly=false, $total_weight = false) {
		global $wpdb;
		$table = $wpdb->prefix .'woocommerce_order_items';
		$sql = 'SELECT order_item_id FROM '. $table .' WHERE order_item_type="line_item" AND order_id='. $order_id;
		$rows = $wpdb->get_results($sql);

		$countryOrigin = isset($data['country_origin'])?$data['country_origin']:get_option('linksynceparcel_country_origin');
		$hsTariff = isset($data['hs_tariff'])?$data['hs_tariff']:get_option('linksynceparcel_hs_tariff');
		$shipping_cost = self::getShippingMethodDetails($order_id);

		$singleWeight = 0;
		if($total_weight > 0) {
			$singleWeight = self::getSingleWeight($rows, $total_weight);
		}

		$pass = false;
		$declared_option_value = 0;

		if($data['order_value_declared_value'] != 0) {
			$checktotal = 0;
			$totalqty = 0;
			$cntr = 0;
			foreach($rows as $row) {
				$prodid_1 = wc_get_order_item_meta( $row->order_item_id, '_product_id', true );
				$varid_1 = wc_get_order_item_meta( $row->order_item_id, '_variation_id', true );
				if($varid_1 > 0) {
					$prodid_1 = $varid_1;
				}
				$item_qty_1 = wc_get_order_item_meta( $row->order_item_id, '_qty', true );
				$sale_price_1 = get_post_meta( $prodid_1, '_sale_price', true );
				$unitvalue_1 = $sale_price_1;
				if(empty($sale_price_1))
					$unitvalue_1 = get_post_meta( $prodid_1, '_regular_price', true );

				$value_1 = $unitvalue_1 * $item_qty_1;

				if($cntr > 0) {
					$totalqty += $item_qty_1;
				}

				$checktotal += $value_1;
				$cntr++;
			}


			if($data['order_value_declared_value'] == 1) {
				if($checktotal >= $data['maximum_declared_value']) {
					$pass = true;
					$declared_option_value = $data['maximum_declared_value'] - $totalqty;
				}
			}

			if($data['order_value_declared_value'] == 2) {
				$pass = true;
				$declared_option_value = $data['fixed_declared_value'] - $totalqty;
			}
		}

		$alter = false;
		$cnt = 0;
		$contents = '';
		$totalCost = 0;
		$total_weight = 0;
		foreach($rows as $row) {
			$prodid = wc_get_order_item_meta( $row->order_item_id, '_product_id', true );
			$parent_id = $prodid;
			$varid = wc_get_order_item_meta( $row->order_item_id, '_variation_id', true );
			if($varid > 0) {
				$prodid = $varid;
				$parent_id = wp_get_post_parent_id($varid);
			}
			$item_description = get_the_title( $parent_id );
			$user_order_details = get_option('linksynceparcel_user_order_details');
			if(isset($user_order_details) && $user_order_details == 0) {
				$item_description = get_option('linksynceparcel_default_good_description');
			}
			$item_description = substr($item_description, 0, 40);
			$item_description = htmlentities($item_description);

			$item_qty = wc_get_order_item_meta( $row->order_item_id, '_qty', true );
			$sale_price = get_post_meta( $prodid, '_sale_price', true );
			$unitvalue = $sale_price;
			if(empty($sale_price))
				$unitvalue = get_post_meta( $prodid, '_regular_price', true );

			$value = wc_get_order_item_meta( $row->order_item_id, '_line_total', true );
			if(empty($unitvalue)) {
				$unitvalue = 1;
				$value = $item_qty * $unitvalue;
			}
			if($singleWeight > 0.01) {
				$weight = $singleWeight * $item_qty;
			} else {
				$weight = $singleWeight;
			}
			if($weight == 0){
				$weight = get_post_meta( $prodid, '_weight', true );
				if(empty($weight)) {
					$default_article_weight = get_option('linksynceparcel_default_article_weight');
					if($default_article_weight)
					{
						$weight = $default_article_weight;
					} else {
						$weight = 0.01;
					}
				}
			}

			$totalCost += $value;

			if($pass) {
				if($cnt == 0) {
					$alter = true;
					$maxval = $declared_option_value;
					$unitval = $maxval / $item_qty;
					$unitvalue = $unitval;
					$value = $maxval;
				}

				if($alter && $cnt > 0) {
					$unitvalue = 1;
					$value = $item_qty;
				}
			}

			$contents .= '<content>';
			$contents .= '<goodsDescription>'. $item_description .'</goodsDescription>';
			$contents .= '<quantity>'. $item_qty .'</quantity>';
			$contents .= '<unitValue>'. number_format($unitvalue, 2, '.', '') .'</unitValue>';
			$contents .= '<value>'. number_format($value, 2, '.', '') .'</value>';
			$contents .= '<weight>'. $weight .'</weight>';
			$contents .= '<countryOriginCode>'. $countryOrigin .'</countryOriginCode>';
			$contents .= '<hSTariff>'. $hsTariff .'</hSTariff>';
			$contents .= '</content>';

			$total_weight += $weight;

			$cnt++;
		}
		return ($totalonly)?$totalCost:array('totalcost' => $totalCost, 'total_weight' => $total_weight, 'contents' => $contents);
	}

	public static function getShippingMethodDetails($order_id) {
		global $wpdb;
		$table = $wpdb->prefix .'woocommerce_order_items';
		$sql = 'SELECT order_item_id FROM '. $table .' WHERE order_item_type="shipping" AND order_id='. $order_id;
		$row = $wpdb->get_row($sql);

		$shipping_cost = wc_get_order_item_meta( $row->order_item_id, 'cost', true );
		return $shipping_cost;
	}

	public static function getSingleWeight($rows, $total_weight) {
		$cntr = 0;
		foreach($rows as $row) {
			$cntr += wc_get_order_item_meta( $row->order_item_id, '_qty', true );
		}

		$weight = $total_weight/$cntr;
		$weight = number_format($weight, 2);
		if($weight < 0.01 ) {
			return 0.01;
		}
		return $weight;
	}

	public static function prepareInternationalArticles($data,$order,$bulk=false) {
		$articlesInfo = self::prepareArticles($data,$order,'',true);
		if($bulk){
			$articlesInfo = self::prepareArticlesBulk($data, $order, true);
		}
		$isInsurance = $data['insurance'];
		$insuranceOrderValue = $data['order_value_insurance'];
		$insuranceValue = $data['insurance_value'];
		$classificationExplanation = $data['product_classification_text'];
		$exportDeclarationNumber = (!empty($data['export_declaration_number']) ? '<exportDeclarationNumber>'. $data['export_declaration_number'] .'</exportDeclarationNumber>' : '<exportDeclarationNumber/>');
		$productClassification = !empty($data['product_classification'])?$data['product_classification']:991;
		$hasCommercialValue = ($data['has_commercial_value']==1)?"true":"false";
		$deliveryFailureDetails = self::deliveryFailureDetails();
		$articleContents = self::articleContents($order, $data, $articlesInfo['total_weight']);

		$articlesInfo['info'] = self::replace_between($articlesInfo['info'], "<actualWeight>", "</actualWeight>", $articleContents['total_weight']);

		if(empty($insuranceValue)) {
			$insuranceValue = '<insuranceValue/>';
		} else {
			$insuranceValue = '<insuranceValue>'. trim($insuranceValue) .'</insuranceValue>';
		}
		if(isset($insuranceOrderValue))
			$insuranceValue = '<insuranceValue>'. trim($articleContents['totalcost']) .'</insuranceValue>';

		$insuranceValue = ($isInsurance==0)? '<insuranceValue/>' : $insuranceValue;

		$search = array(
			'[[preparedarticle]]',
			'[[isInsuranceRequired]]',
			'[[insuranceValue]]',
			'[[classificationExplanation]]',
			'[[exportDeclarationNumber]]',
			'[[productClassification]]',
			'[[hasCommercialValue]]',
			'[[deliveryFailureDetails]]',
			'[[contents]]'
		);

		$replace = array(
			$articlesInfo['info'],
			($isInsurance==0)? 'false' : 'true',
			$insuranceValue,
			$classificationExplanation,
			$exportDeclarationNumber,
			$productClassification,
			$hasCommercialValue,
			$deliveryFailureDetails,
			$articleContents['contents']
		);

		$template = file_get_contents(linksynceparcel_DIR.'assets/xml/international-articleall-template.xml');
		return array('content' => str_replace($search, $replace, $template), 'totalcost' => $articleContents['totalcost']);
	}

	public static function prepareInternationalOrderWeightArticles($data,$order) {
		$articlesInfo = self::prepareOrderWeightArticles($data,$order,'',true);
		$isInsurance = $data['insurance'];
		$insuranceOrderValue = $data['order_value_insurance'];
		$insuranceValue = $data['insurance_value'];
		$classificationExplanation = $data['product_classification_text'];
		$exportDeclarationNumber = (!empty($data['export_declaration_number']) ? '<exportDeclarationNumber>'. $data['export_declaration_number'] .'</exportDeclarationNumber>' : '<exportDeclarationNumber/>');
		$productClassification = !empty($data['product_classification'])?$data['product_classification']:991;
		$hasCommercialValue = isset($data['has_commercial_value'])?"true":"false";
		$deliveryFailureDetails = self::deliveryFailureDetails();
		$articleContents = self::articleContents($order, $data, $articlesInfo['total_weight']);

		$articlesInfo['info'] = self::replace_between($articlesInfo['info'], "<actualWeight>", "</actualWeight>", $articleContents['total_weight']);

		if(empty($insuranceValue)) {
			$insuranceValue = '<insuranceValue/>';
		} else {
			$insuranceValue = '<insuranceValue>'. trim($insuranceValue) .'</insuranceValue>';
		}
		if(isset($insuranceOrderValue))
			$insuranceValue = '<insuranceValue>'. trim($articleContents['totalcost']) .'</insuranceValue>';

		$insuranceValue = ($isInsurance==0)? '<insuranceValue/>' : $insuranceValue;

		$search = array(
			'[[preparedarticle]]',
			'[[isInsuranceRequired]]',
			'[[insuranceValue]]',
			'[[classificationExplanation]]',
			'[[exportDeclarationNumber]]',
			'[[productClassification]]',
			'[[hasCommercialValue]]',
			'[[deliveryFailureDetails]]',
			'[[contents]]'
		);

		$replace = array(
			$articlesInfo['info'],
			($isInsurance==0)? 'false' : 'true',
			$insuranceValue,
			$classificationExplanation,
			$exportDeclarationNumber,
			$productClassification,
			$hasCommercialValue,
			$deliveryFailureDetails,
			$articleContents['contents']
		);

		$template = file_get_contents(linksynceparcel_DIR.'assets/xml/international-articleall-template.xml');
		return array('content' => str_replace($search, $replace, $template), 'totalcost' => $articleContents['totalcost']);
	}

	public static function deliveryFailureDetails() {
		$addressName = self::xmlData(trim(get_option('linksynceparcel_return_address_name')));
		$companyName = self::xmlData(trim(get_option('linksynceparcel_return_business_name')));
		$addressOne = self::xmlData(trim(get_option('linksynceparcel_return_address_line1')));
		$addressTwo = self::xmlData(trim(get_option('linksynceparcel_return_address_line2')));
		$suburb = self::xmlData(trim(get_option('linksynceparcel_return_address_suburb')));
		$stateCode = trim(get_option('linksynceparcel_return_address_statecode'));
		$postCode = trim(get_option('linksynceparcel_return_address_postcode'));

		$deliveryFailureDetails = (!empty($addressName) ? '<deliveryFailureName>'. $addressName .'</deliveryFailureName>' : '<deliveryFailureName/>');
		$deliveryFailureDetails .= (!empty($companyName) ? '<deliveryFailureCompanyName>'. $companyName .'</deliveryFailureCompanyName>' : '<deliveryFailureCompanyName/>');
		$deliveryFailureDetails .= '<deliveryFailureAddressLine1>'. $addressOne .'</deliveryFailureAddressLine1>';
		$deliveryFailureDetails .= '<deliveryFailureAddressLine2>'. $addressTwo .'</deliveryFailureAddressLine2>';
		$deliveryFailureDetails .= '<deliveryFailureSuburb>'. $suburb .'</deliveryFailureSuburb>';
		$deliveryFailureDetails .= '<deliveryFailureStateCode>'. $stateCode .'</deliveryFailureStateCode>';
		$deliveryFailureDetails .= '<deliveryFailurePostcode>'. $postCode .'</deliveryFailurePostcode>';
		$deliveryFailureDetails .= '<deliveryFailureCountryCode>AU</deliveryFailureCountryCode>';
		return $deliveryFailureDetails;
	}

	public static function isInternationalDelivery($shipCountry) {
		if($shipCountry != 'AU') {
			if(self::checkAssignShippingType()) {
				return true;
			}
		}
		return false;
	}

	public static function checkAssignShippingType() {
		global $wpdb;
		$table = $wpdb->prefix ."linksynceparcel_nonlinksync";
		$sql = "SELECT * FROM ". $table ." WHERE method='international_delivery'";
		$row = $wpdb->get_row($sql);
		return !empty($row)?true:false;
	}

	public static function checkAssignApiCode() {
		if(get_option('linksynceparcel_laid')) {
			self::addMessage('linksynceparcel_consignment_error','You have not configured linksync eParcel');
			self::addMessage('linksynceparcel_manifest_view_error','You have not configured linksync eParcel');
		}
	}

	public static function getInternationFields($orderid, $consignmentnumber) {
		global $wpdb;
		$table = $wpdb->prefix ."linksynceparcel_international_fields";
		$sql = 'SELECT * FROM '. $table .' WHERE order_id='. $orderid .' AND consignment_number="'. $consignmentnumber .'"';
		$row = $wpdb->get_row($sql);
		return $row;
	}

	public static function generateDocument($consignmentNumber,$labelContent,$field) {
		try
		{
			if($labelContent)
			{
				$name = $consignmentNumber;
				if($field == 'customdocs') {
					$name = 'int_'.$name;
				}
				$filename = $name.'.pdf';
				$filepath = linksynceparcel_UPLOAD_DIR.'consignment/'.$filename;
				$handle = fopen($filepath,'wb');
				fwrite($handle, $labelContent);
				fclose($handle);
				self::updateConsignmentTable($consignmentNumber,$field,$filename);
				self::updateConsignmentTable($consignmentNumber,'is_label_created',1);
			}
		}
		catch(Exception $e)
		{
			$error = 'CustomDocs is empty. Consignment is invalid.';
			update_option('linksynceparcel_order_view_error',$error);
		}
	}

	public static function getCountryDeliveryConsignment($consignmentnumber) {
		global $wpdb;
		$table = $wpdb->prefix ."linksynceparcel_consignment";
		$query = 'SELECT order_id FROM '. $table .' WHERE consignment_number="'. $consignmentnumber .'"';
		$row = $wpdb->get_row($query);
		$shipping_country = get_post_meta($row->order_id,'_shipping_country',true);
		return $shipping_country;
	}

	public static function createUploadDirectory() {
		$filepath = linksynceparcel_UPLOAD_URL.'/blazeonline_uploads/label/';

		if (!file_exists($filepath.'/consignment')) {
			mkdir($filepath.'/consignment', 0740, true);
		}
		if (!file_exists($filepath.'/manifest')) {
			mkdir($filepath.'/manifest', 0740, true);
		}
		if (!file_exists($filepath.'/returnlabels')) {
			mkdir($filepath.'/returnlabels', 0740, true);
		}

		$logpath = linksynceparcel_UPLOAD_URL .'/blazeonline_uploads/';
		if (!file_exists($logpath.'log')) {
			mkdir($logpath.'log', 0740, true);
		}

		$logpath = linksynceparcel_UPLOAD_URL .'/blazeonline_uploads/';
		if (!file_exists($logpath.'session-logs')) {
			mkdir($logpath.'session-logs', 0740, true);
		}

		$filename = linksynceparcel_UPLOAD_URL .'/blazeonline_uploads/index.html';
		if (!file_exists($filename)) {
			// Create index.html file
			$filecontent = '<html><body><a href="https://www.linksync.com/integrate/woocommerce-eparcel-integration/">Integrate WooCommerce with Australia Post eParcel with linksync</a></body></html>';
			file_put_contents($filename, $filecontent);
		}
	}

	public static function checkAssignConfigurationSettings() {
		$laid = get_option('linksynceparcel_laid');
		$st_apikey = get_option('linksynceparcel_st_apikey');
		$st_password = get_option('linksynceparcel_st_password');

		if(empty($laid) || empty($st_apikey) || empty($st_password)) {
			return true;
		} else {
			return false;
		}
	}

	public static function getValidPhoneNumber($str) {
		$strpos = strpos($str, ';');
		if($strpos !== false) {
			$strex = explode(';', $str);
			$strpos1 = strpos($strex[1], '&');
			if($strpos1 !== false) {
				$strex1 = explode('&', $strex[1]);
				return $strex1[0];
			}
			return $strex[1];
		}
		return $str;
	}

	public static function saveScreenOptions()
	{
		if(isset($_POST['_wpnonce-eParcel-default-settings'])) {
			eparcel_save_new_defaults($_POST);
		}
	}

	public static function getAllOrderId()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "posts";

		$status_condition = 'post_status="wc-pending" OR post_status="wc-processing" OR post_status="wc-on-hold" OR ';

		$display_choosen_status = (int)get_option('linksynceparcel_display_choosen_status');
		if($display_choosen_status == 1)
		{
			$chosen_statuses = get_option('linksynceparcel_chosen_statuses');
			if($chosen_statuses && count($chosen_statuses) > 0)
			{
				$status_condition = '';
				foreach($chosen_statuses as $chosen_status)
				{
					$status_condition .= 'post_status="'.$chosen_status.'" OR ';
				}
			}
		}

		$status_condition = substr($status_condition, 0, -4);

		$sql = 'SELECT ID FROM '. $table_name .' WHERE post_type = "shop_order" AND ('. $status_condition .')';
		$results = $wpdb->get_results($sql);
		if(count($results) > 0) {
			$string = '';
			foreach($results as $result) {
				if(self::getOrderChargeCode($result->ID)) {
					$string .= $result->ID .',';
				}
			}
			return substr($string, 0, -1);
		}
		return false;
	}

	public static function saveOrderStatuses()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_order_statuses";

		$statuses = LinksynceparcelHelper::getOrderStatuses();

		$values = '';
		$cnt = 0;
		foreach($statuses as $slug => $status) {
			$check = self::checkOrderStatus($slug);
			if(!$check) {
				$values .= '("'. $slug .'", "'. $status .'"),';
				$cnt++;
			}
		}

		if($cnt > 0) {
			$data_values = substr($values, 0, -1);
			$wpdb->query('INSERT INTO '. $table_name .' (`status`, `status_name`) VALUES '. $data_values);
		}
	}

	public static function checkOrderStatus($status)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_order_statuses";

		$result = $wpdb->get_row( "SELECT `status` FROM ". $table_name ." WHERE status='". $status ."'" );
		if(!empty($result)) {
			return true;
		}
		return false;
	}

	public static function getListOrderStatuses()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_order_statuses";

		$results = $wpdb->get_results( 'SELECT `status`, `status_name` FROM '. $table_name );

		$data = array();
		foreach($results as $result) {
			$data[$result->status] = $result->status_name;
		}

		return $data;
	}

    public static function getAllNonChangedStatusOrders($manifest_number)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "linksynceparcel_consignment";

        $results = $wpdb->get_results( "SELECT order_id FROM ". $table_name ." WHERE manifest_number='". $manifest_number ."'" );

        $data = array();
        foreach($results as $result) {
            $data[] = $result->order_id;
        }

        return $data;
    }

    public static function getPendingManifest()
    {
    	global $wpdb;
        $table_name = $wpdb->prefix . "linksynceparcel_manifest";
        $result = $wpdb->get_row( 'SELECT `manifest_number` FROM '. $table_name .' WHERE despatch_status=1 ORDER BY manifest_id DESC LIMIT 1' );
        return (!empty($result->manifest_number))?$result->manifest_number:0;
    }

	public static function requiredWooVersion()
	{
		$pass = false;
		$woo_version = self::wpbo_get_woo_version_number();
		$req_version = '2.6.1';
		if($woo_version >= $req_version) {
			$pass = true;
		}
		return $pass;
	}

	public static function checkLinksynceParcelVersion()
	{
		$plugin_path = linksynceparcel_DIR . 'blazeonlineeparcel.php';
		$plugin_data = get_plugin_data($plugin_path);
		$plugin_version = $plugin_data['Version'];
		return $plugin_version;
	}

	public static function displayGroupedChargeCode($formValue, $service_key, $chargecodes = NULL)
	{
		if($chargecodes == NULL)
		{
			$chargecodes = self::getEParcelChargeCodes();
		} ?>
		<option value="">Please select a charge code</option>
		<?php foreach ($chargecodes as $ppcc_key => $ppcc_val) {
			if($ppcc_val['key'] == $service_key) {
			?>
			   <option value="<?php echo $ppcc_key; ?>" <?php if($formValue==$ppcc_key){ echo "selected='selected'"; }?>> <?php echo $ppcc_key; ?> </option>
			<?php
			}
		}
	}

	public static function getAllChargeCodesOptions()
	{
		$services = self::eParcelServices();
		$data = array();
		foreach($services as $key => $service) {
			$chargecode = self::getSingleChargeCode($key);

			if(!empty($chargecode))
			{
				$data[$chargecode] = array(
					'key' => $key,
					'name' => $service
				);
			}
		}

		return $data;
	}

	public static function getSingleChargeCode($key)
	{
		return get_option('linksynceparcel_'. $key .'_charge_code');
	}

	public static function eParcelServices()
	{
		$services = array(
			'parcel_post' => 'Parcel Post',
			'express_post' => 'Express Post eParcel',
			'int_economy' => 'Int. Economy Air',
			'int_express_courier' => 'Int. Express Courier Document',
			'int_express_courier_mer' => 'Int. Express Courier Merchandise',
			'int_express_post' => 'Int. Express Post',
			'int_pack_track' => 'Int. Pack & Track',
			'int_registered' => 'Int. Registered',
		);
		return $services;
	}

	public static function checkConsignmentProcessExist($dateprocess)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "linksynceparcel_consignment";

		$result = $wpdb->get_row( 'SELECT `date_process` FROM '. $table_name .' WHERE date_process="'. $dateprocess .'"' );
		if(!empty($result)) {
			return $result;
		}
		return false;
	}

	public static function calculateWeightDefault($weight)
	{
		if($weight==floatval(0.00))
		{
			return 0.01;
		}

		return $weight;
	}

	public static function session_logs($session, $content)
	{
		$filename = $session.'.txt';
		$filepath = linksynceparcel_UPLOAD_URL .'/blazeonline_uploads/session-logs/';
		if (!file_exists($filepath)) {
			mkdir($filepath, 0777, true);
		}
		$path = $filepath.$filename;
		file_put_contents($path, $content);
	}

	public static function session_maifest($session, $content)
	{
		$filename = 'manifest_'. $session .'.txt';
		$filepath = linksynceparcel_UPLOAD_URL .'/blazeonline_uploads/session-logs/';
		if (!file_exists($filepath)) {
			mkdir($filepath, 0777, true);
		}
		$path = $filepath.$filename;
		file_put_contents($path, $content);
	}

	public static function get_session_manifest($session)
	{
		$file = str_replace(".", "", $session);
		$file = 'manifest_'. $session .'.txt';
		$file = linksynceparcel_UPLOAD_URL .'/blazeonline_uploads/session-logs/'. $file;
		if (file_exists($file)) {
			$text = file_get_contents($file);
			$obj = json_decode($text, true);
			return $obj;
		}
	}

	public static function remove_manifest_session($session)
	{
		$file = str_replace(".", "", $session);
		$file = 'manifest_'. $session .'.txt';
		$file = linksynceparcel_UPLOAD_URL .'/blazeonline_uploads/session-logs/'. $file;
		if(file_exists($file)) {
			unlink($file);
		}

		$filename = $session .'.txt';
		$filename = linksynceparcel_UPLOAD_URL .'/blazeonline_uploads/session-logs/'. $filename;
		if(file_exists($filename)) {
			unlink($filename);
		}
	}

	public static function replace_between($str, $needle_start, $needle_end, $replacement) {
	    $pos = strpos($str, $needle_start);
	    $start = $pos === false ? 0 : $pos + strlen($needle_start);

	    $pos = strpos($str, $needle_end, $start);
	    $end = $pos === false ? strlen($str) : $pos;

	    return substr_replace($str, $replacement, $start, $end - $start);
	}

	public static function checkNewChargeCodeConfig()
	{
		if ( (get_option('linksynceparcel_parcel_post_charge_code') !== "") ||
			(get_option('linksynceparcel_express_post_charge_code') !== "") ||
			(get_option('linksynceparcel_int_economy_charge_code') !== "") ||
			(get_option('linksynceparcel_int_express_courier_charge_code') !== "") ||
			(get_option('linksynceparcel_int_express_post_charge_code') !== "") ||
			(get_option('linksynceparcel_int_pack_track_charge_code') !== "") ||
			(get_option('linksynceparcel_int_registered_charge_code') !== "")
			)
		{
			return true;
		}

		return false;
	}

	public static function mres($value)
	{
		$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
		$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

		return str_replace($search, $replace, $value);
	}

	public static function get_linksync_site_url()
	{
		$siteurl = get_site_url();
		$parseurl = parse_url($siteurl);
		$url = str_replace('www.', '', str_replace($parseurl['scheme'].'://', '', $siteurl));
		return $url;
	}
}

function linksyneparcel_set_html_content_type()
{
	return 'text/html';
}

$is_greater_than_21 = LinksynceparcelHelper::woocommerce_version_check(LinksynceparcelHelper::wpbo_get_woo_version_number());
?>
