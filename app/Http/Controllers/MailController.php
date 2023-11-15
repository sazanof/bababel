<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class MailController extends Controller
{
    public function getHeader()
    {
        return Image::make(base_path(env('MAIL_HEADER_IMG')))->response('jpg');
    }

    public function getLogo()
    {
        return Image::make(base_path(env('MAIL_LOGO')))->response('png');
    }
}
