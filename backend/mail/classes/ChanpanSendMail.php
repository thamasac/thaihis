<?php
namespace backend\mail\classes;
class CNSendMail {
    public static function SendMailTemplate($email_to, $title){
        $result = \Yii::$app->mailer->compose('@backend/mail/layouts/register',[
            'fullname'=>'nuttaphon chanpan'
        ])
        ->setFrom(['ncrc.damasac@gmail.com'=>$title])
        ->setTo($email_to)
        ->setSubject('ยินดีต้อนรับสู่งานประชุมวิชาการโรงพยาบาลขอนแก่น 2558')
        //->attach(Yii::getAlias('@webroot').'/attach/'.'brochure.pdf')
        //->attach(Yii::getAlias('@webroot').'/attach/'.'Poster.pdf')        
        ->send();
        return $result; 
    }
    
    public static function SendMailNotTemplate($email_to, $title){
        $result = \Yii::$app->mailer->compose()
                    ->setFrom(['ncrc.damasac@gmail.com'=>$title])
                    ->setTo($email_to)
                    ->setSubject('คำถามของคุณที่ ' . \Yii::$app->name)
                    ->setTextBody('หัวข้อ ติดตามคำถามของคุณได้ที่ : test') //เลือกอยางใดอย่างหนึ่ง
                    ->setHtmlBody('หัวข้อ  ติดตามคำถามของคุณได้ที่ : test ') //เลือกอยางใดอย่างหนึ่ง
                    ->send();
        return $result; 
    }
}
