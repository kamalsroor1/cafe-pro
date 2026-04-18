<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShiftReportExport implements FromArray, WithHeadings
{
    protected $shifts;
    protected $summaries;

    public function __construct($shifts, $summaries)
    {
        $this->shifts = $shifts;
        $this->summaries = $summaries;
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->shifts as $shift) {
            $summary = $this->summaries[$shift->id] ?? [];
            $rows[] = [
                $shift->id,
                $shift->user->name ?? 'Unknown',
                $shift->started_at ? $shift->started_at->format('Y-m-d H:i') : '',
                $shift->ended_at ? $shift->ended_at->format('Y-m-d H:i') : 'Active',
                $summary['orders_count'] ?? 0,
                $summary['total_sales'] ?? 0,
                $summary['expected_cash'] ?? 0,
                $summary['actual_cash'] ?? 0,
                $summary['difference'] ?? 0,
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            'رقم الشفت',
            'المستخدم',
            'وقت البدء',
            'وقت الانتهاء',
            'الطلبات',
            'المبيعات',
            'النقدية المتوقعة',
            'النقدية الفعلية',
            'الفرق',
        ];
    }
}
