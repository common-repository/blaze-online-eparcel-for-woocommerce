<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
class LinksynceparcelAdminArticlePresets
{
	public static function output()
	{
		$action = sanitize_text_field(isset($_GET['action']) ? $_GET['action'] : '');
		if($action == 'add')
		{
			if (isset($_POST['save']) && check_admin_referer( 'article_preset_action', 'article_preset_nonce_field' ) ) {
				$data = array(
					'name' => sanitize_text_field($_POST['linksynceparcel']['name']),
					'weight' => floatval($_POST['linksynceparcel']['weight']),
					'height' => floatval($_POST['linksynceparcel']['height']),
					'width' => floatval($_POST['linksynceparcel']['width']),
					'length' => floatval($_POST['linksynceparcel']['length']),
					'status' => floatval($_POST['linksynceparcel']['status']),
				);
				$errors = LinksynceparcelValidator::validateArticlePresets($data);
				if($errors)
				{
					$error = implode('<br/>',$errors);
				}
				else
				{
					try
					{
						require_once(linksynceparcel_DIR.'model/ArticlePreset/Model.php' );
						$articlePreset = new ArticlePreset();
						$articlePreset->insert($data);
						unset($_POST);
						unset($_REQUEST);
						$result = __( 'Article Preset has been added successfully.', 'linksynceparcel' );
					}
					catch(Exception $e)
					{
						$error = $e->getMessage();
					}
				}
			}
			include_once(linksynceparcel_DIR.'views/admin/article_presets/add.php');
		}
		else if($action == 'edit')
		{
			$id = sanitize_text_field($_REQUEST['id']);
			require_once(linksynceparcel_DIR.'model/ArticlePreset/Model.php' );
			$articlePreset = new ArticlePreset();
			
			if (isset($_POST['save']) && check_admin_referer( 'article_preset_action', 'article_preset_nonce_field' ) ) {
				$data = array(
					'name' => sanitize_text_field($_POST['linksynceparcel']['name']),
					'weight' => floatval($_POST['linksynceparcel']['weight']),
					'height' => floatval($_POST['linksynceparcel']['height']),
					'width' => floatval($_POST['linksynceparcel']['width']),
					'length' => floatval($_POST['linksynceparcel']['length']),
					'status' => floatval($_POST['linksynceparcel']['status']),
				);
				$errors = LinksynceparcelValidator::validateArticlePresets($data);
				if($errors)
				{
					$error = implode('<br/>',$errors);
				}
				else
				{
					try
					{
						$articlePreset->update($data, array('id' => $id));
						$result = __( 'Article Preset has been updated successfully.', 'linksynceparcel' );
					}
					catch(Exception $e)
					{
						$error = $e->getMessage();
					}
				}
			}
			$preset = $articlePreset->get_by(array('id' => $id));
			$preset = $preset[0];
			include_once(linksynceparcel_DIR.'views/admin/article_presets/edit.php');
		}
		else
		{
			if($action == 'delete')
			{
				$id = sanitize_text_field($_REQUEST['id']);
				require_once(linksynceparcel_DIR.'model/ArticlePreset/Model.php' );
				$articlePreset = new ArticlePreset();
				
				try
				{
					$articlePreset->delete(array('id' => $id));
					$result = __( 'Article Preset has been deleted successfully.', 'linksynceparcel' );
				}
				catch(Exception $e)
				{
					$error = $e->getMessage();
				}
			}
			if( (isset($_REQUEST['action2']) || isset($_POST['action']) ) && ($_REQUEST['action2'] == 'delete' || $_POST['action'] == 'delete') )
			{
				$ids = $_REQUEST['articlepreset'];
				require_once(linksynceparcel_DIR.'model/ArticlePreset/Model.php' );
				$articlePreset = new ArticlePreset();
				
				try
				{
					foreach($ids as $id)
					{
						$articlePreset->delete(array('id' => $id));
					}
					$result = __( 'Article Preset(s) have been deleted successfully.', 'linksynceparcel' );
				}
				catch(Exception $e)
				{
					$error = $e->getMessage();
				}
			}
			include_once(linksynceparcel_DIR.'model/ArticlePreset/List.php');
			include_once(linksynceparcel_DIR.'views/admin/article_presets/list.php');
		}
	}
}
?>