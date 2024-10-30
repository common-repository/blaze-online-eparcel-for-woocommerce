<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelAdminManifestsList
{
	public static function output()
	{
		include_once(linksynceparcel_DIR.'model/Manifest/List.php');
		include_once(linksynceparcel_DIR.'views/admin/manifests/list.php');
	}
	
	public static function generateLabels()
	{
		$ids = $_REQUEST['manifest'];
		try 
		{
			if(is_array($ids))
			{
				foreach ($ids as $manifestNumber) 
				{
					try 
					{
						$labelContent = LinksynceparcelApi::printManifest($manifestNumber);
						if($labelContent)
						{
							$filename = $manifestNumber.'.pdf';
							$filepath = linksynceparcel_UPLOAD_DIR.'manifest/'.$filename;
							$handle = fopen($filepath,'wb');
							fwrite($handle, $labelContent);
							fclose($handle);
			
							LinksynceparcelHelper::updateManifestTable($manifestNumber,'label',$filename);
							
							$successmsg = sprintf('%s: Manifest Summary has been generated.', $manifestNumber);
							LinksynceparcelHelper::addMessage('linksynceparcel_manifest_view_success',$successmsg);
						}
						else
						{
							$error = 'Manifest label content is empty';
							update_option('linksynceparcel_manifest_view_error',$error);
						}
		
					}
					catch (Exception $e) 
					{
						$error = 'Failed to get manifest label content #'. $manifestNumber.', Error: '.$e->getMessage();
						update_option('linksynceparcel_manifest_view_error',$error);
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
			update_option('linksynceparcel_manifest_view_error',$e->getMessage());
		}
		wp_redirect(admin_url('admin.php?page=linksynceparcel&subpage=manifests'));
	}
}
?>