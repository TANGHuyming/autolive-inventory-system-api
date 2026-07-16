<x-layout>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f5; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="450" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); font-family: Arial, Helvetica, sans-serif;">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #0f766e; padding: 28px 32px; text-align: center;">
                            <p style="margin: 0; color: #ffffff; font-size: 13px; letter-spacing: 1px; text-transform: uppercase; opacity: 0.85;">
                                Transaction Notification
                            </p>
                            <h1 style="margin: 8px 0 0; color: #ffffff; font-size: 20px; font-weight: 600;">
                                Processed by {{ $transaction['employee_full_name'] }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 32px;">
                            <p style="margin: 0 0 24px; color: #52525b; font-size: 14px; line-height: 1.6;">
                                A new transaction has been recorded. Please review the details below.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e4e4e7; color: #71717a; font-size: 13px; width: 140px;">Customer</td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e4e4e7; color: #18181b; font-size: 14px; font-weight: 600; text-align: right;">{{ $transaction['full_name'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e4e4e7; color: #71717a; font-size: 13px;">Telephone</td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e4e4e7; color: #18181b; font-size: 14px; font-weight: 600; text-align: right;">{{ $transaction['telephone'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; color: #71717a; font-size: 13px;">Date</td>
                                    <td style="padding: 12px 0; color: #18181b; font-size: 14px; font-weight: 600; text-align: right;">{{ \Carbon\Carbon::parse($transaction['transaction_date'])->format('M d, Y \a\t h:i A') }}</td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                <tr style="background-color: #0f766e; padding: 4px;">
                                    <td style="padding: 12px 4px; border: 1px solid #e4e4e7; color: #ffffff; font-size: 13px;">Item name</td>
                                    <td style="padding: 12px 4px; border: 1px solid #e4e4e7; color: #ffffff; font-size: 13px;">Stock Quantity</td>
                                    <td style="padding: 12px 4px; border: 1px solid #e4e4e7; color: #ffffff; font-size: 13px;">Location</td>
                                <tr>
                                @foreach ($items as $item)
                                    <tr>
                                        <td style="padding: 12px 4px; border: 1px solid #e4e4e7; color: #18181b; font-size: 14px; font-weight: 600; text-align: left;">{{$item['nameEn']}} - {{$item['nameKh']}}</td>
                                        <td style="padding: 12px 4px; border: 1px solid #e4e4e7; color: #18181b; font-size: 14px; font-weight: 600; text-align: left;">{{$item['stock_quantity']}}</td>
                                        <td style="padding: 12px 4px; border: 1px solid #e4e4e7; color: #18181b; font-size: 14px; font-weight: 600; text-align: left;">{{$item['location']}}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #fafafa; padding: 20px 32px; text-align: center; border-top: 1px solid #e4e4e7;">
                            <p style="margin: 0; color: #a1a1aa; font-size: 12px;">
                                This is an automated message. Please do not reply.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</x-layout>
