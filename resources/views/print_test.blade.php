<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        /* إعدادات الطباعة الأساسية */
        @media print {
            @page {
                margin: 0;
                size: auto;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }

        /* تنسيق جسم الفاتورة */
        body {
            font-family: 'Arial', sans-serif;
            /* صغرنا العرض لـ 72 لضمان عدم الهروب جهة الشمال */
            width: 72mm; 
            margin: 0;
            padding: 2mm 4mm; /* زيادة الهامش الداخلي لحماية الأسعار */
            color: #000;
            background-color: #fff;
            box-sizing: border-box;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .receipt-header h3 {
            margin: 5px 0;
            font-size: 18px;
        }

        .receipt-info {
            font-size: 13px;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        /* تنسيق الجدول لضمان ثبات الأعمدة */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            table-layout: fixed; /* يضمن عدم تمدد الجدول خارج العرض المحدد */
        }

        th {
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }

        td {
            padding: 6px 0;
            vertical-align: top;
            word-wrap: break-word;
        }

        /* توزيع المساحات: الصنف ياخد النص، والكمية والسعر الباقي */
        .col-item { width: 50%; text-align: right; }
        .col-qty  { width: 15%; text-align: center; }
        .col-price { width: 35%; text-align: left; } /* السعر لليسار قليلاً لضمان ظهوره في الـ RTL */

        .total-section {
            border-top: 2px solid #000;
            margin-top: 10px;
            padding-top: 8px;
            font-weight: bold;
            font-size: 15px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .print-btn {
            background: #28a745;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <button class="no-print print-btn" onclick="window.print()">طباعة تجربة (Test Print)</button>

    <div class="receipt-header">
        <h3>مطعم تيست</h3>
        <p>فاتورة مبيعات رقم: #12345</p>
    </div>

    <div class="receipt-info">
        <div>التاريخ: 13-05-2026</div>
        <div>الكاشير: علي شلبي</div>
        <div>الفرع: الرئيسي</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-item">الصنف</th>
                <th class="col-qty">ع</th>
                <th class="col-price">السعر</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="col-item">ساندوتش برجر كبير جداً بالجبنة</td>
                <td class="col-qty">2</td>
                <td class="col-price">150.00</td>
            </tr>
            <tr>
                <td class="col-item">بطاطس مقلية عائلي</td>
                <td class="col-qty">1</td>
                <td class="col-price">40.00</td>
            </tr>
            <tr>
                <td class="col-item">مشروب غازي</td>
                <td class="col-qty">1</td>
                <td class="col-price">20.00</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: right;">الإجمالي النهائي:</td>
                <td style="text-align: left;">210.00 ج.م</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>شكراً لزيارتكم!</p>
        <p>رقم السجل الضريبي: 123-456-789</p>
        <p>--- نسخة تجريبية للنظام ---</p>
    </div>

</body>
</html>