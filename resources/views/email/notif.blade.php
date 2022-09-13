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
                                    <div style="Margin-left:20px;Margin-right:20px">
                                    <div style="line-height:2px;font-size:1px">
                                        &nbsp;
                                    </div>
                                    </div>
                                    <div style="Margin-left:20px;Margin-right:20px">
                                        <div>
                                            <h1 style="Margin-top:0;Margin-bottom:20px;font-style:normal;font-weight:normal;color:#111324;font-size:16px;line-height:31px;text-align:left">
                                                This user created an account on WALLETADS :
                                            </h1>
                                        </div>
                                    </div>
                                    <div style="Margin-left:20px;Margin-right:20px">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>Company Name</td>
                                                    <td>:</td>
                                                    <td>{{$user->company_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tax ID</td>
                                                    <td>:</td>
                                                    <td>{{$user->tax_id}}</td>
                                                </tr>
                                                <tr>
                                                    <td>First Name</td>
                                                    <td>:</td>
                                                    <td>{{$user->first_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Last Name</td>
                                                    <td>:</td>
                                                    <td>{{$user->last_name}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Street Address</td>
                                                    <td>:</td>
                                                    <td>{{$user->street}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Postcode</td>
                                                    <td>:</td>
                                                    <td>{{$user->post_code}}</td>
                                                </tr>
                                                <tr>
                                                    <td>City</td>
                                                    <td>:</td>
                                                    <td>{{$user->city}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td>:</td>
                                                    <td>{{$user->email}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Telephone</td>
                                                    <td>:</td>
                                                    <td>{{$user->phone}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div style="Margin-left:20px;Margin-right:20px">
                                        <p></p>
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
        