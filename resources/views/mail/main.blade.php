<div
    style="margin:50px auto;
    padding: 40px 0;
    font-size: 16px;
    font-family: Arial, Helvetica, Roboto, Arial, sans-serif;
    width: 700px;
    max-width: 90%;
    color: #333333;
    border:1px solid #f6f6f6f6;
    border-radius:20px;
    background: #ffffff;
    box-shadow: 0 0 40px #f7f7f7f7">
    <img style="margin: 0 auto; width: 250px; height:auto;display: block; border:none" src="{{url('mail/logo')}}"/>
    <p
        style="font-family: Arial, Helvetica, Roboto, Arial, sans-serif;
        text-align: center;
        text-transform: uppercase;
        font-size: 24px;
        color:#e57d21;
        font-weight: bold;
    ">{{env('APP_NAME')}}</p>
    <p
        style="font-family: Arial, Helvetica, Roboto, Arial, sans-serif;
        text-align: center;
        font-size: 16px;
        color:#666;
        font-weight: bold;
    ">{{$msg}}</p>

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
    ">{{\Illuminate\Support\Carbon::make($meeting->date)->format('d.m.Y H:i')}}</p>
            <p
                style="font-family: Arial, Helvetica, Roboto, Arial, sans-serif;
        margin: 5px 0;
        text-align: center;
        font-size: 16px;
        color:#444;
        font-weight: bold;
    ">{{$meeting->name}}</p>
            <p
                style="font-family: Arial, Helvetica, Roboto, Arial, sans-serif;
        margin: 5px 0;
        text-align: center;
        font-size: 14px;
        color:#666;
    ">{{$meeting->welcome}}</p>
            <p
                style="font-family: Arial, Helvetica, Roboto, Arial, sans-serif;
        margin: 20px 0;
        text-align: center;
        font-size: 14px;
        color:#666;
    ">
                <span style="display:inline-block; margin-right: 6px">{{__('mail.meeting.organizer')}}:</span>
                <span style="font-weight: bold;">{{$meeting->owner->firstname}} {{$meeting->owner->lastname}}</span>
            </p>
            <p
                style="font-family: Arial, Helvetica, Roboto, Arial, sans-serif;
        margin: 20px 0;
        text-align: center;
        font-size: 14px;
        color:#666;
    ">
                <a
                    href="{{url('/#/meetings/3841/view')}}"
                    target="_blank"
                    style="font-family: Arial, Helvetica, Roboto, Arial, sans-serif;
            text-align: center;
            font-size: 14px;
            display:block;
            padding: 10px;
            background:#e57d21;
            margin: 0 auto;
            width: 130px;
            border-radius: 30px;
            text-decoration: none;
            color:#fff;
            font-weight: bold;
        ">
                    {{__('mail.go')}}
                </a>
            </p>
        </div>

    @stop
    @yield('custom')

    <p
        style="font-family: Arial, Helvetica, Roboto, Arial, sans-serif;
        margin: 60px 0 30px 0;
        text-align: center;
        font-size: 14px;
        color:#888;
    ">{{env('APP_NAME')}}</p>

    <p
        style="font-family: Arial, Helvetica, Roboto, Arial, sans-serif;
        margin: 30px 0 0px 0;
        text-align: center;
        font-size: 12px;
        color:#ddd;
        font-style: italic;
    ">
        {{__('mail.sent_at')}}: {{\Illuminate\Support\Carbon::now()->format('d.m.Y H:i:s')}}
    </p>


</div>
