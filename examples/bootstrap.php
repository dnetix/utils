<?php

require_once __DIR__ . '/../vendor/autoload.php';

$config_recaptcha = [
    'site_key' => '',
    'secret_key' => '',
];

/*
 * Example to use with Laravel because it will have troubles with the regular session
 * By default the SDK API will look for the environment variables:
 * FACEBOOK_APP_ID
 * FACEBOOK_APP_SECRET
 * FACEBOOK_LOGIN_CALLBACK_URL
 * So you should set those in the .env file, but if you're not using Laravel or cant set the enviroment variables
 * just merge the configuration to the constructor like the one below this commented function
function facebook($accessToken = null){
    return new \Dnetix\Social\FacebookHandler([
        'persistent_data_handler' => new \Dnetix\Social\FacebookLaravelSessionHandler(session()),
        'access_token' => $accessToken,
        'default_access_token' => $accessToken
    ]);
}
 * */

function facebook($accessToken = null)
{
    $config = [
        'app_id' => 'asvbd',
        'app_secret' => 'svdasdvasv',
        'login_callback_url' => 'davsdav',
    ];

    $config['access_token'] = $accessToken;
    $config['default_access_token'] = $accessToken;

    return new \Dnetix\Social\FacebookHandler($config);
}
