<?php
/*
This is a simple demo API showing how to interact with the
transact-io.php library

You are free to use this example in any way you wish.
If you feel this exmaple can be improved please send
a git pull request
*/

require_once __DIR__ . '/../transact-io.php'; // Include lib

// simple response object for errors
class ErrorResponse {
  public $code = 400;
  public $messsage = '';
  public $data = null;
  function __construct($code, $msg, $data=null) {
      $this->code = $code;
      $this->message = $msg;
      $this->data = $data;
   }
}

// all of our responses are JSOn
header('Content-Type: text/javascript; charset=utf8');

$transact = new TransactIoMsg();

// Required: set the secret use to sign messages
// The secret needs to be set for outboud message tokens
// and it also needs to be set for inbound validation
$transact->setSecret('Signing Secret');

// Optional: default signing algorithim
$transact->setAlg('HS256');

function InitSaleParameters($transact) {

  // Required: set ID of who gets paid
  $transact->setRecipient('5206507264147456');

  // Required:  Set the price of the sale
  $transact->setPrice(2);

  // Required:  Set PROD to use real money,  TEST for testing
  $transact->setClass('PROD');

  // Required:  set URL associated with this puchase
  // User should be able to return to this URL
  if (!empty($_REQUEST['url'])) {
    $transact->setURL($_REQUEST['url']);
  } else if (!empty($_SERVER['SCRIPT_URI'])) {
    $transact->setURL($_SERVER['SCRIPT_URI']);
  } else {
    $transact->setURL('https://example.site/article1/');
  }

  // Recommended: Title for customer to read for the purchase
  $transact->setTitle('PHP Demo Title');

  $transact->setMethod('CLOSE'); // Optional: by default close the popup


  // Unique code for seller to set to what they want
  //  This could be a code for the item your selling
  $transact->setItem('ItemCode1');

  // Optional Unique ID of this sale
  $transact->setUid('ItemCode1');

  // Set your own meta data
  // Note you must keep this short to avoid going over the 1024 byte limt
  // of the token URL
  $transact->setMeta(array(
    'your' => 'data',
    'anything' => 'you want'
  ));

  return $transact;
}

function fetchPremiumContent($token) {

}

switch($_REQUEST['action']) {

  case 'getToken':

    $transact = InitSaleParameters($transact);

    $response = array(
      'token' => $transact->getToken()
    );
    echo json_encode($response);

    break;
  case 'getSubscriptionToken':
    $transact = InitSaleParameters($transact);

    $response = array(
      'token' => $transact->getSubscriptionToken()
    );
    echo json_encode($response);

    break;
  case 'getDonateToken':
    $transact = InitSaleParameters($transact);

    if (empty($_REQUEST['price'])) {
      json_encode(new ErrorResponse('400', 'Invalid Price'));
      return;
    }
    $transact->setPrice($_REQUEST['price']);
    $transact->setTitle('Donation Title');

    // Unique code for seller to set to what they want
    //  This could be a code for the item your selling
    $transact->setItem('Donate');

    // Optional Unique ID of this sale
    $transact->setUid('Donate_ID');

    $response = array(
      'token' => $transact->getToken()
    );
    echo json_encode($response);

    break;
  case 'getPurchasedContent':

    try {
      $decoded = $transact->decodeToken($_REQUEST['t']);
      echo json_encode(array(
        'content' => 'SUCESSS PAID CONTENT HERE!',
        'status' => 'OK',
        'subscription' =>  $decoded->sub,
        'subscription_expires' =>  $decoded->sub_expires,
        'decoded' => $decoded
        ));
     } catch (Exception $e) {

       echo json_encode(array(
        'content' => 'Failed validation',
        'status' => 'ERROR',
        'message' =>  $e->getMessage(),
        ));
     }

  break;
  default:
    header("HTTP/1.0 404 Not Found");

    echo json_encode(new ErrorResponse('404', 'Invalid API call'));
    exit;
};




?>
