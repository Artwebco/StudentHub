<!DOCTYPE html>
<html lang="mk">

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo {
            max-height: 70px;
        }

        .school-info {
            text-align: right;
        }

        .invoice-title {
            font-size: 20px;
            font-bold: true;
            color: #4F46E5;
        }

        .details-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
        }

        .main-table th {
            background-color: #4F46E5;
            color: white;
            padding: 8px;
            text-align: left;
        }

        .main-table td {
            border-bottom: 1px solid #eee;
            padding: 8px;
        }

        .total-box {
            margin-top: 30px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td>
                @if($settings->logo_path)
                    <img src="{{ public_path('storage/' . $settings->logo_path) }}" class="logo">
                @endif
            </td>
            <td class="school-info">
                <div style="font-size: 16px; font-weight: bold;">{{ $settings->school_name }}</div>
                <div>{{ $settings->address }}, {{ $settings->city }}</div>
                <div>ЕДБ: {{ $settings->tax_number }}</div>
                <div>Жиро-сметка: {{ $settings->bank_account }}</div>
                <div>Банка: {{ $settings->bank_name }}</div>
            </td>
        </tr>
    </table>

    <table class="details-table">
        <tr>
            <td>
                <div class="invoice-title">ФАКТУРА бр. {{ $invoice->invoice_number }}</div>
                <div>Датум: {{ date('d.m.Y') }}</div>
            </td>
            <td style="text-align: right;">
                <strong>ДО (УЧЕНИК):</strong><br>
                {{ $invoice->student->first_name }} {{ $invoice->student->last_name }}<br>
                Период: {{ \Carbon\Carbon::parse($invoice->date_from)->format('d.m.Y') }} -
                {{ \Carbon\Carbon::parse($invoice->date_to)->format('d.m.Y') }}
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th>Опис на услуга</th>
                <th>Датум</th>
                <th>Време</th>
                <th style="text-align: right;">Цена</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lessons as $lesson)
                <tr>
                    <td>Индивидуален час - {{ $lesson->lessonType->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($lesson->lesson_date)->format('d.m.Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($lesson->start_time)->format('H:i') }}</td>
                    <td style="text-align: right;">{{ number_format($lesson->price_at_time, 2) }} ден.</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        ВКУПНО ЗА ПЛАЌАЊЕ: {{ number_format($invoice->total_amount, 2) }} МКД
    </div>

    <div class="footer">
        Фактурата е компјутерски генерирана и е валидна без печат и потпис.
        Ве молиме извршете ја уплатата во рок од 7 дена на горенаведената жиро-сметка.
    </div>
</body>

</html>
