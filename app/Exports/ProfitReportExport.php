<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProfitReportExport implements FromArray, WithHeadings
{
    protected $days;

    public function __construct(array $days)
    {
        $this->days = $days;
    }

    public function array(): array
    {
        return array_map(function ($day) {
            return [
                $day['date'],
                $day['orders_count'],
                $day['sales'],
                $day['cogs'],
                $day['expenses'],
                $day['wastage'],
                $day['net_profit'],
            ];
        }, $this->days);
    }

    public function headings(): array
    {
        return [
            'التاريخ',
            'الطلبات',
            'المبيعات',
            'COGS',
            'المصروفات',
            'الهالك',
            'صافي الربح',
        ];
    }
}
