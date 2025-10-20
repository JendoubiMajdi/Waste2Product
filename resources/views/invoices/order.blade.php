<!DOCTYPE html>
<html lang="en">
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
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            margin-bottom: 40px;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 20px;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 5px;
        }
        .invoice-info {
            float: right;
            width: 45%;
            text-align: right;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 5px;
        }
        .client-details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table thead {
            background-color: #4CAF50;
            color: white;
        }
        table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        table tbody tr:hover {
            background-color: #f5f5f5;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .total-row.grand-total {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            font-weight: bold;
            padding: 12px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #777;
            font-size: 11px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header clearfix">
            <div class="company-info">
                <div class="company-name">Waste2Product</div>
                <div>Recycled & Reusable Products</div>
                <div>Email: contact@waste2product.com</div>
                <div>Phone: +216 XX XXX XXX</div>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">INVOICE</div>
                <div class="info-row"><span class="label">Invoice #:</span> {{ $order->id }}</div>
                <div class="info-row"><span class="label">Date:</span> {{ \Carbon\Carbon::parse($order->date)->format('F d, Y') }}</div>
                <div class="info-row"><span class="label">Status:</span> {{ ucfirst($order->statut) }}</div>
            </div>
        </div>

        <!-- Client Information -->
        <div class="section">
            <div class="section-title">Bill To</div>
            <div class="client-details">
                @if($order->client)
                    <div class="info-row"><span class="label">Name:</span> {{ $order->client->name }}</div>
                    <div class="info-row"><span class="label">Email:</span> {{ $order->client->email }}</div>
                    @if($order->client->phone)
                        <div class="info-row"><span class="label">Phone:</span> {{ $order->client->phone }}</div>
                    @endif
                    @if($order->delivery_address)
                        <div class="info-row"><span class="label">Delivery Address:</span> {{ $order->delivery_address }}</div>
                    @endif
                @else
                    <div>No client information available</div>
                @endif
            </div>
        </div>

        <!-- Products Table -->
        <div class="section">
            <div class="section-title">Order Details</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">#</th>
                        <th style="width: 40%;">Product</th>
                        <th style="width: 15%;" class="text-center">Quantity</th>
                        <th style="width: 17%;" class="text-right">Unit Price</th>
                        <th style="width: 18%;" class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $product->nom }}</strong>
                                @if($product->description)
                                    <br><small style="color: #777;">{{ Str::limit($product->description, 60) }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $product->pivot->quantite }}</td>
                            <td class="text-right">{{ number_format($product->prix, 3) }} DT</td>
                            <td class="text-right"><strong>{{ number_format($product->prix * $product->pivot->quantite, 3) }} DT</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Section -->
        <div class="clearfix">
            <div class="total-section">
                <div class="total-row grand-total">
                    <span>TOTAL AMOUNT</span>
                    <span>{{ number_format($total, 3) }} DT</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>For any questions regarding this invoice, please contact us at contact@waste2product.com</p>
            <p style="margin-top: 10px;">Payment Terms: Due upon receipt | All prices in Tunisian Dinars (DT)</p>
        </div>
    </div>
</body>
</html>
