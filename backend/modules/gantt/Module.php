<?php

namespace backend\modules\gantt;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\gantt\controllers';
    static public $pmsModuleId = "1521801182077746900";

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
    
    public static $formsId = [
    	'response_ezf_id'=>'1532333795009058500',
        'request_task_form'=>'1554624848096777200',
        'reject_request_form'=>'1554624929026878800',
        'maintask_form'=>'1520711894072728800',
        'decline_task_form'=>'1555485950088259100',
        'task_item_type'=>'1555565606003032900',
        'res_own_ezf_id'=>'1555497567060110800',
    	'refer_task'=>'1555907721029866600',
        'task_ezf_id'=>'1520742721018881300',
    ];
    public static $formsTable = [
        'request_task_form'=>'zdata_request_task',
        'reject_request_form'=>'zdata_reject_request',
        'maintask_form'=>'zdata_project',
        'decline_task_form'=>'zdata_decline_task',
        'task_item_type'=>'zdata_decline_task',
    	'res_own_ezf_id'=>'zdata_response_owner',
    	'refer_task'=>'zdata_refer_task',
    	'response_ezf_id'=>'zdata_task_response',
        'task_ezf_id'=>'zdata_activity',
    ];
    
    
}
