@isset($data)
    <h4 style="text-align: center;">{{$data->campaign->name}}</h4>
    <p></p>
    <table>
        <thead>
            <tr>
                <th style="font-weight: bold; text-align: left;">Audience</th>
                <th style="font-weight: bold; text-align: left;">Ads Name</th>
                <th style="font-weight: bold; text-align: left;">Airdrops</th>
                <th style="font-weight: bold; text-align: left;">Clicks</th>
                <th style="font-weight: bold; text-align: left;">Mints</th>
                <th style="font-weight: bold; text-align: left;">Impressions</th>
                <th style="font-weight: bold; text-align: left;">Mints</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->audiences as $audience)
                <tr>
                    <td style="text-align: left;">{{$audience->name}}</td>
                    <td style="text-align: left;">{{$audience->ads->name}}</td>
                    <td style="text-align: left;">{{$audience->ads->count_airdrop}}</td>
                    <td style="text-align: left;">{{$audience->ads->count_click}}</td>
                    <td style="text-align: left;">{{$audience->ads->count_mint}}</td>
                    <td style="text-align: left;">{{$audience->ads->count_impression}}</td>
                    <td style="text-align: left;">{{$audience->ads->count_view}}</td>
                </tr>
            @endforeach

            <tr>
                <td style="font-weight: bold; text-align: left;" colspan="2">Total</td>
                <td style="font-weight: bold; text-align: left;">{{$data->counter->airdrop}}</td>
                <td style="font-weight: bold; text-align: left;">{{$data->counter->click}}</td>
                <td style="font-weight: bold; text-align: left;">{{$data->counter->mint}}</td>
                <td style="font-weight: bold; text-align: left;">{{$data->counter->impression}}</td>
                <td style="font-weight: bold; text-align: left;">{{$data->counter->view}}</td>
            </tr>
        </tbody>
    </table>
@endisset
