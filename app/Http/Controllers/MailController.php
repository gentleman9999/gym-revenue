<?php

namespace App\Http\Controllers;
use App\Models\Comms\EmailTemplates;
use App\Models\Endusers\Lead;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
//use App\Http\Controllers\Controller;
//use App\Mail\Email;
//use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailController extends Controller {

    use  SerializesModels;

    protected $template, $user, $message;
    public function __construct()
    {


    }



    public function html_email(Request $request) {


        $getuser=User::find($request->user()->id);
        $sendto = $getuser->email;

        $email_id =$request->data['id'];
        $template =EmailTemplates::find($email_id);
 //    dd($email_id,$template,$request->user()->email);
        $sendtestto =$request->user()->email;
        $html = $template->markup;
        $subject =$template->subject;
        $sendername=$request->user()->name;
  //      dd($sendto,$email_id,$html,$subject,$sendtestto,$sendername,$request->user());


 //       return $this->from($this->user->email)
 //           ->subject($this->data['subject'])
 //           ->markdown('emails.endusers.email-from-rep', ['data' => $this->data, 'user'=> $this->user->toArray()]);



        Mail::html($html,  function ($message) {
           $message
        //  ->cc('shivam@capeandbay.com')
               ->to('steve@capeandbay.com')
        //         ->subject('close .. dam it ');
          ->subject('close .. dam it ');
                });
  /*
        Mail::send([
            'html' => $html,
            'text' => 'Plain text here',
            'raw'  => 'raw text'
        ], [
            'user' => ''
        ], function ($message) {
            $message
            ->to('steve@capeandbay.com') //sendto')
            ->subject('{{$subject}}');
        });

*/

        echo "HTML Email Sent. Check your inbox.";
    }


    public function basic_email() {

    }
}
