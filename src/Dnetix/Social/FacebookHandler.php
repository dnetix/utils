<?php
namespace Dnetix\Social;

use Facebook\Facebook;

/**
 * Class FacebookHandler
 * Extends the Facebook class in order to make easier the management of the functions provided by the API
 */
class FacebookHandler extends Facebook {
    private $permissions = ['email', 'public_profile', 'user_friends'];
    private $login_callback_url;
    private $redirect_helper;
    private $access_token;
    private $error;

    public function __construct($config = []) {
        try {
            parent::__construct($config);
        }catch(\Exception $e){
            $this->error = $e->getMessage();
        }

        $this->login_callback_url = isset($config['login_callback_url']) ? $config['login_callback_url'] : getenv('FACEBOOK_LOGIN_CALLBACK_URL');
        $this->access_token = isset($config['access_token']) ? $config['access_token'] : null;
    }

    public function permissions() {
        return $this->permissions;
    }

    public function loginCallbackUrl() {
        return $this->login_callback_url;
    }

    public function redirectHelper() {
        if(!$this->redirect_helper){
            $this->redirect_helper = $this->getRedirectLoginHelper();
        }
        return $this->redirect_helper;
    }

    /**
     * The changes the permissions by default
     * @param $permissions
     * @return $this
     */
    public function setPermissions($permissions) {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * Returns the link to make the authentication on Facebook OAuth
     * @return string
     */
    public function getLoginUrl() {
        if($this->isConnected()){
            return $this->redirectHelper()->getLoginUrl($this->loginCallbackUrl(), $this->permissions());
        }
        return null;
    }

    public function accessToken() {
        if(!$this->access_token){
            $this->getAccessToken();
        }
        return $this->access_token;
    }

    public function setAccessToken($accessToken) {
        $this->setDefaultAccessToken($accessToken);
        $this->access_token = $accessToken;
        return $this;
    }

    public function error() {
        return $this->error;
    }

    public function getAccessToken() {
        try {
            $this->access_token = $this->redirectHelper()->getAccessToken();
            return $this->access_token;
        } catch (\Exception $e){
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * Checks if there is errors while using the API, for example, if the application data has not been provided
     * @return bool
     */
    public function isConnected() {
        return !$this->error;
    }

    /**
     * Transforms the actual Access Token (expires in 2 hours) to a long lived one (2 months)
     * @return \Facebook\Authentication\AccessToken
     */
    public function getLongLivedAccessToken() {
        return $this->getOAuth2Client()->getLongLivedAccessToken($this->accessToken());
    }

    /**
     * @return bool|\Facebook\GraphNodes\GraphUser
     */
    public function getMe() {
        if(!$this->accessToken()){
            if(!$this->error){
                $this->error = 'An access token has not been provided';
            }
            return false;
        }
        $this->setDefaultAccessToken($this->accessToken());
        try {
            $userNode = $this->get('/me?fields=id,email,first_name,last_name,link,locale,name,timezone,gender,picture.type(large)')->getGraphUser();
            return $userNode;
        } catch (\Exception $e){
            $this->error = $e->getMessage();
            return false;
        }
    }
}