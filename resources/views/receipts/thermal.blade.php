<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $order->id }}</title>
    <style>
        @page { margin: 10px; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 5px; }
        .mt-2 { margin-top: 5px; }
        .mt-4 { margin-top: 10px; }
        .border-bottom { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; }
        .w-full { width: 100%; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 3px 0; }
        .qr-code { text-align: center; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="text-center border-bottom">
        <h2 class="font-bold mb-2">{{ $company_name }}</h2>
        <div>{{ $company_address }}</div>
        <div>الرقم الضريبي: {{ $tax_number }}</div>
    </div>

    <div class="border-bottom text-right">
        <div>رقم الطلب: #{{ $order->id }}</div>
        <div>التاريخ: {{ $date }}</div>
        <div>الكاشير: {{ $cashier }}</div>
    </div>

    <table class="border-bottom">
        <thead>
            <tr>
                <th class="text-right">الصنف</th>
                <th class="text-center">الكمية</th>
                <th class="text-left">السعر</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td class="text-right">{{ $item->product->name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-left">${{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="border-bottom font-bold">
        <tr>
            <td class="text-right">الإجمالي:</td>
            <td class="text-left">${{ number_format($order->total_amount, 2) }}</td>
        </tr>
    </table>

    <div class="qr-code">
        <img src="data:image/svg+xml;base64,{!! $qr_code !!}" alt="QR Code" width="100">
    </div>

    <div class="text-center mt-4 font-bold">
        شكراً لزيارتكم!
    </div>
</body>
</html>
