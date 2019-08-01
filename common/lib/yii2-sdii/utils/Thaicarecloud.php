<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace appxq\sdii\utils;

use yii\authclient\OAuth2;
use yii\authclient\OAuthToken;
/**
 * Description of Thaicarecloud
 *
 * @author appxq
 */
class Thaicarecloud extends OAuth2{
    
    public $authUrl = 'http://localhost/oauth_server/backend/web/site/authorize';

    public $tokenUrl = 'http://localhost/oauth_server/backend/web/oauth2/token';

    public $apiBaseUrl = 'http://localhost/oauth_server/backend/web/users';
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->scope === null) {
            $this->scope = implode(' ', [
                'id',
                'email',
            ]);
        }
        
    }
    
    protected function initUserAttributes()
    {
        return $this->api('userinfo', 'GET');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function defaultName()
    {
        return 'thaicarecloud';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultTitle()
    {
        return 'Thaicarecloud';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultViewOptions()
    {
        return [
            'popupWidth' => 860,
            'popupHeight' => 480,
        ];
    }
    
    
    
}
