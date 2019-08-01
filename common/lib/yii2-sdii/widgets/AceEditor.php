<?php

namespace appxq\sdii\widgets;

use yii\helpers\Html;
use trntv\aceeditor\AceEditor as BaseAceEditor;

/**
 * Description of AceEditor
 *
 * @author appxq
 */
class AceEditor extends BaseAceEditor {

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        \trntv\aceeditor\AceEditorAsset::register($this->getView());

        $editor_id = isset($this->id) ? $this->id : $this->getId();
        //$editor_id = $this->getId();

        $editor_var = $editor_id;

        $this->getView()->registerJs("var {$editor_var} = ace.edit(\"{$editor_id}\")");
        $this->getView()->registerJs("{$editor_var}.setTheme(\"ace/theme/{$this->theme}\")");
        $this->getView()->registerJs("{$editor_var}.getSession().setMode(\"ace/mode/{$this->mode}\")");

        $textarea_var = 'acetextarea_' . $editor_id;
        $this->getView()->registerJs("
            var {$textarea_var} = $('#{$this->options['id']}').hide();
                {$editor_var}.getSession().setValue({$textarea_var}.val());
                {$editor_var}.getSession().on('change', function(){
                {$textarea_var}.val({$editor_var}.getSession().getValue());
            });
        ");
        Html::addCssStyle($this->containerOptions, 'width: 100%; min-height: 400px');
        Html::addCssStyle($this->options, 'display: none');
        $this->containerOptions['id'] = $editor_id;
        $this->getView()->registerCss("#{$editor_id}{position:relative}");
    }

}
