<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelAdminAssignShippingTypes
{
	public static function output()
	{
		$data = array();
		$action = sanitize_text_field(isset($_GET['action']) ? $_GET['action'] : '');
		if($action == 'add')
		{
			if (isset($_POST['save']) && check_admin_referer( 'assign_shipping_types_action', 'assign_shipping_types_nonce_field' ) ) {
				$data['shipping_type'] = 'desc';
				$chargecode = sanitize_text_field($_POST['linksynceparcel']['charge_code']);
				$method = sanitize_text_field($_POST['linksynceparcel']['method']);
				$data['method'] = $method;
				$data['charge_code'] = $chargecode;
				$chargecodes = LinksynceparcelHelper::getEParcelChargeCodes();
				$service_type = !empty($chargecodes[$chargecode])?$chargecodes[$chargecode]['key']:'';
				$data['service_type'] = $service_type;
				
				unset($data['method2']);
									
				$errors = LinksynceparcelValidator::validateAssignShippingTypes($data);
				if($errors)
				{
					$error = implode('<br/>',$errors);
				}
				else
				{
					try
					{
						require_once(linksynceparcel_DIR.'model/AssignShippingType/Model.php' );
						$assignShippingType = new AssignShippingType();
						$type = $assignShippingType->get_by(array('method' => $data['method']));
						if($type)
						{
							throw new Exception("For this shipping method, charge code already assigned.");
						}
						else
						{
							$assignShippingType->insert($data);
						}
						unset($_POST);
						unset($_REQUEST);
						$result = __( 'Charge code has been assigned to shipping type successfully.', 'linksynceparcel' );
					}
					catch(Exception $e)
					{
						$error = $e->getMessage();
					}
				}
			}

			$apiChargecodes = get_option('linksynceparcel_chargecodes');
			$clientChargecodes = json_decode($apiChargecodes, true);
			$chargeCodes = !empty($clientChargecodes)?$clientChargecodes['ChargeCode']:array();
			$shipping_titles = LinksynceparcelHelper::listOfShippingMethods();
			include_once(linksynceparcel_DIR.'views/admin/assign_shipping_type/add.php');
		}
		else if($action == 'edit')
		{
			$id = sanitize_text_field($_REQUEST['id']);
			require_once(linksynceparcel_DIR.'model/AssignShippingType/Model.php' );
			$assignShippingType = new AssignShippingType();
			
			if (isset($_POST['save']) && check_admin_referer( 'assign_shipping_types_action', 'assign_shipping_types_nonce_field' ) ) {
				$data['shipping_type'] = 'desc';
				$chargecode = sanitize_text_field($_POST['linksynceparcel']['charge_code']);
				$method = sanitize_text_field($_POST['linksynceparcel']['method']);
				$data['method'] = $method;
				$data['charge_code'] = $chargecode;
				$chargecodes = LinksynceparcelHelper::getEParcelChargeCodes();
				$service_type = !empty($chargecodes[$chargecode])?$chargecodes[$chargecode]['key']:'';
				$data['service_type'] = $service_type;
				
				unset($data['method2']);
				$errors = LinksynceparcelValidator::validateAssignShippingTypes($data);
				if($errors)
				{
					$error = implode('<br/>',$errors);
				}
				else
				{
					try
					{
						$type = $assignShippingType->get_by(array('id' => $id));
						$type = $type[0];
						if($type->method != $data['method'])
						{
							$type = $assignShippingType->get_by(array('method' => $data['method']));
							if($type)
							{
								throw new Exception("For this shipping method, charge code already assigned.");
							}
							else
							{
								$assignShippingType->update($data, array('id' => $id));
							}
						}
						else
						{
							$assignShippingType->update($data, array('id' => $id));
						}
						$result = __( 'Updated successfully.', 'linksynceparcel' );
					}
					catch(Exception $e)
					{
						$error = $e->getMessage();
					}
				}
			}
			$type = $assignShippingType->get_by(array('id' => $id));
			$type = $type[0];
			$apiChargecodes = get_option('linksynceparcel_chargecodes');
			$clientChargecodes = json_decode($apiChargecodes, true);
			$chargeCodes = !empty($clientChargecodes)?$clientChargecodes['ChargeCode']:array();
			$shipping_titles = LinksynceparcelHelper::listOfShippingMethods();
			include_once(linksynceparcel_DIR.'views/admin/assign_shipping_type/edit.php');
		}
		else
		{
			if($action == 'delete')
			{
				$id = sanitize_text_field($_REQUEST['id']);
				require_once(linksynceparcel_DIR.'model/AssignShippingType/Model.php' );
				$assignShippingType = new AssignShippingType();
				
				try
				{
					$assignShippingType->delete(array('id' => $id));
					$result = __( 'An item has been deleted successfully.', 'linksynceparcel' );
				}
				catch(Exception $e)
				{
					$error = $e->getMessage();
				}
			}
			if( (isset($_REQUEST['action2']) || isset($_POST['action']) ) && ($_REQUEST['action2'] == 'delete' || $_POST['action'] == 'delete') )
			{
				$ids = $_REQUEST['assignshippingtype'];
				require_once(linksynceparcel_DIR.'model/AssignShippingType/Model.php' );
				$assignShippingType = new AssignShippingType();
				
				try
				{
					foreach($ids as $id)
					{
						$assignShippingType->delete(array('id' => $id));
					}
					$result = __( 'Item(s) have been deleted successfully.', 'linksynceparcel' );
				}
				catch(Exception $e)
				{
					$error = $e->getMessage();
				}
			}
			
			include_once(linksynceparcel_DIR.'model/AssignShippingType/List.php');
			include_once(linksynceparcel_DIR.'views/admin/assign_shipping_type/list.php');
		}
	}
}
?>