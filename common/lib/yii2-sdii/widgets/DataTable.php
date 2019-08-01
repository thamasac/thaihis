<?php
namespace appxq\sdii\widgets;

use yii\widgets\InputWidget;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfHelper;

class DataTable extends InputWidget {

//    public $template = '<div id="{id}" class="panel panel-{theme}">
//                            <div class="panel-heading">
//                                <h3 class="panel-title">{icon}{text}</h3>
//                            </div>
//                            <div class="panel-body">
//                                {gridview}
//                            </div>
//                        </div>';
    public $template = '{gridview}';
    public $path;
    public $specific = [];
    public $tag = 'div';
    public $iconTemp = '<i class="fa {icon}"></i> ';
    public $icon = '';
    public $theme = 'default';
    public $ezf_id;
    public $ezf_field_id;
    public $column;
    public $modal_size = 'modal-xxl';
    public $filter = 0;
    public $default_column = 0;


    public function init() {
	if (!isset($this->path)) {
	    $icon = '';
	    if(isset($this->specific['icon']) && $this->specific['icon']!=''){
		$icon = strtr($this->iconTemp, ['{icon}'=>$this->specific['icon']]);
	    }
            $disabled = 0;
	    if(isset($this->options['disabled']) && $this->options['disabled']){
                $disabled = 1;
            }
            
	    $this->options['style'] = isset($this->specific['color'])?"color: {$this->specific['color']};":'';
            
            if (isset($this->model)) {
                $target = Html::getAttributeValue($this->model, 'id');
            } else {
                $target = '';
            }
            
            $uiView = \backend\modules\ezforms2\classes\EzfHelper::ui($this->ezf_id)
            ->modal('modal-'.$this->ezf_field_id)
            ->reloadDiv('datawidget-'.$this->ezf_field_id)
            ->data_column($this->column)
            ->target($target)
            ->disabled($disabled)
            ->theme($this->theme)
            ->filter($this->filter)
            ->default_column($this->default_column)
            ->title($icon . ' ' . $this->value);

	    $this->path = [
		'{gridview}' => $uiView->buildGrid(), //EzfHelper::uiGrid($this->ezf_id, $target, 'datawidget-'.$this->ezf_field_id, 'modal-'.$this->ezf_field_id, $this->column, $disabled),
                //'{btnadd}' => !$disabled?EzfHelper::btnAdd($this->ezf_id, $target, [], 'datawidget-'.$this->ezf_field_id, 'modal-'.$this->ezf_field_id):'',
                //'{btnadd}' => !$disabled?EzfHelper::btn($this->ezf_id)->target($target)->reloadDiv('datawidget-'.$this->ezf_field_id)->modal('modal-'.$this->ezf_field_id)->buildBtnAdd():'',
//                '{text}' => $this->value,
//                '{theme}' => $this->theme,
//		'{icon}' => $icon,
//                '{id}'=>'data-main-'.$this->ezf_field_id
	    ];
	}
    }

    /**
     * @inheritdoc
     */
    public function run() {
        $view = $this->getView();
        
	$options = $this->options;
	$submodal = '<div id="modal-'.$this->ezf_field_id.'" class="fade modal" role="dialog"><div class="modal-dialog '.$this->modal_size.'"><div class="modal-content"></div></div></div>';
        if (!isset($this->model)) {
            $submodal = '';
        }
        
	echo Html::tag($this->tag, strtr($this->template, $this->path), $options);
        $inputMain = "ez{$this->ezf_id}-id";
        
        $view->registerJs("
        var hasForm = $( 'body' ).has( '#$inputMain' ).length;
        if(hasForm>0){
            //$('#ezform-{$this->options['data-ezfid']} div[item-id={$this->ezf_field_id}]').hide();
        } else {
            $('#ezf-modal-box').append('$submodal');
            
        }   
        
        $('#modal-{$this->ezf_field_id}').on('hidden.bs.modal', function(e){
            var hasmodal = $('body .modal').hasClass('in');
            if(hasmodal){
                $('body').addClass('modal-open');
            } 
        });

        function getUiAjax(url, divid) {
            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#'+divid).html(result);
                }
            });
        }
        
        ");
    }

}
