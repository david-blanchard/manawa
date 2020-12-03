<?php
include 'bootstrap.php';


echo urlencode('https://localhost');
echo PHP_EOL;
echo PHP_EOL;

echo urlencode('https://www.withings.com');

use waytohealth\OAuth2\Client\Provider\Withings;

$client_id     = '2dae3c40cec6e26b585850fc7bf78954446870fa6f26fcbc9af945a1c38aea18';
$client_secret = '8ad3ed2e5a941d828cbd6148b8f018e25c0159e4df7e2066b5c5b6325f89514d';

$provider = new Withings([
    'clientId'          => $client_id,
    'clientSecret'      => $client_secret,
    'redirectUri'       => 'https://localhost'
]);

// start the session
session_start();

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {

    // Fetch the authorization URL from the provider; this returns the
    // urlAuthorize option and generates and applies any necessary parameters
    // (e.g. state).
    $authorizationUrl = $provider->getAuthorizationUrl();

    // Get the state generated for you and store it to the session.
    $_SESSION['oauth2state'] = $provider->getState();

    // Redirect the user to the authorization URL.
    header('Location: ' . $authorizationUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || array_key_exists('oauth2state', $_SESSION) && ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    try {

        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // We have an access token, which we may use in authenticated
        // requests against the service provider's API.
        echo $accessToken->getToken() . "\n";
        echo $accessToken->getRefreshToken() . "\n";
        echo $accessToken->getExpires() . "\n";
        echo ($accessToken->hasExpired() ? 'expired' : 'not expired') . "\n";

        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $provider->getResourceOwner($accessToken);

        var_export($resourceOwner->toArray());

        // The provider provides a way to get an authenticated API request for
        // the service, using the access token; it returns an object conforming
        // to Psr\Http\Message\RequestInterface.
        $request = $provider->getAuthenticatedRequest(
            Withings::METHOD_GET,
            Withings::BASE_WITHINGS_API_URL . '/v2/user?action=getdevice',
            $accessToken,
            ['headers' => [Withings::HEADER_ACCEPT_LANG => 'en_US'], [Withings::HEADER_ACCEPT_LOCALE => 'en_US']]
            // Fitbit uses the Accept-Language for setting the unit system used
            // and setting Accept-Locale will return a translated response if available.
            // https://dev.fitbit.com/docs/basics/#localization
        );
        // Make the authenticated API request and get the parsed response.
        $response = $provider->getParsedResponse($request);

        // If you would like to get the response headers in addition to the response body, use:
        //$response = $provider->getResponse($request);
        //$headers = $response->getHeaders();
        //$parsedResponse = $provider->parseResponse($response);

    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());

    }

}

/*

$client_secret = '8ad3ed2e5a941d828cbd6148b8f018e25c0159e4df7e2066b5c5b6325f89514d';
$client_id     = '2dae3c40cec6e26b585850fc7bf78954446870fa6f26fcbc9af945a1c38aea18';
$nonce         = 'The nonce I retrieved using service: Signature v2 - Getnonce';

$signed_params = array(
    'action'     => 'activate',
    'client_id'  => $client_id,
    'nonce'      => $nonce,
);   
ksort($signed_params);
$data = implode(",", $signed_params);
$signature = hash_hmac('sha256', $data, $client_secret);

$call_post_params = array(
    // Set the generated signature
    'signature'    => $signature,
    
    // Set the signed parameters as clear text in the call post parameters
  'action'       => 'activate',
  'client_id'    => $client_id,
  'nonce'        => $nonce,
    
    // Set other parameters requested to call the service (here we are calling "User v2 - Create") 
    'redirect_uri' => 'https://www.withings.com',
    'birthdate'    => 1563746400
    // [...]
);

// Then call the service by using the $call_post_params array as post parameters

*/