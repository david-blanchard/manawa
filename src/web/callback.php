<?php
include 'bootstrap.php';

use OAuth2\Client;
use OAuth2\GrantType;
use OAuth2\GrantType\AuthorizationCode;


const CLIENT_ID     = '2dae3c40cec6e26b585850fc7bf78954446870fa6f26fcbc9af945a1c38aea18';
const CLIENT_SECRET = '8ad3ed2e5a941d828cbd6148b8f018e25c0159e4df7e2066b5c5b6325f89514d';


const REDIRECT_URI           = 'https://localhost/callback.php';
const AUTHORIZATION_ENDPOINT = 'https://www.withings.com/authorize';
const TOKEN_ENDPOINT         = 'https://www.withings.com/access_token';

$client = new OAuth2\Client(CLIENT_ID, CLIENT_SECRET);
if (!isset($_GET['code']))
{
    $auth_url = $client->getAuthenticationUrl(AUTHORIZATION_ENDPOINT, REDIRECT_URI);
    header('Location: ' . $auth_url);
    die('Redirect');
}
else
{
    $params = array('code' => $_GET['code'], 'redirect_uri' => REDIRECT_URI);
    $response = $client->getAccessToken(TOKEN_ENDPOINT, 'authorization_code', $params);
    parse_str($response['result'], $info);
    $client->setAccessToken($info['access_token']);
    //$response = $client->fetch('https://graph.facebook.com/me');
    //var_dump($response, $response['result']);
}