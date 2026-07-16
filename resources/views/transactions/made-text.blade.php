TRANSACTION NOTIFICATION
=========================

Processed by: {{ $transaction['employee_full_name'] }}

A new transaction has been recorded. Please review the details below.

-------------------------
Customer:   {{ $transaction['full_name'] }}
Telephone:  {{ $transaction['telephone'] }}
Date:       {{ \Carbon\Carbon::parse($transaction['transaction_date'])->format('M d, Y \a\t h:i A') }}
-------------------------

This is an automated message. Please do not reply.
