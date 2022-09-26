<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "https://www.w3.org/TR/html4/strict.dtd">
<html lang="en">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                                            <a href="{{env('FE_URL')}}" target="_blank">
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
                                                <div style="Margin-left:5%;Margin-right:5%">
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
                                                <div style="Margin-left:5%;Margin-right:5%">
                                                    <div style="line-height:2px;font-size:1px">
                                                        &nbsp;
                                                    </div>
                                                </div>
                                                <div style="Margin-left:5%;Margin-right:5%">
                                                    <div>
                                                        <h1 style="Margin-top:0;Margin-bottom:20px;font-style:normal;font-weight:normal;color:#111324;font-size:16px;line-height:31px;text-align:justify; display:inline-block;">
                                                            Thank you for creating a campaign on <span style="font-weight: bold;"> WALLETADS.</span> You can find your invoice attached to this email and in the <span style="font-weight: bold;"> WALLETADS </span> invoice dashboard.
                                                        </h1>
                                                    </div>
                                                </div>
                                                <div style="Margin-left:5%;Margin-right:5%; background:rgba(203, 180, 223, 0.25);">
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td style="font-weight: 700; margin-left: 30px;">Invoice: <a style="font-weight: normal;">{{$invoice->invoice_number}}</a></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 700; margin-left: 30px;">Date: <a style="font-weight: normal;">{{$invoice->date}}</a></td>
                                                            </tr>
                                                            <tr>
                                                        </tbody>
                                                    </table>
                                                    <hr style="margin-left: 10px;margin-right: 10px;">
                                                    <table>
                                                        <tr>
                                                            <td style="font-weight: 700; margin-left: 15px;">Billed to:</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="margin-left: 10px;">{{$user->company}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-weight: 700; margin-left: 5px; font-size: 16px; ">Payment method:</td>
                                                            <td style="font-weight: 700; padding-left: 7vw; width:auto; ">Amount billed:</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="margin-left: 20%;">{{$invoice->payment_method}}</td>
                                                            <td style="margin-left: 10px; padding-left: 7vw;">${{$user->budget}}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <br>
                                                <div>
                                                    <div style="Margin-bottom:30px;text-align:center;">
                                                        <button style="background-color: #7089FF; border: none; color: white; padding: 18px 50px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; font-weight: bold; border-radius: 5px; margin: 2px 2px; cursor: pointer;" >
                                                        <a style="text-decoration: none; font-weight: bold; color: white;" href="{{env('FE_URL')}}/invoices" target="_blank">See Invoice Dashboard<a>                                                                          
                                                        </button>
                                                    </div>
                                                </div>
                                                <br/>
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