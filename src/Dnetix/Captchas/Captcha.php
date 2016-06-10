<?php

namespace Dnetix\Captchas;


interface Captcha
{

    /**
     * Returns the HTML code to insert in the form that will be uploaded
     * @return string
     */
    public function getFormTag();

    /**
     * Returns the HTML script tag(s) necessary in order to work properly
     * @return string
     */
    public function getScriptTag();

    /**
     * Receives the captcha result and checks if its valid or not
     * @param $response
     * @param $remoteIp
     * @return bool
     */
    public function check($response, $remoteIp = null);

    /**
     * Returns the string with the errors if any
     * @return string
     */
    public function getErrorMessages();

}