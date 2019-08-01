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
class NotifyJob extends \yii\base\BaseObject implements \yii\queue\JobInterface {

    //put your code here




    public function execute($queue) {
        \Yii::$app->mailer->compose('@backend/mail/layouts/notify', [
                    'notify' => 'Test',
                    'detail' => 'Setail',
                    'url' => '/notify',
                ])
                ->setFrom(['ncrc.damasac@gmail.com' => 'nCRC Thailand'])
                ->setTo('aomruk12123@gmail.com')
                ->setSubject('test')
                //->attach(Yii::getAlias('@webroot').'/attach/'.'brochure.pdf')
                //->attach(Yii::getAlias('@webroot').'/attach/'.'Poster.pdf')        
                ->send();
        
        

        \backend\modules\line\classes\LineFn::setLine()
                ->message('Detail')
                ->altMessage('test')
                ->typeTemplateConfirm('/notify')
                ->token('0PKMkmn+JngtXzvXsyHLOMA2cDBxZK2qE4aQVT9JSAR8+qkoMzyQSHgtDwezogrYQAlDBgWCp+2MkCw8v1NRzEvSijH0Lq2kQTnFCheGi/t4ASm4B2iC31knSxdVnxAUCuteev8/zW7d78TElFgVfQdB04t89/1O/w1cDnyilFU=')
                ->pushMessage('U5d7e0c99aefac210694320e6b7d3c300');
    }

}
