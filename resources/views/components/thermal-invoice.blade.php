<!-- Thermal Receipt / Invoice Print Component (58mm / 80mm ESC/POS Optimized) -->
<script>
    window.restaurantSettings = @json(\App\Models\RestaurantSetting::getAllSettings());

    window.printOrderInvoice = function(order) {
        if (!order) {
            alert('No order data found to print invoice.');
            return;
        }

        const settings = window.restaurantSettings || {};
        const restaurantName = settings.restaurant_name || 'The Grand Royal Restaurant';
        const tagline = settings.tagline || '';
        const address = settings.address || '';
        const phone = settings.phone || '';
        const email = settings.email || '';
        const gstin = settings.gstin || '';
        const footerNote = settings.invoice_footer || 'Thank you for dining with us! Please visit again.';
        const currency = settings.currency || '₹';
        const paperSize = settings.printer_paper_size || '58mm';
        const is58mm = (paperSize === '58mm');

        const orderNo = order.order_number || order.id || 'N/A';
        const orderType = order.order_type || 'Dine In';
        const tableInfo = order.table_no || (orderType === 'Dine In' ? 'Dine In' : 'Take Away');
        const attendant = order.attendant || 'Staff';
        
        let orderDate = '';
        let orderTime = '';
        if (order.order_time) {
            const parts = order.order_time.split(' ');
            orderDate = parts[0] || '';
            orderTime = parts.slice(1).join(' ') || '';
        } else {
            const now = new Date();
            orderDate = now.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
            orderTime = now.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }

        const items = order.items || [];
        if (items.length === 0) {
            alert('No ordered items found for this invoice.');
            return;
        }

        let totalQty = 0;
        const itemRows = items.map(item => {
            const name = item.item_name || item.name || 'Dish';
            const qty = Number(item.quantity || 1);
            totalQty += qty;
            const rate = Number(item.unit_price || item.price || 0);
            const amt = Number(item.total_price || (qty * rate));
            const portion = item.portion;
            const notes = item.notes;

            return `
                <tr style="border-bottom: 1px dashed #ddd;">
                    <td style="padding: 2.5px 1px; vertical-align: top; text-align: left; width: 44%; word-break: break-word;">
                        <div style="font-weight: 700; font-size: ${is58mm ? '9.5px' : '11px'}; color: #000; line-height: 1.15;">${name}</div>
                        ${portion && portion !== 'Regular' ? `<span style="display: inline-block; font-size: ${is58mm ? '7.5px' : '8.5px'}; font-weight: 800; background: #f0f0f0; border: 1px solid #bbb; padding: 0 2px; border-radius: 2px; margin-top: 1px;">[${portion}]</span>` : ''}
                        ${notes ? `<div style="font-size: ${is58mm ? '7.5px' : '8.5px'}; color: #333; font-style: italic; margin-top: 1px;">* ${notes}</div>` : ''}
                    </td>
                    <td style="padding: 2.5px 1px; vertical-align: top; text-align: center; font-weight: bold; font-size: ${is58mm ? '9.5px' : '11px'}; width: 14%;">
                        ${qty}
                    </td>
                    <td style="padding: 2.5px 1px; vertical-align: top; text-align: right; font-family: monospace; font-size: ${is58mm ? '8.5px' : '10.5px'}; width: 19%;">
                        ${rate.toFixed(2)}
                    </td>
                    <td style="padding: 2.5px 1px; vertical-align: top; text-align: right; font-family: monospace; font-weight: bold; font-size: ${is58mm ? '9.5px' : '11px'}; width: 23%;">
                        ${amt.toFixed(2)}
                    </td>
                </tr>
            `;
        }).join('');

        const totalAmount = Number(order.total_amount || 0);
        const isPaid = (order.status === 'Complete' || order.status === 'Paid');
        const paymentMethod = order.payment_method || 'Cash';
        const cashAmount = Number(order.cash_amount || 0);
        const onlineAmount = Number(order.online_amount || 0);

        let paymentDetailsHtml = '';
        if (!isPaid) {
            paymentDetailsHtml = `
                <tr>
                    <td style="padding-left: 6px; font-size: ${is58mm ? '8.5px' : '10px'}; color: #ea580c; font-weight: bold;">&bull; Status:</td>
                    <td class="text-right font-mono" style="font-size: ${is58mm ? '8.5px' : '10px'}; color: #ea580c; font-weight: bold;">PAYMENT DUE</td>
                </tr>
            `;
        } else if (paymentMethod === 'Split') {
            paymentDetailsHtml = `
                <tr>
                    <td style="padding-left: 6px; font-size: ${is58mm ? '8.5px' : '10px'};">&bull; Cash Paid:</td>
                    <td class="text-right font-mono" style="font-size: ${is58mm ? '8.5px' : '10px'};">${currency} ${cashAmount.toFixed(2)}</td>
                </tr>
                <tr>
                    <td style="padding-left: 6px; font-size: ${is58mm ? '8.5px' : '10px'};">&bull; Online Paid:</td>
                    <td class="text-right font-mono" style="font-size: ${is58mm ? '8.5px' : '10px'};">${currency} ${onlineAmount.toFixed(2)}</td>
                </tr>
            `;
        } else if (paymentMethod === 'Cash') {
            paymentDetailsHtml = `
                <tr>
                    <td style="padding-left: 6px; font-size: ${is58mm ? '8.5px' : '10px'};">&bull; Cash Received:</td>
                    <td class="text-right font-mono" style="font-size: ${is58mm ? '8.5px' : '10px'};">${currency} ${totalAmount.toFixed(2)}</td>
                </tr>
            `;
        } else if (paymentMethod === 'Online') {
            paymentDetailsHtml = `
                <tr>
                    <td style="padding-left: 6px; font-size: ${is58mm ? '8.5px' : '10px'};">&bull; Online (UPI/Card):</td>
                    <td class="text-right font-mono" style="font-size: ${is58mm ? '8.5px' : '10px'};">${currency} ${totalAmount.toFixed(2)}</td>
                </tr>
            `;
        }

        const cssPageSize = is58mm ? '58mm auto' : '80mm auto';
        const bodyWidth = is58mm ? '48mm' : '72mm';
        const baseFontSize = is58mm ? '9.5px' : '11.5px';
        const headerTitleSize = is58mm ? '13px' : '15px';
        const grandTotalSize = is58mm ? '12.5px' : '14px';
        const subFontSize = is58mm ? '8.5px' : '10px';

        const invoiceHtml = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Invoice #${orderNo} - ${restaurantName}</title>
                <style>
                    @page {
                        size: ${cssPageSize};
                        margin: 0mm;
                    }
                    @media print {
                        html, body {
                            width: ${bodyWidth} !important;
                            max-width: ${bodyWidth} !important;
                            margin: 0 auto !important;
                            padding: 0 !important;
                        }
                    }
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Courier New', monospace, sans-serif;
                        width: ${bodyWidth};
                        max-width: ${bodyWidth};
                        margin: 0 auto;
                        padding: 1mm 0 6mm 0;
                        color: #000;
                        background: #fff;
                        font-size: ${baseFontSize};
                        line-height: 1.25;
                        box-sizing: border-box;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    .text-center { text-align: center; }
                    .text-right { text-align: right; }
                    .text-left { text-align: left; }
                    .bold { font-weight: bold; }
                    .extra-bold { font-weight: 900; }
                    .font-mono { font-family: 'Courier New', monospace, monospace; }
                    .dashed { border-top: 1px dashed #000; margin: 3px 0; }
                    .double-solid { border-top: 1.5px solid #000; border-bottom: 1.5px solid #000; padding: 2.5px 0; margin: 3px 0; }
                    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
                    .header-title {
                        font-size: ${headerTitleSize};
                        font-weight: 900;
                        text-transform: uppercase;
                        letter-spacing: 0.3px;
                        line-height: 1.15;
                    }
                    .header-subtitle {
                        font-size: ${subFontSize};
                        margin-top: 1.5px;
                        color: #111;
                    }
                    .header-contact {
                        font-size: ${subFontSize};
                        margin-top: 1px;
                        color: #111;
                        word-break: break-word;
                    }
                    .invoice-badge {
                        display: inline-block;
                        border: 1px solid #000;
                        padding: 1px 5px;
                        font-weight: 900;
                        font-size: ${subFontSize};
                        letter-spacing: 0.3px;
                        text-transform: uppercase;
                        margin: 2px 0;
                    }
                    .meta-table td {
                        padding: 1px 0;
                        font-size: ${subFontSize};
                        line-height: 1.2;
                    }
                    .items-table th {
                        font-size: ${subFontSize};
                        text-transform: uppercase;
                        border-top: 1px dashed #000;
                        border-bottom: 1px dashed #000;
                        padding: 2.5px 1px;
                        font-weight: 800;
                    }
                    .items-table td {
                        padding: 2.5px 1px;
                        line-height: 1.2;
                    }
                </style>
            </head>
            <body>
                <!-- Header (Dynamic from Settings) -->
                <div class="text-center">
                    <div class="header-title">${restaurantName}</div>
                    ${tagline ? `<div class="header-subtitle">${tagline}</div>` : ''}
                    ${address ? `<div class="header-contact">${address}</div>` : ''}
                    ${phone || email ? `<div class="header-contact">${[phone ? 'Ph: ' + phone : '', email].filter(Boolean).join(' | ')}</div>` : ''}
                    ${gstin ? `<div class="header-contact bold">GSTIN: ${gstin}</div>` : ''}
                    
                    <div class="dashed"></div>
                    <div class="invoice-badge">${isPaid ? 'TAX INVOICE' : 'ESTIMATE BILL'}</div>
                    <div class="extra-bold" style="font-size: ${is58mm ? '12px' : '14px'}; margin-top: 1px;">ORDER #${orderNo}</div>
                </div>

                <div class="dashed"></div>

                <!-- Meta Details -->
                <table class="meta-table">
                    <tr>
                        <td style="width: 50%;"><strong>Date:</strong> ${orderDate}</td>
                        <td style="width: 50%;" class="text-right"><strong>Time:</strong> ${orderTime}</td>
                    </tr>
                    <tr>
                        <td><strong>Type:</strong> ${orderType}</td>
                        <td class="text-right"><strong>Table:</strong> ${tableInfo}</td>
                    </tr>
                    <tr>
                        <td><strong>Staff:</strong> ${attendant}</td>
                        <td class="text-right"><strong>Status:</strong> <span class="bold" style="${isPaid ? 'color: #047857;' : 'color: #ea580c;'}">${isPaid ? 'PAID' : 'UNPAID'}</span></td>
                    </tr>
                </table>

                <!-- Items Table -->
                <table class="items-table" style="margin-top: 3px;">
                    <thead>
                        <tr>
                            <th class="text-left" style="width: 44%;">Item</th>
                            <th class="text-center" style="width: 14%;">Qty</th>
                            <th class="text-right" style="width: 19%;">Rate</th>
                            <th class="text-right" style="width: 23%;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemRows}
                    </tbody>
                </table>

                <div class="dashed"></div>

                <!-- Totals -->
                <table class="meta-table">
                    <tr>
                        <td>Total Items (Qty):</td>
                        <td class="text-right bold">${items.length} items (${totalQty} pcs)</td>
                    </tr>
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right bold font-mono">${currency} ${totalAmount.toFixed(2)}</td>
                    </tr>
                </table>

                <div class="double-solid">
                    <table>
                        <tr>
                            <td style="font-size: ${grandTotalSize}; font-weight: 900;">${isPaid ? 'TOTAL PAID:' : 'TOTAL PAYABLE:'}</td>
                            <td class="text-right font-mono" style="font-size: ${grandTotalSize}; font-weight: 900;">${currency} ${totalAmount.toFixed(2)}</td>
                        </tr>
                    </table>
                </div>

                <!-- Payment Method Breakdown -->
                <table class="meta-table">
                    <tr>
                        <td><strong>Payment Mode:</strong></td>
                        <td class="text-right bold uppercase">${isPaid ? paymentMethod : 'UNPAID'}</td>
                    </tr>
                    ${paymentDetailsHtml}
                </table>

                <div class="dashed"></div>

                <!-- Footer (Dynamic from Settings) -->
                <div class="text-center" style="font-size: ${subFontSize}; margin-top: 4px; line-height: 1.35;">
                    <div class="bold" style="font-size: ${is58mm ? '9px' : '10px'};">${footerNote}</div>
                    <div style="margin-top: 3px; font-size: 8px; color: #444;">Powered by RESCO POS</div>
                </div>
            </body>
            </html>
        `;

        const directPrintEnabled = (settings.direct_print_enabled !== '0' && settings.direct_print_enabled !== false);

        function fallbackBrowserPrint() {
            const printFrame = document.createElement('iframe');
            printFrame.style.position = 'fixed';
            printFrame.style.right = '0';
            printFrame.style.bottom = '0';
            printFrame.style.width = '0';
            printFrame.style.height = '0';
            printFrame.style.border = '0';
            document.body.appendChild(printFrame);

            const frameDoc = printFrame.contentWindow.document;
            frameDoc.open();
            frameDoc.write(invoiceHtml);
            frameDoc.close();

            setTimeout(() => {
                printFrame.contentWindow.focus();
                printFrame.contentWindow.print();
                setTimeout(() => {
                    if (document.body.contains(printFrame)) {
                        document.body.removeChild(printFrame);
                    }
                }, 3000);
            }, 250);
        }

        function showPrintToast(msg, type = 'info') {
            const existingToast = document.getElementById('resco-print-toast');
            if (existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.id = 'resco-print-toast';
            const bgClass = type === 'success' 
                ? 'bg-emerald-600 text-white shadow-emerald-500/20' 
                : (type === 'warning' ? 'bg-amber-600 text-white shadow-amber-500/20' : 'bg-gray-900 text-white shadow-black/30');

            toast.className = `fixed bottom-5 right-5 z-[9999] px-4 py-3 rounded-lg shadow-xl text-xs font-semibold flex items-center space-x-2.5 transition-all duration-300 transform translate-y-2 opacity-0 ${bgClass}`;
            toast.innerHTML = `
                <svg class="w-4 h-4 animate-spin ${type !== 'info' ? 'hidden' : ''}" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>${msg}</span>
            `;
            document.body.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            });

            setTimeout(() => {
                if (toast && document.body.contains(toast)) {
                    toast.classList.add('translate-y-2', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }
            }, type === 'info' ? 3500 : 4000);
        }

        // If Direct Print is enabled in Settings and order has a valid ID
        if (directPrintEnabled && order.id) {
            showPrintToast('🖨️ Sending bill directly to thermal printer...', 'info');

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            fetch('/orders/' + order.id + '/direct-print', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showPrintToast('✅ ' + (data.message || 'Bill printed successfully!'), 'success');
                } else {
                    console.warn('Direct print fallback:', data.message);
                    showPrintToast('⚠️ Direct printer offline. Opening print preview...', 'warning');
                    fallbackBrowserPrint();
                }
            })
            .catch(err => {
                console.error('Direct print fetch error:', err);
                fallbackBrowserPrint();
            });
            return;
        }

        // Fallback or Normal mode
        fallbackBrowserPrint();
    };
</script>
