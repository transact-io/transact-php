<?php

require __DIR__ . '/vendor/autoload.php';
use \Firebase\JWT\JWT;


session_set_cookie_params(86400*365,"/");
session_start();


class TransactIoMsg {

  private $secret = 'Signing Secret'; // signing secret. CHANGE this to YOURS

  private $token = array(

    'iat' => 0, // issued at
    'item' => '',   //code for what they are buying
    'method' => 'CLOSE',  // CLOSE (popup) or POST (to page)
    'price' => 0, // price in cents
    'recipient' => '', // recipient who receives the funds
    'tclass' => 'PROD', //Class or Currency to use.  TEST or PROD,
    'title' => 'Description for Humans to read', // describe
    'uid' => '', // Unique ID to identify
    'url' => '', // URL of what they are buying
);

  private $method='CLOSE';
  private $alg='HS256';  // use the SHA 256 Algorithim
  private $leeway = 600;

  function __construct() {
    if ($_SERVER['SERVER_PORT'] == 443)
      $this->token['url'] = 'https://';
    else
      $this->token['url'] = 'http://';

    if (!empty($_SERVER['HTTP_HOST']))
      $this->token['url'] .= $_SERVER['HTTP_HOST'];
  }

  function setSecret($val) {
    $this->secret = $val;
  }

  function setRecipient($val) {
    // this should be a 64 bit int,
    // treat as string incase we are on a 32bit machine
    $this->token['recipient'] =  (string) $val;
  }
  function setPrice($val) {
    if ($val < 1 || $val > 100000) {
      throw new Exception('Price must be betwen 1 and 100000');
    }

   $this->token['price'] = (int) $val;
  }
  function setClass($val) {
    $this->token['tclass'] = $val;
  }

  // set leeway in seconds
  // https://tools.ietf.org/html/rfc7519#section-4.1.4
  function setLeeway($val) {
    $this->leeway = $val;
  }

  function setMethod($val) {
    switch($val) {
      case 'POST':
      case 'CLOSE':
        $this->methhod = $val;
        break;
      default:
        throw new Exception('method must be POST or CLOSE (popup)');
    }
  }

  function setAlg($val) {
     switch($val) {
      case 'HS256':
      case 'ES256':
        $this->alg = $val;
        break;
      default:
        throw new Exception('HS256 and ES256 supported');
    }
  }
  function setTitle($val) {
    $this->token['title'] = $val;
  }
  function setItem($val) {
    $this->token['item'] = $val;
  }
  function setUid($val) {
    $this->token['uid'] = $val;
  }
  function setMeta($val) {
    $this->token['meta'] = $val;
  }

  function setURL($val) {
    $this->token['url'] = $val;
  }

  function setAffiliate($val) {
    if (is_numeric($val))
      $this->token['aff'] = (int) $val;
  }
  function getToken() {
    $this->token['iat'] = time();  // set timestamp

    if (empty($this->secret))
      throw new Exception('Must set signing secret');

    $token = JWT::encode($this->token, $this->secret);
    return $token;
  }

  function getSubscriptionToken() {

    $this->token['iat'] = time();  // set timestamp

    unset($this->token['price']);
    $this->token['sub'] = TRUE;
    $this->token['title'] = 'Subscription';

    if (empty($this->secret))
      throw new Exception('Must set signing secret');

    $token = JWT::encode($this->token, $this->secret);
    return $token;
  }

  function decodeToken($token) {
    JWT::$leeway = $this->leeway; // $leeway in seconds
    return JWT::decode($token, $this->secret, array($this->alg));
  }

}


?>
