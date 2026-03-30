<!DOCTYPE html>
<html lang="mk">

<head>
    <meta charset="UTF-8">
    <title>Фактура {{ $invoice->invoice_number }}</title>
</head>

<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f7fb;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellspacing="0" cellpadding="0"
                    style="max-width:640px;background:#ffffff;border-radius:12px;padding:24px;border:1px solid #e5e7eb;">
                    <tr>
                        <td>
                            <h2 style="margin:0 0 16px 0;font-size:22px;color:#111827;">Нова фактура</h2>
                            <p style="margin:0 0 12px 0;font-size:15px;line-height:1.6;">Почитуван/а {{ $recipientName }},</p>
                            <p style="margin:0 0 12px 0;font-size:15px;line-height:1.6;">
                                Во прилог ја добивате фактурата со број <strong>{{ $invoice->invoice_number }}</strong>.
                            </p>
                            <p style="margin:0 0 20px 0;font-size:15px;line-height:1.6;">
                                Вкупен износ: <strong>{{ number_format($invoice->total_amount, 0, ',', '.') }} ден.</strong>
                            </p>

                            <p style="margin:0;font-size:14px;line-height:1.6;color:#374151;">
                                Доколку имате прашања, слободно одговорете на оваа порака.
                            </p>

                            <p style="margin:24px 0 0 0;font-size:14px;color:#6b7280;">Со почит,<br>{{ config('app.name') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
