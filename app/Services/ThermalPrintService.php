<?php

namespace App\Services;

use App\Models\Order;
use App\Models\RestaurantSetting;
use Illuminate\Support\Facades\Log;

class ThermalPrintService
{
    /**
     * Get list of all installed printers on the Windows machine
     */
    public static function getInstalledPrinters(): array
    {
        try {
            $output = [];
            exec('powershell -NoProfile -Command "(Get-Printer).Name"', $output, $code);
            if ($code === 0 && !empty($output)) {
                return array_values(array_filter(array_map('trim', $output)));
            }
        } catch (\Throwable $e) {
            Log::warning('ThermalPrintService: Unable to list printers: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Get the default Windows printer name
     */
    public static function getDefaultPrinter(): ?string
    {
        try {
            $output = [];
            exec('powershell -NoProfile -Command "(Get-CimInstance Win32_Printer -Filter \'Default = True\').Name"', $output, $code);
            if ($code === 0 && !empty($output[0])) {
                return trim($output[0]);
            }
        } catch (\Throwable $e) {
            Log::warning('ThermalPrintService: Unable to get default printer: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Format a clean, aligned plain text receipt for 58mm (32 cols) or 80mm (48 cols)
     */
    public static function formatReceiptText(Order $order, array $settings, string $paperSize = '58mm'): string
    {
        $is58 = ($paperSize === '58mm');
        $width = $is58 ? 32 : 48;

        $restaurantName = $settings['restaurant_name'] ?? 'THE GRAND ROYAL';
        $tagline = $settings['tagline'] ?? '';
        $address = $settings['address'] ?? '';
        $phone = $settings['phone'] ?? '';
        $email = $settings['email'] ?? '';
        $gstin = $settings['gstin'] ?? '';
        $currency = $settings['currency'] ?? 'Rs';
        $footerNote = $settings['invoice_footer'] ?? 'Thank you for dining with us! Please visit again.';

        $orderNo = $order->order_number ?? $order->id ?? 'N/A';
        $orderType = $order->order_type ?? 'Dine In';
        $tableInfo = $order->table_no ?? ($orderType === 'Dine In' ? 'Dine In' : 'Take Away');
        $attendant = $order->attendant ?? 'Staff';
        $isPaid = in_array($order->status, ['Complete', 'Paid']);

        $orderDate = '';
        $orderTime = '';
        if ($order->order_time) {
            $parts = explode(' ', $order->order_time);
            $orderDate = $parts[0] ?? '';
            $orderTime = implode(' ', array_slice($parts, 1));
        } else {
            $orderDate = now()->format('d-M-Y');
            $orderTime = now()->format('H:i:s');
        }

        $lines = [];
        $lines[] = str_repeat('=', $width);
        $lines[] = self::centerText(strtoupper($restaurantName), $width);
        if ($tagline) {
            $lines[] = self::centerText($tagline, $width);
        }
        if ($address) {
            $wrappedAddr = self::wrapWords($address, $width);
            foreach ($wrappedAddr as $wa) {
                $lines[] = self::centerText($wa, $width);
            }
        }
        if ($phone || $email) {
            $contactStr = implode(' | ', array_filter([$phone ? 'Ph: ' . $phone : '', $email]));
            $lines[] = self::centerText($contactStr, $width);
        }
        if ($gstin) {
            $lines[] = self::centerText('GSTIN: ' . $gstin, $width);
        }

        $lines[] = str_repeat('-', $width);
        $lines[] = self::centerText($isPaid ? 'TAX INVOICE' : 'ESTIMATE BILL', $width);
        $lines[] = self::centerText('ORDER #' . $orderNo, $width);
        $lines[] = str_repeat('-', $width);

        $lines[] = self::twoColText('Date: ' . $orderDate, 'Time: ' . $orderTime, $width);
        $lines[] = self::twoColText('Type: ' . $orderType, 'Table: ' . $tableInfo, $width);
        $lines[] = self::twoColText('Staff: ' . $attendant, 'Status: ' . ($isPaid ? 'PAID' : 'UNPAID'), $width);
        $lines[] = str_repeat('-', $width);

        // Column Headers
        if ($is58) {
            // 32 chars: Item(15) Qty(3) Rate(6) Amt(8)
            $lines[] = str_pad('Item', 15) . str_pad('Qty', 3, ' ', STR_PAD_LEFT) . str_pad('Rate', 6, ' ', STR_PAD_LEFT) . str_pad('Amt', 8, ' ', STR_PAD_LEFT);
        } else {
            // 48 chars: Item(25) Qty(5) Rate(8) Amt(10)
            $lines[] = str_pad('Item Name', 25) . str_pad('Qty', 5, ' ', STR_PAD_LEFT) . str_pad('Rate', 8, ' ', STR_PAD_LEFT) . str_pad('Amount', 10, ' ', STR_PAD_LEFT);
        }
        $lines[] = str_repeat('-', $width);

        $items = $order->items ?? [];
        $totalQty = 0;
        foreach ($items as $item) {
            $name = $item->item_name ?? $item->name ?? 'Dish';
            $qty = (float)($item->quantity ?? 1);
            $totalQty += $qty;
            $rate = (float)($item->unit_price ?? $item->price ?? 0);
            $amt = (float)($item->total_price ?? ($qty * $rate));
            $portion = $item->portion ?? 'Regular';

            if ($portion && $portion !== 'Regular') {
                $name .= ' [' . $portion . ']';
            }

            if ($is58) {
                if (mb_strlen($name) <= 15) {
                    $lines[] = str_pad($name, 15) . 
                               str_pad((string)$qty, 3, ' ', STR_PAD_LEFT) . 
                               str_pad(number_format($rate, 0), 6, ' ', STR_PAD_LEFT) . 
                               str_pad(number_format($amt, 2), 8, ' ', STR_PAD_LEFT);
                } else {
                    $lines[] = $name;
                    $lines[] = str_pad('', 15) . 
                               str_pad((string)$qty, 3, ' ', STR_PAD_LEFT) . 
                               str_pad(number_format($rate, 0), 6, ' ', STR_PAD_LEFT) . 
                               str_pad(number_format($amt, 2), 8, ' ', STR_PAD_LEFT);
                }
            } else {
                if (mb_strlen($name) <= 25) {
                    $lines[] = str_pad($name, 25) . 
                               str_pad((string)$qty, 5, ' ', STR_PAD_LEFT) . 
                               str_pad(number_format($rate, 2), 8, ' ', STR_PAD_LEFT) . 
                               str_pad(number_format($amt, 2), 10, ' ', STR_PAD_LEFT);
                } else {
                    $lines[] = $name;
                    $lines[] = str_pad('', 25) . 
                               str_pad((string)$qty, 5, ' ', STR_PAD_LEFT) . 
                               str_pad(number_format($rate, 2), 8, ' ', STR_PAD_LEFT) . 
                               str_pad(number_format($amt, 2), 10, ' ', STR_PAD_LEFT);
                }
            }

            if (!empty($item->notes)) {
                $lines[] = '  * ' . $item->notes;
            }
        }

        $totalAmount = (float)($order->total_amount ?? 0);
        $cashAmount = (float)($order->cash_amount ?? 0);
        $onlineAmount = (float)($order->online_amount ?? 0);
        $paymentMethod = $order->payment_method ?? 'Cash';

        $lines[] = str_repeat('-', $width);
        $lines[] = self::twoColText('Total Items (Qty):', count($items) . ' (' . $totalQty . ' pcs)', $width);
        $lines[] = self::twoColText('Subtotal:', $currency . ' ' . number_format($totalAmount, 2), $width);
        $lines[] = str_repeat('=', $width);
        $lines[] = self::twoColText($isPaid ? 'TOTAL PAID:' : 'TOTAL PAYABLE:', $currency . ' ' . number_format($totalAmount, 2), $width);
        $lines[] = str_repeat('=', $width);

        $lines[] = self::twoColText('Payment Mode:', $isPaid ? strtoupper($paymentMethod) : 'UNPAID', $width);
        if ($isPaid) {
            if ($paymentMethod === 'Split') {
                $lines[] = self::twoColText('  Cash Paid:', $currency . ' ' . number_format($cashAmount, 2), $width);
                $lines[] = self::twoColText('  Online Paid:', $currency . ' ' . number_format($onlineAmount, 2), $width);
            } elseif ($paymentMethod === 'Cash') {
                $lines[] = self::twoColText('  Cash Received:', $currency . ' ' . number_format($totalAmount, 2), $width);
            } elseif ($paymentMethod === 'Online') {
                $lines[] = self::twoColText('  Online (UPI/Card):', $currency . ' ' . number_format($totalAmount, 2), $width);
            }
        } else {
            $lines[] = self::twoColText('  Status:', 'PAYMENT DUE', $width);
        }

        $lines[] = str_repeat('-', $width);
        if ($footerNote) {
            $wrappedFooter = self::wrapWords($footerNote, $width);
            foreach ($wrappedFooter as $wf) {
                $lines[] = self::centerText($wf, $width);
            }
        }
        $lines[] = self::centerText('Powered by RESCO POS', $width);
        $lines[] = str_repeat('-', $width);
        $lines[] = "\n\n\n\n"; // Feed paper past cutter

        return implode("\r\n", $lines);
    }

    /**
     * Print formatted receipt text directly to the printer via Windows Spooler
     */
    public static function printDirectText(string $receiptContent, ?string $printerName = null): array
    {
        try {
            $storageDir = storage_path('app/receipts');
            if (!file_exists($storageDir)) {
                mkdir($storageDir, 0777, true);
            }

            $tempFile = $storageDir . '/receipt_' . time() . '_' . rand(1000, 9999) . '.txt';
            file_put_contents($tempFile, $receiptContent);

            $targetPrinter = $printerName ? trim($printerName) : '';
            if ($targetPrinter === 'default' || empty($targetPrinter)) {
                $targetPrinter = self::getDefaultPrinter();
            }

            if ($targetPrinter) {
                $escapedPrinter = addslashes($targetPrinter);
                $escapedFile = addslashes($tempFile);
                $cmd = 'powershell -NoProfile -Command "Get-Content -Raw -Encoding UTF8 \'' . $escapedFile . '\' | Out-Printer -Name \'' . $escapedPrinter . '\'"';
            } else {
                $escapedFile = addslashes($tempFile);
                $cmd = 'powershell -NoProfile -Command "Get-Content -Raw -Encoding UTF8 \'' . $escapedFile . '\' | Out-Printer"';
            }

            $output = [];
            $exitCode = 0;
            exec($cmd, $output, $exitCode);

            // Clean up temporary text file
            @unlink($tempFile);

            if ($exitCode === 0) {
                return [
                    'success' => true,
                    'message' => 'Bill sent directly to printer: ' . ($targetPrinter ?: 'Default Printer'),
                    'printer' => $targetPrinter ?: 'Default Printer',
                ];
            } else {
                $errMsg = implode(' ', $output);
                Log::error('ThermalPrintService direct print failed: ' . $errMsg);
                return [
                    'success' => false,
                    'message' => 'Failed to send to printer (' . ($targetPrinter ?: 'Default') . '): ' . $errMsg,
                ];
            }
        } catch (\Throwable $e) {
            Log::error('ThermalPrintService exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Printing error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Print an order directly
     */
    public static function printOrderDirect(Order $order): array
    {
        $settings = RestaurantSetting::getAllSettings();
        $paperSize = $settings['printer_paper_size'] ?? '58mm';
        $printerName = $settings['direct_printer_name'] ?? null;

        $content = self::formatReceiptText($order, $settings, $paperSize);
        return self::printDirectText($content, $printerName);
    }

    /**
     * Print a test receipt
     */
    public static function printTestReceipt(?string $printerName = null, string $paperSize = '58mm'): array
    {
        $settings = RestaurantSetting::getAllSettings();
        $is58 = ($paperSize === '58mm');
        $width = $is58 ? 32 : 48;

        $lines = [];
        $lines[] = str_repeat('=', $width);
        $lines[] = self::centerText('RESCO POS - TEST RECEIPT', $width);
        $lines[] = self::centerText('58mm Thermal Direct Print', $width);
        $lines[] = str_repeat('-', $width);
        $lines[] = self::twoColText('Date: ' . now()->format('d-M-Y'), 'Time: ' . now()->format('H:i:s'), $width);
        $lines[] = self::twoColText('Printer: ' . ($printerName ?: 'Default'), 'Size: ' . $paperSize, $width);
        $lines[] = str_repeat('-', $width);
        $lines[] = self::centerText('DIRECT PRINT IS WORKING!', $width);
        $lines[] = self::centerText('Ready for fast billing.', $width);
        $lines[] = str_repeat('=', $width);
        $lines[] = self::centerText('Powered by RESCO Software', $width);
        $lines[] = str_repeat('-', $width);
        $lines[] = "\n\n\n\n";

        $content = implode("\r\n", $lines);
        return self::printDirectText($content, $printerName);
    }

    /* ---------------- Helper Formatting Functions ---------------- */

    private static function centerText(string $text, int $width): string
    {
        $len = mb_strlen($text);
        if ($len >= $width) {
            return mb_substr($text, 0, $width);
        }
        $left = (int)floor(($width - $len) / 2);
        return str_repeat(' ', $left) . $text;
    }

    private static function twoColText(string $left, string $right, int $width): string
    {
        $space = $width - mb_strlen($left) - mb_strlen($right);
        if ($space < 1) {
            $space = 1;
        }
        return $left . str_repeat(' ', $space) . $right;
    }

    private static function wrapWords(string $text, int $width): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            if (mb_strlen($currentLine . ($currentLine ? ' ' : '') . $word) <= $width) {
                $currentLine .= ($currentLine ? ' ' : '') . $word;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            }
        }
        if ($currentLine) {
            $lines[] = $currentLine;
        }

        return $lines;
    }
}
