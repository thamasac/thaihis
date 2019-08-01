<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\modules\user\classes;

/**
 * Description of CNUser
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class CNUserFunc {
    public static function getUserById($type, $user_id){        
        return CNUserQuery::getUserById($type, $user_id);
    }
    public static function createUser($type,$user,$password_set=''){        
        return CNUserQuery::saveUser($type, $user, $password_set);
    }
    public static function checkUser($field, $values){
        $data = \common\modules\user\models\User::find();
        if($field=="email"){
            $user = $data->where('email=:email', [':email'=>$values]);
        }else if($field=="username"){
            $user = $data->where('username=:username', [':username'=>$values]);
        }
        return $user->one();
        
    }
}
