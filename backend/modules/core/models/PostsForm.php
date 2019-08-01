<?php
namespace backend\modules\core\models;
/**
 * TagsForm class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 2 ธ.ค. 2558 12:02:34
 * @link http://www.appxq.com/
 * @example 
 */
use Yii;
use yii\base\Model;
use backend\modules\core\classes\CoreFunc;

class PostsForm extends Model {
    
    public $ID;
    public $categories;
    public $post_author;
    public $post_date;
    public $post_date_gmt;
    public $post_content;
    public $post_title;
    public $post_excerpt;
    public $post_status;
    public $comment_status;
    public $ping_status;
    public $post_password;
    public $post_name;
    public $to_ping;
    public $pinged;
    public $post_modified;
    public $post_modified_gmt;
    public $post_content_filtered;
    public $post_parent;
    public $guid;
    public $menu_order;
    public $post_type;
    public $post_mime_type;
    public $comment_count;
    public $sticky_posts;
    public $tags_id;
    public $post_format;
    public $page_template;
    
    public function rules()
    {
	return [
	    [['post_title'], 'required'],
            [['post_author', 'post_parent', 'menu_order', 'comment_count'], 'integer'],
            [['post_content', 'post_title', 'post_excerpt', 'to_ping', 'pinged', 'post_content_filtered'], 'string'],
            [['post_status', 'comment_status', 'ping_status', 'post_password', 'post_type'], 'string', 'max' => 20],
            [['post_name'], 'string', 'max' => 200],
            [['guid'], 'string', 'max' => 255],
            [['post_mime_type'], 'string', 'max' => 100],
	    [['page_template', 'post_format', 'tags_id', 'ID', 'sticky_posts', 'categories', 'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt'], 'safe']
        ];
    }
    
    public function attributeLabels() {
	return [
	    'ID' => CoreFunc::t('ID'),
	    'post_author' => CoreFunc::t('Author'),
	    'post_date' => CoreFunc::t('Date'),
	    'post_date_gmt' => CoreFunc::t('Date GMT'),
	    'post_content' => CoreFunc::t('Content'),
	    'post_title' => CoreFunc::t('Title'),
	    'post_excerpt' => CoreFunc::t('Excerpt'),
	    'post_status' => CoreFunc::t('Status'),
	    'comment_status' => CoreFunc::t('Allow Comments'),
	    'ping_status' => CoreFunc::t('Allow Pings'),
	    'post_password' => CoreFunc::t('Password'),
	    'post_name' => CoreFunc::t('Name'),
	    'to_ping' => CoreFunc::t('To Ping'),
	    'pinged' => CoreFunc::t('Pinged'),
	    'post_modified' => CoreFunc::t('Modified'),
	    'post_modified_gmt' => CoreFunc::t('Modified GMT'),
	    'post_content_filtered' => CoreFunc::t('Content Filtered'),
	    'post_parent' => CoreFunc::t('Parent'),
	    'guid' => CoreFunc::t('Guid'),
	    'menu_order' => CoreFunc::t('Order'),
	    'post_type' => CoreFunc::t('Type'),
	    'post_mime_type' => CoreFunc::t('Mime Type'),
	    'comment_count' => CoreFunc::t('Comment Count'),
	    'sticky_posts' => CoreFunc::t('Make this post sticky'),
	    'page_template' => CoreFunc::t('Template'),
	    'categories' => CoreFunc::t('Categories'),
	    'tags' => CoreFunc::t('Tags'),
	];
    }
    
    public function save()
    {
	try {
	    $model = new CorePosts();
	    
	    if(!empty($this->ID)){
		$model = CorePosts::findOne($this->ID);
	    } 
	    
	    $model->attributes = $this->attributes;
	   
	    $model->save();
	    
	    return $model;
	} catch (\yii\db\Exception $e) {
	   return false;
	}
    }
    
}
