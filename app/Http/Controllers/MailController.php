<?php

namespace App\Http\Controllers;
use App\Models\Comms\EmailTemplates;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Prologue\Alerts\Facades\Alert;

class MailController extends Controller {
    use  SerializesModels;
    public function html_email(Request $request) {
        $email_id =$request->data['id'];
        $template =EmailTemplates::find($email_id);
        $sendtestto =$request->user()->email;
        $html = $template->markup;
        $subject =$template->subject;

        Mail::html($html,  function ($message) use ($subject, $sendtestto) {
           $message
            ->to($sendtestto)
            ->subject($subject);
                });
        Alert::success('This Email was sent to you')->flash();
        return redirect()->back();
    }
    public function basic_email() {

    }
}
