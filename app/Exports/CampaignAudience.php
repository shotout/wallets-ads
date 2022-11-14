<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class CampaignAudience implements FromView,ShouldAutoSize,WithTitle
{
    use Exportable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        $file_name = $this->data->campaign->name;
        return $file_name;
    }

    public function view(): View
    {
        return view('exports.audience', [
            'data' => $this->data
        ]);
    }
}
