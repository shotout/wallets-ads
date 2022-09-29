<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "https://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <title>{{$user->email_message}}</title> 
    </head>
    <body>

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
                                    <a href ="{{env('APP_URL')}}" target="_blank">
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
                                    {{-- <div style="Margin-left:20px;Margin-right:20px">
                                    <div>
                                        <h1 style="Margin-top:0;Margin-bottom:20px;font-style:normal;font-weight:normal;color:#111324;font-size:16px;line-height:31px;text-align:left">Thank you for request reset password on the NFT Daily App!</h1>
                                    </div>
                                    </div> --}}
                                    <div style="Margin-left:20px;Margin-right:20px">
                                    <div>
                                        <p style="Margin-top:0;Margin-bottom:20px">
                                            We have received a request to reset your password for <span style="font-weight: bold;">WALLETADS</span>. You can click the button below to reset your password.
                                        </p>
                                    </div>
                                    </div>
                                    <div style="Margin-left:20px;Margin-right:20px">
                                        <div style="Margin-bottom:20px;text-align:center">
                                            <a href="{{env('FE_URL')}}/change-password?verify={{$user->remember_token}}" style="border-radius:4px;display:inline-block;font-size:14px;font-weight:bold;line-height:24px;padding:12px 54px;text-align:center;text-decoration:none!important;color:#ffffff!important;background-color:#7856ff;font-family:sans-serif;text-transform: uppercase;"
                                                target="_blank">Reset Password</a>
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
        