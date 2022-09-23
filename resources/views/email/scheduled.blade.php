<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "https://www.w3.org/TR/html4/strict.dtd">
<html lang="en">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <title>{{$user->email_message}}</title>
</head>

<body style="font-family: inter">
    <div style="overflow: hidden;">
        <div style="margin:0;padding:0">
            <table cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;table-layout:fixed;min-width:320px;width:100%;background-color:#f2f4f6">
                <tbody>
                    <tr>
                        <td>
                            <div role="section">
                                <div style="background-color:#ffffff">
                                    <div style="Margin:0 auto;max-width:600px;min-width:320px;width:320px;width:calc(28000% - 167400px);word-wrap:break-word;word-break:break-word">
                                        <div style="width: 200px;margin: 0 auto;">
                                            <a href="{{env('APP_URL')}}" target="_blank">
                                                <img src="{{env('APP_URL')}}/assets/images/logo.png" style="margin-top: 50px;margin-bottom: 20%;width: 100%;text-align: center;">
                                            </a>
                                        </div>
                                        <div style="border-collapse:collapse;display:table;width:100%">
                                            <div style="max-width:600px;min-width:320px;width:320px;width:calc(28000% - 167400px);text-align:left;color:#111324;font-size:16px;line-height:24px;font-family:sans-serif">
                                                <div style="Margin-left:20px;Margin-right:20px">
                                                    <div style="line-height:20px;font-size:1px">
                                                        &nbsp;
                                                    </div>
                                                </div>
                                                <div style="Margin-left:20px;Margin-right:20px">
                                                    <div>
                                                        <p style="Margin-top:0;Margin-bottom:20px">Hi {{$user->name}},</p>
                                                    </div>
                                                </div>
                                                <div style="Margin-left:20px;Margin-right:20px">
                                                    <div style="line-height:1px;font-size:1px">
                                                        &nbsp;
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="background-color:#ffffff">
                                    <div style="Margin:0 auto;max-width:600px;min-width:320px;width:320px;width:calc(28000% - 167400px);word-wrap:break-word;word-break:break-word">
                                        <div style="border-collapse:collapse;display:table;width:100%">
                                            <div style="max-width:600px;min-width:320px;width:320px;width:calc(28000% - 167400px);text-align:left;color:#111324;font-size:16px;line-height:24px;font-family:sans-serif">
                                                <div style="Margin-left:20px;Margin-right:20px">
                                                    <div style="line-height:2px;font-size:1px">
                                                        &nbsp;
                                                    </div>
                                                </div>
                                                <div style="Margin-left:20px;Margin-right:20px">
                                                    <div>
                                                        <h1 style="Margin-top:0;Margin-bottom:20px;font-style:normal;font-weight:normal;color:#111324;font-size:16px;line-height:31px;text-align:justify">
                                                                We would like to inform you that your <span style="font-weight: bold;"> WALLETADS </span> campaign <span style="font-weight: bold;"> {{$campaign->name}} </span> has successfully been scheduled.
                                                        </h1>
                                                    </div>
                                                </div>
                                                <div style="Margin-left:20px;Margin-right:20px; background:rgba(203, 180, 223, 0.25);">
                                                    <table style = "padding-left: 20px;">
                                                        <tbody>
                                                            <tr>
                                                                <td style="font-weight: 800; margin-left: 30px;">{{$campaign->name}}</td>
                                                            </tr>
                                                            
                                                            <tr>
                                                        </tbody>
                                                    </table style = "padding-left: 20px;">
                                                    <hr style="margin-left: 10px;margin-right: 10px;">
                                                    <table style = "padding-left: 20px;">
                                                        <tr>
                                                            <td style="font-weight: 600; margin-left: 15px;">Sendouts:</td> <td style="font-weight: 600; padding-left: 120px;">Total Budget:</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="margin-left: 10px; font-weight: 500;">{{$sendout}} sendouts</td> <td style="font-weight: 500; padding-left: 120px;">${{$budget}}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <br>
                                                <div style="Margin-left:20px;Margin-right:20px">
                                                    <div>
                                                        <p style="Margin-top:0;Margin-bottom:20px">You can click the button below to setup another campaign.</p>
                                                    </div>
                                                </div>
                                                <br>
                                                <div style="Margin-left:20px;Margin-right:20px">
                                                    <div style="Margin-bottom:20px;text-align:center">
                                                        <a href="{{env('FE_URL')}}/create-campaign" style="border-radius:4px;display:inline-block;font-size:14px;font-weight:bold;line-height:24px;padding:12px 75px;text-align:center;text-decoration:none!important;color:#ffffff!important;background-color:#7089FF;font-family:sans-serif;text-transform: uppercase;" target="_blank">CREATE ANOTHER CAMPAIGN</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>