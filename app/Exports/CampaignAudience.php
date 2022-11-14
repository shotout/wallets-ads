<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;

class CampaignAudience implements FromView,ShouldAutoSize,WithTitle,WithColumnWidths
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

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 30,   
            'C' => 30,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,         
        ];
    }

    public function view(): View
    {
        return view('exports.audience', [
            'data' => $this->data
        ]);
    }
}
