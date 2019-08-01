<?php

namespace dms\joke\widgets;

/**
 * SDProvince class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 25 พ.ย. 2558 13:08:20
 * @link http://www.appxq.com/
 * @example 
 */
use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class PMSTaskCompletionInput extends InputWidget {

//    public $max = 100;
//    public $min = 0;

    public function init() {
        parent::init();

        $this->id = \appxq\sdii\utils\SDUtility::getMillisecTime();

        if (isset($this->options['class'])) {
            $this->options['class'] .= ' slider';
        } else {
            $this->options['class'] = 'slider';
        }

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->id . "-slider";
        }
        if (!isset($this->options['max'])) {
            $this->options['id'] = 100;
        }
        if (!isset($this->options['min'])) {
            $this->options['id'] = 0;
        }
        $this->options['text'] = isset($this->options['text']) ? ' ' . $this->options['text'] : '';
    }

    public function run() {
        if ($this->hasModel()) {
            if ($this->model[$this->attribute] == '') {
                $this->model[$this->attribute] = 0;
                $this->value = 0;
            } else {
                $this->value = $this->model[$this->attribute];
            }
            $task_type = $this->options['task_type'];
            $ezf_id = $this->model['ezf_field']['ezf_id'];
            $field_name = $this->attribute;
            if ($this->attribute == 'task_complete') {
                $ezfield_data = $this->model['ezf_field']['ezf_field_data'];
            }

            if ($task_type == '2') {
                $btnSelectTask = Html::button('Select Task related', ['class' => 'btn btn-success ', 'id' => 'select_task_related', 'data-toggle' => 'popover'
                            , 'data-ezf_id' => $ezf_id,'data-dataid'=>$this->model['id'], 'data-field_name' => $field_name, 'style' => 'width:100%']);
                echo $btnSelectTask;
            } else {
                $btnSelectTask = Html::button('View Task to complete', ['class' => 'btn btn-success btn_select_task', 'data-toggle' => 'popover'
                            , 'data-ezf_id' => $ezf_id,'data-dataid'=>$this->model['id'], 'data-field_name' => $field_name, 'style' => 'width:100%']);
                echo Html::activeInput('range', $this->model, $this->attribute, $this->options) .
                Html::tag('div', Html::tag('div', $this->options['min'], ['class' => 'pull-left  label label-info']) .
                        Html::tag('span', $this->value . $this->options['text'], ['id' => $this->id . '_div']) .
                        Html::tag('div', $this->options['max'], ['class' => 'pull-right label label-info']), ['class' => 'text-center']) . '<br>' . $btnSelectTask;
            }
        } else {
            if ($this->value == '') {
                $this->value = 0;
            }

            $task_type = $this->options['task_type'];
            $ezf_id = $this->model['ezf_field']['ezf_id'];
            $field_name = $this->attribute;

            if ($task_type == '2') {
                $btnSelectTask = Html::button('Select Task related', ['class' => 'btn btn-success ', 'id' => 'select_task_related', 'data-toggle' => 'popover'
                            , 'data-ezf_id' => $ezf_id, 'data-field_name' => $field_name, 'style' => 'width:100%']);
                echo $btnSelectTask;
            } else {
                $btnSelectTask = Html::button('View Task related', ['class' => 'btn btn-success btn_select_task', 'data-toggle' => 'popover'
                            , 'data-ezf_id' => $ezf_id, 'data-field_name' => $field_name, 'style' => 'width:100%']);
                echo Html::input('range', $this->name, $this->value, $this->options) . Html::tag('div', Html::tag('div', $this->options['min'], ['class' => 'pull-left label label-info']) .
                        Html::tag('span', $this->value . $this->options['text'], ['id' => $this->id . '_div']) .
                        Html::tag('div', $this->options['max'], ['class' => 'pull-right label label-info']), ['class' => 'text-center']) . '<br>' . $btnSelectTask;
            }
        }

        $this->registerClientScript();
        $this->registerClientCss();
    }

    public function registerClientScript() {
        $view = $this->getView();
        $view->registerJs("
                $(document).ready(function(){
//                    var pop = $('[data-toggle=\"popover\"]');
//                    pop.attr('data-content','<a href=\"#\">Task1</a>');
//                    pop.popover({
//                        container: 'body',
//                        html : true ,
//                    });
                });
                
                
                $('#modal-ezform-main').on('hidden.bs.modal',function(){
                    var modal = $(this);
                    modal.find('.popover').remove();
                });
                $('#{$this->options['id']}').on('input',function(){
                    $('#{$this->id}_div').html($(this).val()+'{$this->options['text']}');
                });
                
                $('.btn_select_task').on('click',function(){
                    var ezf_id = $(this).attr('data-ezf_id');
                    var field_name = $(this).attr('data-field_name');
                    var btn = $(this);
                    
                    $.get('/ezforms2/task-completion/select-task',{ezf_id:ezf_id,field_name:field_name},function(result){
                        //$('.popover-content').html(result);
                        btn.popover({
                            container: '#modal-ezform-main',
                            html : true ,
                            content:result,
                            title:'The task to complete. <button class=\"btn btn-default\" id=\"btn_close_popover\">Close</button>',
                        });
                        btn.popover('show');
                        $('#btn_close_popover').on('click',function(){
                            var btn = $(this);
                            btn.parents('.popover').remove();
                        });
                        
                    });
                });
                
                $('#select_task_related').click(function(){
                    var modal = $('#modal-ezform-community');
                    var ezf_id = $(this).attr('data-ezf_id');
                    var dataid = $(this).attr('data-dataid');
                    var field_name = $(this).attr('data-field_name');
                    modal.find('.modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                    var url = '/ezforms2/task-completion/select-task-related?ezf_id='+ezf_id+'&field_name='+field_name+'&dataid='+dataid;
                    modal.modal();
                    modal.find('.modal-content').load(url);
                });
               

            ");
    }

    public function registerClientCss() {
        $view = $this->getView();
        $view->registerCss("
                .slider:hover {
                    cursor: pointer;
                    
                }
                .popover{
                    max-width: 100%; /* Max Width of the popover (depending on the container!) */
                }
            ");
    }

}
