<!DOCTYPE html>
<html lang="mk">

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            line-height: 1.4;
        }

        .w-full {
            width: 100%;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        /* Header section */
        .logo {
            max-height: 80px;
            margin-bottom: 8px;
        }

        .school-details {
            font-size: 12px;
            line-height: 1.4;
        }

        .school-name {
            font-size: 14px;
            font-weight: bold;
        }

        /* Invoice number and date box */
        .info-table {
            border: 1px solid #a0a0a0;
            width: 220px;
            float: right;
        }

        .info-table th {
            background-color: #c2d2df;
            border: 1px solid #a0a0a0;
            padding: 5px;
            text-align: center;
            font-weight: bold;
        }

        .info-table td {
            border: 1px solid #a0a0a0;
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }

        /* User section */
        .section-header {
            background-color: #c2d2df;
            padding: 5px 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 40px;
            border: 1px solid #a0a0a0;
            width: 97%;
        }

        .user-details {
            padding: 15px 5px;
            font-size: 13px;
            font-weight: bold;
        }

        /* Main Items Table */
        .items-table {
            width: 100%;
            margin-top: 20px;
            border: 1px solid #a0a0a0;
        }

        .items-table th {
            background-color: #c2d2df;
            border: 1px solid #a0a0a0;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 12px
        }

        .items-table td {
            border: 1px solid #a0a0a0;
            padding: 8px;
            vertical-align: top;
            font-size: 12px
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Totals section */
        .total-row td {
            font-weight: bold;
            padding: 8px;
        }

        .footer-note {
            margin-top: 15px;
            font-style: italic;
            font-size: 11px;
            text-align: center
        }

        /* Signature section */
        .signature-container {
            margin-top: 40px;
            position: relative;
            width: 300px;
        }

        .signature-img {
            position: absolute;
            left: 20px;
            top: 0px;
            max-height: 90px;
            z-index: 10;
        }

        .cancelled-watermark {
            position: fixed;
            top: 42%;
            left: 10%;
            width: 80%;
            text-align: center;
            font-size: 72px;
            font-weight: 800;
            color: rgba(220, 38, 38, 0.16);
            border: 6px solid rgba(220, 38, 38, 0.22);
            border-radius: 12px;
            padding: 18px 0;
            transform: rotate(-24deg);
            z-index: 999;
            pointer-events: none;
            letter-spacing: 3px;
        }

        .cancelled-inline {
            margin-top: 10px;
            display: inline-block;
            font-size: 11px;
            font-weight: bold;
            color: #b91c1c;
            border: 1px solid #fecaca;
            background: #fef2f2;
            border-radius: 999px;
            padding: 2px 10px;
        }
    </style>
</head>

<body>
    @if($invoice->status === 'cancelled')
        <div class="cancelled-watermark">CANCELLED</div>
    @endif

    <table class="w-full">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                @php
                    $logoPath = storage_path('app/public/' . $settings->logo_path);
                    $logoUrl = ($settings->logo_path && file_exists($logoPath)) ? 'file://' . str_replace('\\', '/', $logoPath) : null;
                @endphp

                @if($logoUrl)
                    <img src="{{ $logoUrl }}" class="logo">
                @endif

                <div class="school-details">
                    <div class="school-name">{{ $settings->school_name }}</div>
                    <div>{{ $settings->company_description ?? 'Друштво за јазични, едукативни и информатички услуги' }}
                    </div>
                    <br>
                    <strong>Адреса:</strong> {{ $settings->address }}, {{ $settings->city }}<br>
                    <strong>Тел.:</strong> {{ $settings->phone ?? '+389 75 400 364' }}<br>
                    {{ $settings->website ?? 'www.besedi.mk' }}<br>
                    {{ $settings->email ?? 'contact@besedi.mk' }}<br>
                    <strong>Даночен број на фирмата:</strong> {{ $settings->registration_number }}<br>
                    <strong>ЕМБС:</strong> {{ $settings->tax_number  }}<br>
                    <br>
                    <strong>Банка депонент:</strong> {{ $settings->bank_name }}<br>
                    <strong>Бр. на сметката:</strong> {{ $settings->bank_account }}<br>
                    <br>
                    <strong>Рок на плаќање до:</strong> {{ \Carbon\Carbon::now()->addDays(5)->format('d.m.Y') }}
                </div>
            </td>
            <td style="width: 40%; vertical-align: top; padding-top: 150px;">
                <table class="info-table border-collapse">
                    <tr>
                        <th>ФАКТУРА #</th>
                        <th>ДАТУМ</th>
                    </tr>
                    <tr>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ \Carbon\Carbon::now()->locale('mk')->translatedFormat('j F Y') }}</td>
                    </tr>
                </table>
                @if($invoice->status === 'cancelled')
                    <div class="cancelled-inline">Статус: Поништено</div>
                @endif
            </td>
        </tr>
    </table>

    <div class="section-header">Корисник</div>
    <div class="user-details">
        @if($invoice->student)
            {{ $invoice->student->first_name }} {{ $invoice->student->last_name }}
        @else
            {{ $invoice->custom_client_name }}
        @endif
    </div>

    <table class="items-table border-collapse">
        <thead>
            <tr>
                <th style="width: 65%;">ОПИС НА УСЛУГАТА</th>
                <th style="width: 10%;" class="text-center">Количина</th>
                <th style="width: 15%;" class="text-right">Единечна цена МКД</th>
                <th style="width: 10%;" class="text-right">Вкупно МКД</th>
            </tr>
        </thead>
        <tbody>
            {{-- 1. АВАНСНА ФАКТУРА (ЗА СТУДЕНТ) --}}
            @if($invoice->is_advance && $invoice->student)
                <tr>
                    <td>
                        1. {{ $invoice->service_description }}<br>
                        <small>За период: {{ \Carbon\Carbon::parse($invoice->date_from)->format('d.m.Y') }} до
                            {{ \Carbon\Carbon::parse($invoice->date_to)->format('d.m.Y') }}</small>
                    </td>
                    <td class="text-center">{{ number_format($invoice->quantity, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($invoice->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                </tr>

                {{-- 2. ОПШТА УСЛУГА (ИТ / ПРЕВОД) --}}
            @elseif(!$invoice->student)
                <tr>
                    <td>
                        1. {{ $invoice->service_description }}
                        {{-- ТУКА НЕМА ДАТУМИ --}}
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                </tr>

                {{-- 3. РЕДОВНА ФАКТУРА ОД ДНЕВНИК --}}
            @elseif($invoice->student && isset($lessons) && $lessons->isNotEmpty())
                @foreach($lessons->groupBy('lesson_type_id') as $typeId => $groupedLessons)
                    <tr>
                        <td>
                            {{ $loop->iteration }}. Часови по {{ $groupedLessons->first()->lessonType->name }}<br>
                            <small>За период: {{ \Carbon\Carbon::parse($invoice->date_from)->format('d.m.Y') }} -
                                {{ \Carbon\Carbon::parse($invoice->date_to)->format('d.m.Y') }}</small>
                        </td>
                        <td class="text-center">{{ $groupedLessons->count() }}</td>
                        <td class="text-right">{{ number_format($groupedLessons->first()->price_at_time, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($groupedLessons->sum('price_at_time'), 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif

            {{-- ВКУПНО (СЕКОГАШ БЕЗ ДЕЦИМАЛИ) --}}
            <tr class="total-row">
                <td colspan="3" class="text-right">Вкупно:</td>
                <td class="text-right">
                    @php
                        $baseAmount = $invoice->discount_percent > 0 ? round($invoice->total_amount / (1 - $invoice->discount_percent / 100)) : $invoice->total_amount;
                    @endphp
                    {{ number_format($baseAmount, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="total-row">
                <td colspan="3" class="text-right">Попуст ({{ $invoice->discount_percent ?? 0 }}%):</td>
                <td class="text-right">
                    @php
                        $discount = $invoice->discount_percent > 0 ? $baseAmount - $invoice->total_amount : 0;
                    @endphp
                    {{ number_format($discount, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="total-row">
                <td colspan="3" class="text-right">Вкупно со попуст:</td>
                <td class="text-right">{{ number_format($invoice->total_amount, 0, ',', '.') }} МКД</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 10px; font-size:12px">
        <strong>Со зборови:</strong>
        {{ $amountInWords ?: \App\Support\AmountInWords::mkdDenars($invoice->total_amount) }}
    </div>

    <div class="footer-note">
        Според Законот, {{ $settings->school_name }} не пресметува ДДВ на своите услуги.
    </div>

    <div class="signature-container">
        {{-- Го користиме $sigData што го пративме од InvoiceManagement.php --}}
        @if(isset($sigData) && $sigData)
            <img src="{{ $sigData }}" class="signature-img">
        @endif

        <div style="margin-top: 40px;">
            _______________________<br>
            <div>{{ $settings->manager_name ?? 'Никола Никовски' }}, управител</div>
        </div>
    </div>
</body>

</html>
