<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Comm\MassCommunicationsController;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplates;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Mail;
use Prologue\Alerts\Facades\Alert;
use Twilio\Rest\Client;
use App\Models\User;


class TwilioSMSController extends Controller
{

      public function index(Request $request)
      {
          $txt_id =$request->data['id'];
          $template = SmsTemplates::find($txt_id);
          $template_message =$template->markup;
          if (isset($request->user()->phone->value)) {
              $receiverNumber = $request->user()->phone->value;
             }else{
              Alert::error('your phone is not on file')->flash();
              return redirect()->back();
                   }
          $message = $template_message;
//$message = "This is testing from localhost";
          try {
             $account_sid = "AC6bad234db52cb4f7a8c466c92a8e8a50";
             $auth_token = "1531e87775390625d404a50bc0c15052";
             $twilio_number = "+19562753856";

         //  $account_sid = getenv("TWILIO_SID");
         //  $auth_token = getenv("TWILIO_TOKEN");
         //  $twilio_number = getenv("TWILIO_NO");

        $client = new Client($account_sid, $auth_token);
        $client->messages->create($receiverNumber, [
       'from' => $twilio_number,
       'body' => $message]);
    Alert::success('Your Text was sent to your phone on file')->flash();
    return redirect()->back();
    } catch (Exception $e) {
              dd("Error: ". $e->getMessage());
              }
            }
          }
