<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\api\v1\classes;
use backend\modules\api\v1\classes\MyJWT;
/**
 * Description of JwtWebToken
 *
 * @author chanpan
 */
class JwtWebToken {
    /**
     * 
     * @param type $payload type array or string 
     * @param type $key string 
     * @param type $alg string 'HS512', 'RS256','HS256'
     * @return \Lindelius\JWT\Exception
     */
    public static function enCode($payload, $key, $alg='HS256'){
        try {
        $jwt = new MyJWT();
        $jwt->exp = time() + (60 * 60 * 2); // expire after 2 hours
        $jwt->iat = time();
        $jwt->data = $payload;
        $jwt->setAlg($alg);

        $accessToken = $jwt->encode($key);
        return $accessToken;
        }catch(\Lindelius\JWT\Exception $exception) {
            return $exception;
        }
    }
    
    /**
     * 
     * @param type $token jwt token 
     * @param type $key secreate key
     * @param type $alg 
     * @return type
     */
    public static function deCode($token, $key, $alg='HS256'){
        $jwt = MyJWT::decode($token);
        $jwt->setAlg($alg);
        //$isAdmin = (bool) $decodedJwt->admin;
        $jwt->verify($key);
        
        return $jwt;
    }
    
}
