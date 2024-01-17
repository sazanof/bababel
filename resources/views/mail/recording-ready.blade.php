@extends('mail.main')
@section('custom')
    <div
        style="background: #f9f9f9f9;
        border-top:1px solid #f7f7f7f7;
        border-bottom:1px solid #f7f7f7f7;
        padding:15px 40px;
        margin: 0">

        <p
            style="font-family: Arial, Helvetica, Roboto, Arial, sans-serif;
        margin: 15px 0;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        color:#e57d21;
    ">

            <a style="font-family: Arial, Helvetica, Roboto, Arial, sans-serif;
            margin: 10px auto;
            display:inline-block;padding: 6px 10px;color: #ffffff;background: #e57d21;text-decoration: none"
               href="{{$recording->url}}" target="_blank">{{__('mail.recording.ready_link')}}</a>
        </p>
    </div>
@endsection
