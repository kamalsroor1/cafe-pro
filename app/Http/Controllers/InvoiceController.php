<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ReceiptService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(public ReceiptService $receiptService)
    {
    }

    public function download(Order $order)
    {
        $data = $this->receiptService->buildReceiptData($order);
        
        $pdf = Pdf::loadView('receipts.thermal', $data)
            ->setPaper([0, 0, 226.77, 800], 'portrait'); // 80mm wide thermal paper approx
            
        return $pdf->download("receipt-{$order->id}.pdf");
    }
}
