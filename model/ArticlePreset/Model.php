<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
require_once(linksynceparcel_DIR.'model/Model.php' );
class ArticlePreset extends LinksynceparcelModel
{
	public function __construct()
    {
		global $wpdb;
        parent::__construct($wpdb->prefix . 'linksynceparcel_article_preset');
    }
}
?>