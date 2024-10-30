<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelAdminSendLog
{
	public static function output()
	{
		try
		{
			if(!LinksynceparcelHelper::isZipArchiveInstalled())
			{
				throw new Exception('PHP ZipArchive extension is not enabled on your server, contact your web hoster to enable this extension.');
			}
			else
			{
				if(LinksynceparcelApi::sendLog())
				{
					$message =  'Log has been sent to LWS successfully.';
				}
				else
				{
					$message =  'Log failed to sent to LWS';
				}
				LinksynceparcelHelper::log('Send Log: '.$message);
				echo $message;
			}
		}
		catch(Exception $e)
		{
			$message = $e->getMessage();
			LinksynceparcelHelper::log('Send Log: '.$message);
			echo $message;
		}
		exit;
	}
}
?>