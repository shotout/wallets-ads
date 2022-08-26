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
                        {{-- <div role="banner">
                            <div style="Margin:0 auto;max-width:560px;min-width:280px;width:280px;width:calc(28000% - 167440px)">
                            <div style="border-collapse:collapse;display:table;width:100%">
                                <div style="display:table-cell;Float:left;font-size:12px;line-height:19px;max-width:280px;min-width:140px;width:140px;width:calc(14000% - 78120px);padding:10px 0 5px 0;color:#717a8a;font-family:sans-serif">

                                @if ($flag === 'register')
                                    <p style="Margin-top:0;Margin-bottom:0">
                                        NFT Daily Account Activation
                                    </p>
                                @else
                                    <p style="Margin-top:0;Margin-bottom:0">
                                        NFT Daily Sign In
                                    </p>
                                @endif

                                </div>
                                <div style="display:table-cell;Float:left;font-size:12px;line-height:19px;max-width:280px;min-width:139px;width:139px;width:calc(14100% - 78680px);padding:10px 0 5px 0;text-align:right;color:#717a8a;font-family:sans-serif">
                                </div>
                            </div>
                            </div>
                        </div> --}}
                        <div role="section">
                            <div style="background-color:#ffffff">
                                <div style="Margin:0 auto;max-width:600px;min-width:320px;width:320px;width:calc(28000% - 167400px);word-wrap:break-word;word-break:break-word">
                                    <div style="width: 100px;margin: 0 auto;">
                                    <a href ="https://nftdaily.app/" target="_blank"><img src="https://backend.nftdaily.app/assets/logo/logo.png" style="margin-top: 50px;margin-bottom: 30%;width: 100%;text-align: center;"></a>
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
                                        @if ($flag === 'register')
                                            <h1 style="Margin-top:0;Margin-bottom:20px;font-style:normal;font-weight:normal;color:#111324;font-size:16px;line-height:31px;text-align:left">Thank you for creating an account on the NFT Daily App!</h1>
                                        @endif
                                        @if ($flag === 'unregister')
                                            <h1 style="Margin-top:0;Margin-bottom:20px;font-style:normal;font-weight:normal;color:#111324;font-size:16px;line-height:31px;text-align:left">We are sad to see you go!</h1>
                                        @endif
                                    </div>
                                    </div>
                                    <div style="Margin-left:20px;Margin-right:20px">
                                    <div>
                                        @if ($flag === 'register')
                                            <p style="Margin-top:0;Margin-bottom:20px">
                                                Click the button below to <span style="font-weight: bold;">activate your account</span> on NFT Daily and make sure that you <span style="font-weight: bold;">won't miss out on the newest drops!</span>  
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
                            {{-- <div role="contentinfo">
                                <div style="Margin:0 auto;max-width:600px;min-width:480px;width:480px;width:calc(28000% - 167400px);word-wrap:break-word;word-break:break-word">
                                    <div style="border-collapse:collapse;display:table;width:100%">
                                        <div style="text-align:left;font-size:12px;line-height:19px;color:#717a8a;font-family:sans-serif;Float:left;max-width:560px;min-width:560px;width:560px;width:calc(8000% - 47600px)">
                                            <div style="Margin-left:20px;Margin-right:20px;Margin-top:10px;Margin-bottom:10px">
                                                <div style="font-size:12px;line-height:19px">
                                                    <div>
                                                        <strong>NFT Daily App</strong>
                                                    </div>
                                                </div>
                                                <div style="font-size:12px;line-height:19px;Margin-top:18px"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </div>
    </body>
</html>
        