<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .invoice-info {
            background-color: #f9f9f9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin: 20px 0;
        }
        .invoice-info p {
            margin: 8px 0;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .order-summary {
            margin: 20px 0;
        }
        .order-summary h3 {
            color: #4CAF50;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }
        .product-list {
            list-style: none;
            padding: 0;
        }
        .product-list li {
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .total {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            color: #777;
            font-size: 12px;
            border-top: 1px solid #e0e0e0;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Waste2Product</h1>
            <p style="margin: 5px 0; font-size: 16px;">Your Invoice is Ready</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $order->client ? $order->client->name : 'Valued Customer' }},</p>
            
            <p>Thank you for your order! Please find your invoice attached to this email.</p>
            
            <div class="invoice-info">
                <p><span class="label">Invoice Number:</span> #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p><span class="label">Order Date:</span> {{ \Carbon\Carbon::parse($order->date)->format('F d, Y') }}</p>
                <p><span class="label">Status:</span> {{ ucfirst($order->statut) }}</p>
                @if($order->delivery_address)
                    <p><span class="label">Delivery Address:</span> {{ $order->delivery_address }}</p>
                @endif
            </div>
            
            <div class="order-summary">
                <h3>Order Summary</h3>
                <ul class="product-list">
                    @foreach($order->products as $product)
                        <li>
                            <strong>{{ $product->nom }}</strong> - 
                            Qty: {{ $product->pivot->quantite }} × 
                            {{ number_format($product->prix, 3) }} DT = 
                            <strong>{{ number_format($product->prix * $product->pivot->quantite, 3) }} DT</strong>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="total">
                Total Amount: {{ number_format($total, 3) }} DT
            </div>
            
            <p>The invoice PDF is attached to this email. If you have any questions or concerns about your order, please don't hesitate to contact us.</p>
            
            <p style="margin-top: 30px;">Best regards,<br><strong>Waste2Product Team</strong></p>
        </div>
        
        <div class="footer">
            <p><strong>Waste2Product</strong></p>
            <p>Recycled & Reusable Products</p>
            <p>Email: contact@waste2product.com | Phone: +216 XX XXX XXX</p>
            <p style="margin-top: 10px; font-size: 11px;">This is an automated email. Please do not reply directly to this message.</p>
        </div>
    </div>
</body>
</html>
