<?php  namespace Dnetix\Captchas;

/**
 * Class ReCaptcha
 * A PHP way to use Google's ReCaptcha
 */
class ReCaptcha implements Captcha {

    public $status = false;
    public $errors = [];

    private $site_key;
    private $secret_key;

    const URL_SCRIPT = 'https://www.google.com/recaptcha/api.js';
    const URL_VERIFY = 'https://www.google.com/recaptcha/api/siteverify';

    function __construct($config){
        if(isset($config['site_key']) && isset($config['secret_key'])) {
            $this->site_key = $config['site_key'];
            $this->secret_key = $config['secret_key'];
        }else{
            throw new \Exception('Initialization values site_key and secret_key for ReCaptcha has not been provided');
        }
    }

    /**
     * @return string
     */
    public function siteKey() {
        return $this->site_key;
    }

    /**
     * @return string
     */
    public function secretKey() {
        return $this->secret_key;
    }

    /**
     * Handles the verification for the incoming request. returns true if the captcha has been accepted otherwise
     * false.
     *
     * @param $response
     * @param null $remoteIp
     * @return bool
     * @throws \Exception
     */
    public function check($response, $remoteIp = null){
        if(empty($response)){
            $this->setStatus(false);
            $this->addError('No input given');
            return false;
        }
        if(is_array($response)){
            if(!isset($response['g-recaptcha-response'])){
                throw new \Exception('The g-recapthca-reponse attribute has not been provided on the data');
            }
            $response = $response['g-recaptcha-response'];
        }
        if(empty($remoteIp)){
            $remoteIp = $_SERVER['REMOTE_ADDR'];
        }

        $HTTPData = self::getHTTPData($response, $remoteIp, $this->secretKey());
        if($HTTPData->success){
            $this->setStatus(true);
            return true;
        }else{
            $this->setStatus(false);
            $this->addError('Captcha retried, please challenge it again.');
            return false;
        }
    }

    /**
     * @return array
     */
    public function getErrors(){
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getErrorMessages() {
        return implode(', ', $this->getErrors());
    }

    /**
     * Returns a HTML string to put on the form that you want to protect with the captcha
     * @return string
     */
    public function getFormTag(){
        return '<div class="g-recaptcha" data-sitekey="' . $this->siteKey() . '"></div>';
    }

    /**
     * Returns a HTML string with a script tag to put on the HEAD part of the HTML or after the close of BODY tag
     * it will load the javascript from google that handles the ReCaptcha
     * @return string
     */
    public function getScriptTag(){
        return '<script src="' . self::URL_SCRIPT . '"></script>';
    }

    private function setStatus($status) {
        $this->status = $status;
    }

    private function addError($error){
        $this->errors[] = $error;
    }

    /* Static URL Query methods */

    private static function submitHTTP($response, $remoteIp, $secretKey){
        return file_get_contents(self::URL_VERIFY . '?' . http_build_query([
                'secret' => $secretKey,
                'response' => $response,
                'remoteip' => $remoteIp
            ]));
    }

    private static function getHTTPData($response, $remoteIp, $secretKey){
        $httpResponse = self::submitHTTP($response, $remoteIp, $secretKey);
        if($httpResponse === false){
            return null;
        }else{
            return json_decode($httpResponse);
        }
    }

}