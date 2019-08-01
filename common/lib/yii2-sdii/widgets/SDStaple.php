<?php
namespace appxq\sdii\widgets;
/**
 * SDComponent class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 25 พ.ย. 2558 13:08:20
 * @link http://www.appxq.com/
 * @example 
 */
use Yii;
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\helpers\Url;

class SDStaple extends Select2 {

    public $ezf_id;
    public $ezf_field_id;
    public $modal_size = 'modal-xxl';
    public $target = '';
    public $auto_id = '';
    
    public function init() {
	parent::init();
        if(isset($this->options['data-auto']) && $this->options['data-auto']!=''){
            $this->auto_id = $this->options['data-auto'];
        }
        
        $this->pluginOptions['ajax']['url'] = Url::to([$this->pluginOptions['ajax']['url'], 'auto_id'=> $this->auto_id]);
        
    }
    
    
}
