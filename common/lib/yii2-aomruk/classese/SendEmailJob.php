<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dms\aomruk\classese;

/**
 * Description of SendEmailJob
 *
 * @author AR Soft
 */
class SendEmailJob extends \yii\base\BaseObject implements \yii\queue\Job  {
    //put your code here
    
    public $notify;
    public $detail;
    public $email;


    public function execute($queue)
    {
        
    }
}
