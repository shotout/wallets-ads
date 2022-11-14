@isset($data)
    <h4 style="text-align: center;">{{$data->campaign->name}}</h4>
    <p></p>
    <table>
        <thead>
            <tr>
                <th style="font-weight: bold; text-align: left;">Audience</th>
                <th style="font-weight: bold; text-align: left;">Ad creative</th>
                <th style="font-weight: bold; text-align: left;">Airdrops</th>
                <th style="font-weight: bold; text-align: left;">Impressions</th>
                <th style="font-weight: bold; text-align: left;">Views</th>
                <th style="font-weight: bold; text-align: left;">Link clicks</th>
                <th style="font-weight: bold; text-align: left;">Mints</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->audiences as $audience)
                <tr>
                    <td style="text-align: left;">{{$audience->name ?? '0'}}</td>
                    <td style="text-align: left;">{{$audience->ads->name ?? '0'}}</td>
                    <td style="text-align: left;">{{$audience->count_airdrop ?? '0'}}</td>
                    <td style="text-align: left;">{{$audience->count_impression ?? '0'}}</td>
                    <td style="text-align: left;">{{$audience->count_view ?? '0'}}</td>
                    <td style="text-align: left;">{{$audience->count_click ?? '0'}}</td>
                    <td style="text-align: left;">{{$audience->count_mint ?? '0'}}</td>
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
