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
                                                        <p style="Margin-top:0;Margin-bottom:20px">Hello,</p>
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
                                                            New Campaign has been created on <span style="font-weight: bold;"> WALLETADS.</span>
                                                        </h1>
                                                    </div>
                                                </div>
                                                <div style="Margin-left:5%;Margin-right:5%; background:rgba(203, 180, 223, 0.25);">
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td style="font-weight: 700; margin-left: 30px;">Payment: <a style="font-weight: normal;">{{$campaign->payment_method}}</a></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 700; margin-left: 30px;">Amount: <a style="font-weight: normal;">{{$campaign->amount}}</a></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 700; margin-left: 30px;">Campaign Name: <a style="font-weight: normal;">{{$campaign->name}}</a></td>
                                                            </tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Start Date: <a style="font-weight: normal;">{{$campaign->date}}</a></td>
                                                            </tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Availability: <a style="font-weight: normal;">{{$campaign->availability}} Days </a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Targeting: <a style="font-weight: normal;"></a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Cryptocurrencies used: <a style="font-weight: normal;"></a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Account age: <a style="font-weight: normal;"></a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Available credit in wallet: <a style="font-weight: normal;"></a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Trading Volume: <a style="font-weight: normal;"></a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Airdrops Received: <a style="font-weight: normal;"></a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Amount of transactions: <a style="font-weight: normal;"></a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>NFT Purchases: <br /><br /> <a style="font-weight: normal;"></a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Collection page name: <a style="font-weight: normal;">{{$adspage->name}}</a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Collection page Logo: <a style="font-weight: normal;">Collection Logo of {{$campaign->name}}</a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Collection page Banner: <a style="font-weight: normal;">Collection Banner of {{$campaign->name}}</a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Collection page Text: <a style="font-weight: normal;">{{$adspage->description}}</a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Website <a style="font-weight: normal;">{{$adspage->website}}</a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Discord: <a style="font-weight: normal;">{{$adspage->discord}}</a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Medium: <a style="font-weight: normal;">{{$adspage->medium}}</a></td>
                                                            <tr>
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Telegram: <a style="font-weight: normal;">{{$adspage->telegram}} <br /><br /></a></td>
                                                            <tr>
                                                                @foreach($ads as $ad)
                                                            <tr style="font-weight: 700; margin-left: 30px;">
                                                                <td>Ad Name: <a style="font-weight: normal;">{{$ad->name}}</a></td>
                                                            <tr>
                                                                @endforeach

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <br>
                                                <br />
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