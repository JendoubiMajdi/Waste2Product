<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        .header {
            border-bottom: 3px solid #06b6d4;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .company-info h1 {
            color: #06b6d4;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .company-info p {
            margin: 5px 0;
            font-size: 12px;
        }
        .invoice-info {
            float: right;
            width: 45%;
            text-align: right;
        }
        .invoice-info h2 {
            color: #0e7490;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .invoice-info p {
            margin: 5px 0;
            font-size: 12px;
        }
        .clear {
            clear: both;
        }
        .section {
            margin: 30px 0;
        }
        .section-title {
            color: #0e7490;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e0f2fe;
        }
        .client-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
        }
        .client-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table thead {
            background: #06b6d4;
            color: white;
        }
        table thead th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 13px;
        }
        table tbody tr {
            border-bottom: 1px solid #e0f2fe;
        }
        table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        table tbody td {
            padding: 10px 12px;
            font-size: 12px;
        }
        table tbody td.text-right {
            text-align: right;
        }
        table tfoot {
            background: #f1f5f9;
            font-weight: bold;
        }
        table tfoot td {
            padding: 12px;
            font-size: 14px;
        }
        .total-row {
            background: #0e7490 !important;
            color: white !important;
        }
        .total-row td {
            font-size: 16px !important;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e0f2fe;
            text-align: center;
            font-size: 11px;
            color: #64748b;
        }
        .footer p {
            margin: 5px 0;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background: #22c55e;
            color: white;
        }
        .badge-warning {
            background: #facc15;
            color: #333;
        }
        .badge-info {
            background: #38bdf8;
            color: white;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>Waste2Product</h1>
                <p><strong>Address:</strong> 123 Rue de Tunis, Tunis, Tunisia</p>
                <p><strong>Phone:</strong> +216 XX XXX XXX</p>
                <p><strong>Email:</strong> contact@waste2product.tn</p>
                <p><strong>Tax ID:</strong> TN123456789</p>
            </div>
            <div class="invoice-info">
                <h2>FACTURE</h2>
                <p><strong>Invoice #:</strong> {{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Date:</strong> {{ $order->date->format('d/m/Y') }}</p>
                <p><strong>Status:</strong> <span class="badge badge-info">{{ ucfirst($order->statut) }}</span></p>
            </div>
            <div class="clear"></div>
        </div>

        <!-- Client Information -->
        <div class="section">
            <div class="section-title">Client Information</div>
            <div class="client-info">
                <p><strong>Name:</strong> {{ $order->client->name ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $order->client->email ?? 'N/A' }}</p>
                @if($order->client && $order->client->phone)
                <p><strong>Phone:</strong> {{ $order->client->phone }}</p>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="section">
            <div class="section-title">Order Details</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Product</th>
                        <th style="width: 15%; text-align: center;">Quantity</th>
                        <th style="width: 17%; text-align: right;">Unit Price</th>
                        <th style="width: 18%; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->products as $product)
                    <tr>
                        <td>
                            <strong>{{ $product->nom }}</strong>
                            @if($product->description)
                            <br><small style="color: #64748b;">{{ Str::limit($product->description, 60) }}</small>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $product->pivot->quantite }}</td>
                        <td class="text-right">{{ number_format($product->pivot->unit_price, 3) }} DT</td>
                        <td class="text-right">{{ number_format($product->pivot->subtotal, 3) }} DT</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">TOTAL:</td>
                        <td style="text-align: right;">{{ number_format($order->total_amount, 3) }} DT</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payment Information -->
        <div class="section">
            <div class="section-title">Payment Information</div>
            <div class="client-info">
                <p><strong>Payment Method:</strong> To be determined</p>
                <p><strong>Payment Status:</strong> <span class="badge badge-warning">Pending</span></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
            <p>For any questions, please contact us at contact@waste2product.tn</p>
            <p style="margin-top: 10px;">© {{ date('Y') }} Waste2Product - All rights reserved</p>
        </div>
    </div>
</body>
</html>
