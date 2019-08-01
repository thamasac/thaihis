<?php
namespace appxq\sdii\widgets;

use yii\widgets\InputWidget;
use yii\helpers\Html;

class DisplayText extends InputWidget {

    public $template = '{icon} {text}';
    public $path;
    public $specific = [];
    public $tag = 'div';
    public $iconTemp = '<i class="fa {icon}"></i> ';
    public $icon = '';
    
    public function init() {
	if (!isset($this->path)) {
	    $icon = '';
	    if(isset($this->specific['icon']) && $this->specific['icon']!=''){
		$icon = strtr($this->iconTemp, ['{icon}'=>$this->specific['icon']]);
	    }

            if(isset($this->options['style'])){
                $this->options['style'] .= ' '.(isset($this->specific['color'])?"color: {$this->specific['color']};":'');
            } else {
                $this->options['style'] = (isset($this->specific['color'])?"color: {$this->specific['color']};":'');
            }
            
	    $this->path = [
		'{text}' => $this->value,
		'{icon}' => $icon,
	    ];
	}
    }

    /**
     * @inheritdoc
     */
    public function run() {
	$options = $this->options;
	
	echo Html::tag($this->tag, strtr($this->template, $this->path), $options);
    }

}
