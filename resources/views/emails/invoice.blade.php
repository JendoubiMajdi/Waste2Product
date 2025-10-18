<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #06b6d4 0%, #0e7490 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            background: #f8fafc;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .invoice-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #06b6d4;
        }
        .invoice-details h2 {
            color: #0e7490;
            margin-top: 0;
        }
        .invoice-details p {
            margin: 10px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #06b6d4;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background: #0e7490;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0f2fe;
            color: #64748b;
            font-size: 12px;
        }
        .total {
            font-size: 24px;
            color: #0e7490;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📄 Invoice from Waste2Product</h1>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $order->client->name ?? 'Customer' }}</strong>,</p>
        
        <p>Thank you for your order! Please find attached your invoice for order <strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>.</p>
        
        <div class="invoice-details">
            <h2>Invoice Summary</h2>
            <p><strong>Invoice Number:</strong> #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Date:</strong> {{ $order->date->format('d/m/Y') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($order->statut) }}</p>
            <p><strong>Total Amount:</strong> <span class="total">{{ number_format($order->total_amount, 3) }} DT</span></p>
        </div>
        
        <p>The complete invoice with all order details is attached as a PDF file to this email.</p>
        
        <p><strong>Order Items:</strong></p>
        <ul>
            @foreach($order->products as $product)
            <li>{{ $product->nom }} (x{{ $product->pivot->quantite }}) - {{ number_format($product->pivot->subtotal, 3) }} DT</li>
            @endforeach
        </ul>
        
        <p>If you have any questions about this invoice, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        <strong>Waste2Product Team</strong></p>
    </div>
    
    <div class="footer">
        <p>Waste2Product - Recycling for a Better Tomorrow</p>
        <p>123 Rue de Tunis, Tunis, Tunisia</p>
        <p>Email: contact@waste2product.tn | Phone: +216 XX XXX XXX</p>
        <p style="margin-top: 15px; font-size: 11px;">This is an automated email. Please do not reply directly to this message.</p>
    </div>
</body>
</html>
