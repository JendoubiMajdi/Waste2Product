# Invoice System Documentation

## Overview
The invoice system allows you to generate professional PDF invoices for orders and automatically email them to clients. All invoices display prices in Tunisian Dinars (DT).

## Features Implemented

### 1. PDF Invoice Generation
- Professional invoice layout with company branding
- Includes order details, client information, and itemized products
- Displays quantities, unit prices, and subtotals
- Shows total amount in DT currency
- Footer with payment information and thank you message

### 2. Email Functionality
- Automatically sends invoice as PDF attachment to client's email
- Professional email template with order summary
- Includes company contact information

### 3. User Interface
- **Download Invoice Button**: Available on both order list and order detail pages
- **Email Invoice Button**: Send invoice directly to client with confirmation prompt
- Icons for easy identification (download and envelope icons)

## Files Modified/Created

### Controllers
- **app/Http/Controllers/OrderController.php**
  - Added `downloadInvoice()` method - generates and downloads PDF
  - Added `emailInvoice()` method - generates PDF, emails it, and cleans up temp file

### Views
- **resources/views/invoices/order.blade.php** (NEW)
  - Professional PDF invoice template
  - Styled with CSS for print-friendly output
  
- **resources/views/emails/invoice.blade.php** (NEW)
  - HTML email template for invoice notifications
  - Responsive design with professional styling

- **resources/views/orders/show.blade.php** (UPDATED)
  - Added invoice download and email buttons
  - Enhanced product table with unit prices and subtotals

- **resources/views/orders/index.blade.php** (UPDATED)
  - Added quick-access invoice buttons in actions column
  - Icon buttons for download and email

### Mailable
- **app/Mail/OrderInvoiceMail.php** (NEW)
  - Handles invoice email with PDF attachment
  - Uses email template view

### Routes
- **routes/web.php** (UPDATED)
  ```php
  Route::get('/orders/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('orders.invoice.download');
  Route::post('/orders/{order}/invoice/email', [OrderController::class, 'emailInvoice'])->name('orders.invoice.email');
  ```

## Usage

### Download Invoice
1. Navigate to Orders page (index or detail view)
2. Click the download icon button
3. PDF invoice will be generated and downloaded automatically

### Email Invoice
1. Navigate to Orders page (index or detail view)
2. Click the envelope icon button
3. Confirm the action in the popup dialog
4. Invoice will be generated and emailed to the client's registered email address

## Technical Details

### Dependencies
- **barryvdh/laravel-dompdf**: v3.1.1 - PDF generation library

### Invoice Template Includes
- Company name and contact information
- Invoice number (Order ID)
- Invoice date (Order date)
- Client information (name, email, phone)
- Itemized product list with:
  - Product names
  - Quantities
  - Unit prices in DT
  - Subtotals in DT
- Total amount in DT
- Payment information and terms
- Professional footer

### Email Template Includes
- Branded header with gradient
- Order summary (number and date)
- List of ordered items
- Company contact information
- Professional footer

## Configuration Required

Before using the email functionality, ensure your `.env` file has proper email configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@waste2product.com
MAIL_FROM_NAME="Waste2Product"
```

## Testing

To test the invoice system:

1. **Test PDF Generation**:
   - Create or open an order
   - Click "Download Invoice"
   - Verify PDF contents are correct

2. **Test Email Sending**:
   - Ensure email configuration is set in `.env`
   - Create an order with a valid client email
   - Click "Email Invoice"
   - Check client's email inbox for the invoice

3. **Test from Order List**:
   - Go to Orders index page
   - Use quick-access buttons to download/email invoices

## Error Handling

The system includes error handling for:
- Missing client information
- Email sending failures
- PDF generation errors
- Temporary file cleanup

Success and error messages are displayed using Laravel's flash session messages.

## Future Enhancements

Potential improvements to consider:
- Invoice numbering system separate from order ID
- Multiple invoice templates
- Invoice history/archive
- Batch invoice generation
- Invoice preview before sending
- Custom email messages
- Invoice settings page for company info
