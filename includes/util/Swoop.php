<?php
include_once( plugin_dir_path( __FILE__ ) . '../config.php' );
class Swoop {

  public $clientId;
  private $clientSecret;
  public $redirectUrl;

  public $accessToken;
  public $refreshToken;
  public $idToken;
  public $userMeta;

  public function __construct($clientId, $clientSecret, $redirectUrl) {
    $this->clientId     = $clientId;
    $this->clientSecret = $clientSecret;
    $this->redirectUrl  = $redirectUrl;
  }

  public function loginUrl($additionalParams = array()) {
    $loginUrl = SWOOP_URL.SWOOP_AUTH_ENDPOINT .
    '?client_id='    . $this->clientId.
    '&redirect_uri=' . $this->redirectUrl .
    '&scope=email'   .
    '&response_type=code';

    if(count($additionalParams) > 0) {
      $loginUrl = $loginUrl . '&' . http_build_query($additionalParams);
    }

    return $loginUrl;
  }

  public function callback($code) {
    try {
      $response = $this->post(
        SWOOP_URL . SWOOP_TOKEN_ENDPOINT,
        array(
          'grant_type'    => 'authorization_code',
          'code'          => $code,
          'client_id'     => $this->clientId,
          'client_secret' => $this->clientSecret,
          'redirect_uri'  => $this->redirectUrl
        )
      );

      $json = json_decode($response);
      $idToken = $json->{'id_token'};
      $decoded = $this->decodeToken($idToken);
      $email = $decoded->{'email'};

      $this->idToken = $json->id_token;;
      $this->accessToken = $json->access_token;
      $this->refreshToken = $json->refresh_token;
      $this->userMeta = $decoded;

      return $this->userMeta;
    } catch ( Exception $e) {
      return false;
    }
  }

  public function decodeToken($token) {
    return json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))));
  }

/**
 * Send a POST request without using PHP's curl functions.
 *
 * @param string $url The URL you are sending the POST request to.
 * @param array $postVars Associative array containing POST values.
 * @return string The output response.
 * @throws Exception If the request fails.
 */
private function post($url, $postVars = array()) {
    //Transform our POST array into a URL-encoded query string.
    $postStr = http_build_query($postVars);
    //Create an $options array that can be passed into stream_context_create.
    $options = array(
        'http' =>
            array(
                'method'  => 'POST', //We are using the POST HTTP method.
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postStr //Our URL-encoded query string.
            )
    );
    //Pass our $options array into stream_context_create.
    //This will return a stream context resource.
    $streamContext  = stream_context_create($options);
    //Use PHP's file_get_contents function to carry out the request.
    //We pass the $streamContext variable in as a third parameter.
    $result = file_get_contents($url, false, $streamContext);
    //If $result is FALSE, then the request has failed.
    if($result === false){
        //If the request failed, throw an Exception containing
        //the error.
        $error = error_get_last();
        throw new Exception('POST request failed: ' . $error['message']);
    }
    //If everything went OK, return the response.
    return $result;
  }
}
?>
