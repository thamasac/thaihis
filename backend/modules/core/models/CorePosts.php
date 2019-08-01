<?php

namespace backend\modules\core\models;

use Yii;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "core_posts".
 *
 * @property string $ID
 * @property string $post_author
 * @property string $post_date
 * @property string $post_date_gmt
 * @property string $post_content
 * @property string $post_title
 * @property string $post_excerpt
 * @property string $post_status
 * @property string $comment_status
 * @property string $ping_status
 * @property string $post_password
 * @property string $post_name
 * @property string $to_ping
 * @property string $pinged
 * @property string $post_modified
 * @property string $post_modified_gmt
 * @property string $post_content_filtered
 * @property string $post_parent
 * @property string $guid
 * @property integer $menu_order
 * @property string $post_type
 * @property string $post_mime_type
 * @property integer $comment_count
 */
class CorePosts extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
	return [
	    [
		'class' => SluggableBehavior::className(),
		'attribute' => 'post_title',
		'slugAttribute' => 'post_name',
		'ensureUnique' => true,
		'immutable' => false,
	    ],
	];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'core_posts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
	    [['post_title'], 'required'],
            [['post_author', 'post_parent', 'menu_order', 'comment_count'], 'integer'],
	    [['post_parent', 'menu_order', 'comment_count'], 'default', 'value' => 0],
            [['post_content', 'post_title', 'post_excerpt', 'to_ping', 'pinged', 'post_content_filtered'], 'string'],
            [['post_status', 'comment_status', 'ping_status', 'post_password', 'post_type'], 'string', 'max' => 20],
            [['post_name'], 'string', 'max' => 200],
            [['guid'], 'string', 'max' => 255],
            [['post_mime_type'], 'string', 'max' => 100],
	    [['post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt'], 'safe'],
            [['post_name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'ID' => Yii::t('core', 'ID'),
	    'post_author' => Yii::t('core', 'Author'),
	    'post_date' => Yii::t('core', 'Date'),
	    'post_date_gmt' => Yii::t('core', 'Date GMT'),
	    'post_content' => Yii::t('core', 'Content'),
	    'post_title' => Yii::t('core', 'Title'),
	    'post_excerpt' => Yii::t('core', 'Excerpt'),
	    'post_status' => Yii::t('core', 'Status'),
	    'comment_status' => Yii::t('core', 'Comment Status'),
	    'ping_status' => Yii::t('core', 'Ping Status'),
	    'post_password' => Yii::t('core', 'Password'),
	    'post_name' => Yii::t('core', 'Name'),
	    'to_ping' => Yii::t('core', 'To Ping'),
	    'pinged' => Yii::t('core', 'Pinged'),
	    'post_modified' => Yii::t('core', 'Modified'),
	    'post_modified_gmt' => Yii::t('core', 'Modified GMT'),
	    'post_content_filtered' => Yii::t('core', 'Content Filtered'),
	    'post_parent' => Yii::t('core', 'Parent'),
	    'guid' => Yii::t('core', 'GUID'),
	    'menu_order' => Yii::t('core', 'Menu Order'),
	    'post_type' => Yii::t('core', 'Type'),
	    'post_mime_type' => Yii::t('core', 'Mime Type'),
	    'comment_count' => Yii::t('core', 'Count'),
	];
    }
}
