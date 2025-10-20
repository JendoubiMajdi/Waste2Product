<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Collection;

class OrdersExport
{
    protected $query;
    protected $format;

    public function __construct($query, $format = 'xlsx')
    {
        $this->query = $query;
        $this->format = $format;
    }

    public function export()
    {
        $orders = $this->query->get();
        
        // Prepare data for export
        $data = [];
        
        // Add headers
        $data[] = [
            'Order ID',
            'Date',
            'Client Name',
            'Client Email',
            'Status',
            'Delivery Address',
            'Products',
            'Total Quantity',
            'Total Amount (DT)',
            'Created At',
        ];

        // Add data rows
        foreach ($orders as $order) {
            $productsText = $order->products->map(function($product) {
                return $product->nom . ' (Qty: ' . $product->pivot->quantite . ')';
            })->implode(', ');

            $totalQuantity = $order->products->sum('pivot.quantite');

            $data[] = [
                $order->id,
                $order->date,
                $order->client ? $order->client->name : 'N/A',
                $order->client ? $order->client->email : 'N/A',
                ucfirst($order->statut),
                $order->delivery_address ?? 'N/A',
                $productsText ?: 'No products',
                $totalQuantity,
                number_format($order->total_amount, 3),
                $order->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $data;
    }

    public function download()
    {
        $data = $this->export();
        $filename = 'orders_export_' . now()->format('Y-m-d_His');

        if ($this->format === 'csv') {
            return $this->downloadCSV($data, $filename);
        }

        return $this->downloadExcel($data, $filename);
    }

    protected function downloadCSV($data, $filename)
    {
        $filename .= '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function downloadExcel($data, $filename)
    {
        $filename .= '.xls';

        // Create Excel XML format (compatible with PHP 8+, no external libraries needed)
        $xmlContent = $this->generateExcelXML($data);
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        return response($xmlContent, 200, $headers);
    }

    protected function generateExcelXML($data)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
        
        // Define styles
        $xml .= '<Styles>' . "\n";
        $xml .= '<Style ss:ID="header">' . "\n";
        $xml .= '<Font ss:Bold="1" ss:Color="#FFFFFF"/>' . "\n";
        $xml .= '<Interior ss:Color="#4CAF50" ss:Pattern="Solid"/>' . "\n";
        $xml .= '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/>' . "\n";
        $xml .= '</Style>' . "\n";
        $xml .= '</Styles>' . "\n";
        
        // Create worksheet
        $xml .= '<Worksheet ss:Name="Orders">' . "\n";
        $xml .= '<Table>' . "\n";
        
        // Add rows
        $isHeader = true;
        foreach ($data as $row) {
            $xml .= '<Row>' . "\n";
            foreach ($row as $cell) {
                $styleAttr = $isHeader ? ' ss:StyleID="header"' : '';
                $cell = htmlspecialchars((string)$cell, ENT_XML1, 'UTF-8');
                $xml .= '<Cell' . $styleAttr . '><Data ss:Type="String">' . $cell . '</Data></Cell>' . "\n";
            }
            $xml .= '</Row>' . "\n";
            $isHeader = false;
        }
        
        $xml .= '</Table>' . "\n";
        $xml .= '</Worksheet>' . "\n";
        $xml .= '</Workbook>';
        
        return $xml;
    }
}
