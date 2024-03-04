<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function leaveRequest(Request $request)
    {
        $subject = $request->get('subject');
        $message = $request->get('message');
        $files = $request->file('files');
        $user = $request->user();
        if ($user instanceof User) {
            Mail::send(new FeedbackMail(
                user: $user,
                title: $subject,
                message: $message,
                files: $files
            ));
            // dd($subject, $message, $files);
        }

    }
}
