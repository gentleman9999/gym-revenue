<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Twilio\Rest\Client;


class TwilioSMSController extends Controller
{

      public function index()
      {
           $receiverNumber = "9543910398";
          //shivam
          // $receiverNumber = "7275049781";
          //blair
        //  $receiverNumber = "4239942372";

$message = "This is testing from localhost";

try {

$account_sid = "AC6bad234db52cb4f7a8c466c92a8e8a50";
$auth_token = "1531e87775390625d404a50bc0c15052";
$twilio_number = "+19562753856";

//dd(\);


  //  $account_sid = getenv("TWILIO_SID");
  //  $auth_token = getenv("TWILIO_TOKEN");
  //  $twilio_number = getenv("TWILIO_NO");



$client = new Client($account_sid, $auth_token);
$client->messages->create($receiverNumber, [
'from' => $twilio_number,
'body' => $message]);



dd('SMS Sent Successfully.');

} catch (Exception $e) {
dd("Error: ". $e->getMessage());
}
}

}
