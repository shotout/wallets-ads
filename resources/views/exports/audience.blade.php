@isset($data)
    <h4 style="text-align: center;">{{$data->campaign->name}}</h4>
    <p></p>
    <table>
        <thead>
            <tr>
                <th style="font-weight: bold; text-align: left;">Audience</th>
                <th style="font-weight: bold; text-align: left;">Ads Cretaive</th>
                <th style="font-weight: bold; text-align: left;">Airdrop</th>
                <th style="font-weight: bold; text-align: left;">Click</th>
                <th style="font-weight: bold; text-align: left;">Mint</th>
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
                </tr>
            @endforeach

            <tr>
                <td style="font-weight: bold; text-align: left;" colspan="2">Total</td>
                <td style="font-weight: bold; text-align: left;">{{$data->counter->airdrop}}</td>
                <td style="font-weight: bold; text-align: left;">{{$data->counter->click}}</td>
                <td style="font-weight: bold; text-align: left;">{{$data->counter->mint}}</td>
            </tr>
        </tbody>
    </table>
@endisset
