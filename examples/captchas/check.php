<?php require_once '../bootstrap.php';

    $recaptcha = new \Dnetix\Captchas\ReCaptcha($config_recaptcha);

    if($recaptcha->check($_POST)){
        echo "An human has been do a POST request";
    }else{
        echo "Not an human or just the captcha has not been completed";
    }
