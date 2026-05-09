<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>فاتورة - FishPOS</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
            color: #1a1a1a;
            line-height: 1.4;
        }

        .receipt {
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #1e1b4b;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1e1b4b;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #6b7280;
            font-size: 12px;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .customer-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f8fafc;
            border-radius: 6px;
            font-size: 12px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th,
        .items-table td {
            padding: 8px 4px;
            text-align: right;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
        }

        .items-table th {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
        }

        .items-table .item-name {
            font-weight: 500;
        }

        .totals {
            border-top: 2px solid #1e1b4b;
            padding-top: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .total-row.final {
            font-weight: bold;
            font-size: 14px;
            color: #1e1b4b;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }
            .receipt {
                border: none;
                box-shadow: none;
                max-width: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt" id="receipt-content">
        <!-- سيتم ملء هذا المحتوى عبر JavaScript -->
    </div>

    <script>
        // الحصول على البيانات من URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const invoiceNumber = urlParams.get('invoice');
        const date = urlParams.get('date');
        const total = urlParams.get('total');
        const items = JSON.parse(decodeURIComponent(urlParams.get('items') || '[]'));
        const orderType = urlParams.get('type') || 'store';
        const customerName = urlParams.get('customer');
        const phone = urlParams.get('phone');
        const address = urlParams.get('address');

        // إنشاء محتوى الفاتورة
        const receiptContent = document.getElementById('receipt-content');

        receiptContent.innerHTML = `
            <div class="header">
                <div class="logo">🐟 FishPOS</div>
                <div class="subtitle">نظام إدارة المطاعم</div>
            </div>

            <div class="invoice-info">
                <div>
                    <strong>رقم الفاتورة:</strong> ${invoiceNumber || 'غير محدد'}
                </div>
                <div>
                    <strong>التاريخ:</strong> ${date || new Date().toLocaleString('ar-EG')}
                </div>
            </div>

            ${customerName || phone || address ? `
            <div class="customer-info">
                ${customerName ? `<div><strong>العميل:</strong> ${customerName}</div>` : ''}
                ${phone ? `<div><strong>الهاتف:</strong> ${phone}</div>` : ''}
                ${address ? `<div><strong>العنوان:</strong> ${address}</div>` : ''}
                <div><strong>نوع الطلب:</strong> ${orderType === 'delivery' ? 'توصيل' : orderType === 'takeaway' ? 'تيك أواي' : 'في المطعم'}</div>
            </div>
            ` : ''}

            <table class="items-table">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>الكمية</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    ${items.map(item => `
                        <tr>
                            <td class="item-name">${item.name || 'منتج غير محدد'}</td>
                            <td>${item.qty || 1}</td>
                            <td>${(item.price || 0).toFixed(2)} ج.م</td>
                            <td>${(item.total || 0).toFixed(2)} ج.م</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>

            <div class="totals">
                <div class="total-row">
                    <span>المجموع الفرعي:</span>
                    <span>${(items.reduce((sum, item) => sum + (item.total || 0), 0)).toFixed(2)} ج.م</span>
                </div>
                <div class="total-row final">
                    <span>الإجمالي:</span>
                    <span>${(total || 0).toFixed(2)} ج.م</span>
                </div>
            </div>

            <div class="footer">
                <div>شكراً لزيارتكم</div>
                <div>FishPOS v1.0</div>
            </div>
        `;

        // طباعة تلقائية عند تحميل الصفحة
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };

        // إغلاق النافذة بعد الطباعة
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>