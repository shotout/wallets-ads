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
                                    <div style="Margin-left:20px;Margin-right:20px">
                                    <div>
                                        {{-- @if ($flag === 'register')
                                            <h1 style="Margin-top:0;Margin-bottom:20px;font-style:normal;font-weight:normal;color:#111324;font-size:16px;line-height:31px;text-align:left">Thank you for creating an account on the NFT Daily App!</h1>
                                        @endif
                                        @if ($flag === 'unregister')
                                            <h1 style="Margin-top:0;Margin-bottom:20px;font-style:normal;font-weight:normal;color:#111324;font-size:16px;line-height:31px;text-align:left">We are sad to see you go!</h1>
                                        @endif --}}
                                    </div>
                                    </div>
                                    <div style="Margin-left:20px;Margin-right:20px">
                                    <div>
                                        @if ($flag === 'register')
                                            <p style="Margin-top:0;Margin-bottom:30px">
                                                Thank you for creating an account on the <span style="font-weight: bold;">WALLETADS!</span> Click the button below to <span style="font-weight: bold;">activate your account and to login.</span>
                                            </p>
                                        @endif
                                        @if ($flag === 'login')
                                            <p style="Margin-top:0;Margin-bottom:20px">
                                                Click the button below to <span style="font-weight: bold;">sign in to your account</span> on NFT Daily and make sure that you <span style="font-weight: bold;">won't miss out on the newest drops!</span>  
                                            </p>
                                        @endif
                                        @if ($flag === 'unregister')
                                            <p style="Margin-top:0;Margin-bottom:20px">
                                                To delete your account on NFT Daily, please click the link below. But be aware – <span style="font-weight: bold;">you will not be informed about the newest drops, projects and exciting collections anymore!</span>
                                            </p>
                                        @endif
                                    </div>
                                    </div>
                                    <div style="Margin-left:20px;Margin-right:20px">
                                        @if ($flag === 'register')
                                            <div style="Margin-bottom:20px;text-align:center">
                                                <a href="{{env('BE_URL')}}/auth/verify/{{$user->remember_token}}" style="border-radius:4px;display:inline-block;font-size:14px;font-weight:bold;line-height:24px;padding:12px 54px;text-align:center;text-decoration:none!important;color:#ffffff!important;background-color:#7856ff;font-family:sans-serif;text-transform: uppercase;"
                                                    target="_blank">Activate Account</a>
                                            </div>
                                        @endif
                                        @if ($flag === 'login')
                                            <div style="Margin-bottom:20px;text-align:center">
                                                <a href="{{env('BE_URL')}}/auth/verify/{{$user->remember_token}}" style="border-radius:4px;display:inline-block;font-size:14px;font-weight:bold;line-height:24px;padding:12px 84px;text-align:center;text-decoration:none!important;color:#ffffff!important;background-color:#7856ff;font-family:sans-serif;text-transform: uppercase;"
                                                    target="_blank">Sign In</a>
                                            </div>
                                        @endif
                                        @if ($flag === 'unregister')
                                            <div style="Margin-top:30px;Margin-bottom:20px;text-align:center">
                                                <a href="{{env('BE_URL')}}/users/unregister/{{$user->remember_token}}" style="border-radius:4px;display:inline-block;font-size:14px;font-weight:bold;line-height:24px;padding:12px 54px;text-align:center;text-decoration:none!important;color:#ffffff!important;background-color:#E04F46;font-family:sans-serif;text-transform: uppercase;"
                                                    target="_blank">Delete Account</a>
                                            </div>
                                        @endif
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
        