<?php
require_once realpath(__DIR__) . '/Autoload.php';

TechAPIAutoloader::register();

use TechAPI\Constant;
use TechAPI\Client;
use TechAPI\Auth\ClientCredentials;

// config api
Constant::configs(array(
    'mode'            => Constant::MODE_LIVE,
    //'mode'            => Constant::MODE_SANDBOX,
    'connect_timeout' => 15,
    'enable_cache'    => false,
    'enable_log'      => true,
    'log_path'    => realpath(__DIR__) . '/logs'
));


// config client and authorization grant type
function getTechAuthorization()
{    
    $client = new Client(
        //'YOUR_CLIENT_ID',
        //'YOUR_CLIENT_SECRET',
        //'e615D85fc918f252e1754Ce2391c8Ef923AAB401',
        //'663642d023602e28784F8789dC939f14a54ece5f588848beBdd6314fab8c274de8B618a4',
        '80f8cB0a65b741F39a0c4c302685BBccfd5426e0',
        '9Ad7107767DB2891cbf168d2cBd5Fbaff29baDc823725b89f5c94a8976b5b7bffd88dF0e',
        array('send_brandname_otp') // array('send_brandname', 'send_brandname_otp')
    );
   
    return new ClientCredentials($client);
}