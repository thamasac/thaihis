<?php
namespace backend\modules\ezforms2\classes;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;
/**
 * Description of CommunityBuilder
 *
 * @author appxq
 */
class CommunityBuilder extends Component {
    private $options = [];
    private $object_id = 0;
    private $parent_id = 0;
    private $query_tool = 0;
    private $dataid = 0;
    private $field = '';
    private $type = 'none';
    private $modal = 'modal-ezform-community';
    private $reloadDiv = '';
    
    /**
     * @param string $type
     * @return $this the query object itself
     */
    public function type($type) {
        $this->type = $type;
        return $this;
    }
    
    /**
     * @param string $field
     * @return $this the query object itself
     */
    public function field($field) {
        $this->field = $field;
        return $this;
    }
    
    /**
     * @param boolean $query_tool
     * @return $this the query object itself
     */
    public function query_tool($query_tool) {
        $this->query_tool = $query_tool;
        return $this;
    }
    
    /**
     * @param int $dataid
     * @return $this the query object itself
     */
    public function dataid($dataid) {
        $this->dataid = $dataid;
        return $this;
    }
    
    /**
     * @param int $parent_id
     * @return $this the query object itself
     */
    public function parent_id($parent_id) {
        $this->parent_id = $parent_id;
        return $this;
    }
    
    /**
     * @param array $options options Html
     * @return $this the query object itself
     */
    public function options($options) {
        $this->options = $options;
        return $this;
    }
    
    /**
     * @param int $object_id
     * @return $this the query object itself
     */
    public function object_id($object_id) {
        $this->object_id = $object_id;
        return $this;
    }
    
    /**
     * @param string $reloadDiv div id for reload html
     * @return $this the query object itself
     */
    public function reloadDiv($reloadDiv = '') {
        $this->reloadDiv = $reloadDiv;
        return $this;
    }
    
    /**
     * @param string $modal use modal default modal-ezform-main
     * @return $this the query object itself
     */
    public function modal($modal = null) {
        if (isset($modal)) {
            $this->modal = $modal;
        }
        return $this;
    }
    
    /**
     * @inheritdoc
     * @return CommunityBuilder the newly created [[CommunityBuilder]] instance.
     */
    public static function Community() {
        return Yii::createObject(CommunityBuilder::className());//, [get_called_class()]
    }
    
    public function buildCommunity($limit=20) {
        if(empty($this->reloadDiv)){
           $this->reloadDiv = 'div-community-'.\appxq\sdii\utils\SDUtility::getMillisecTime();
        }
       
        $url = Url::to(['/ezforms2/ezform-community/community-pad',
                    'object_id' => $this->object_id,
                    'modal' => $this->modal,
                    'reloadDiv' => $this->reloadDiv,
                    'parent_id' => $this->parent_id,
                    'query_tool' => $this->query_tool,
                    'field' => $this->field,
                    'type' => $this->type,
                    'dataid' => $this->dataid,
                    'limit' => $limit,
                    
        ]);
        
        $options = $this->options;
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;
        
        $view = Yii::$app->getView();
        $view->registerJs("
            $.ajax({
                method: 'GET',
                url: '" . $url . "',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#{$this->reloadDiv}').html(result);
                }
            });
        ");
        
        return Html::tag('div', '<div class="sdloader "><i class="sdloader-icon"></i></div>', $options);
    }
    
    public function buildQueryTool($limit=20) {
        if(empty($this->reloadDiv)){
           $this->reloadDiv = 'div-querytool-'.\appxq\sdii\utils\SDUtility::getMillisecTime();
        }
        $this->query_tool = 1;
        
        $url = Url::to(['/ezforms2/ezform-community/query-pad',
                    'object_id' => $this->object_id,
                    'modal' => $this->modal,
                    'reloadDiv' => $this->reloadDiv,
                    'parent_id' => $this->parent_id,
                    'query_tool' => $this->query_tool,
                    'field' => $this->field,
                    'type' => $this->type,
                    'dataid' => $this->dataid,
                    'limit' => $limit,
                    
        ]);
        
        $options = $this->options;
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;
        
        $view = Yii::$app->getView();
        $view->registerJs("
            $.ajax({
                method: 'GET',
                url: '" . $url . "',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#{$this->reloadDiv}').html(result);
                }
            });
        ");
        
        return Html::tag('div', '<div class="sdloader "><i class="sdloader-icon"></i></div>', $options);
    }
}
