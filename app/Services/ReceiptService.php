<?php

namespace App\Services;

use App\Models\Order;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReceiptService
{
    /**
     * Build data required for printing a receipt
     */
    public function buildReceiptData(Order $order): array
    {
        $order->loadMissing(['items.product', 'payments.method', 'user']);

        // Generate a simple QR code (e.g., for ZATCA or just general tracking)
        // Format: Company Name, Tax Number, Date, Total, VAT (simplified here)
        $qrData = "Cafe Pro\nOrder: {$order->id}\nTotal: {$order->total_amount}";
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($qrData));

        return [
            'order' => $order,
            'company_name' => config('app.name', 'Cafe Pro'),
            'company_address' => '123 Coffee St, City',
            'tax_number' => '1234567890',
            'qr_code' => $qrCode,
            'date' => $order->created_at->format('Y-m-d H:i:s'),
            'cashier' => $order->user->name ?? 'System',
        ];
    }
}
