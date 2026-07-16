<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
// use Illuminate\Mail\Mailables\Address;
use App\Models\Transaction;

class TransactionMade extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected Transaction $transaction,
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // from: new Address('2023277tang@aupp.edu.kh', 'TANG Huyming'),
            // replyTo: new Address('noreply@autolive.kh'),
            subject: 'Transaction Made',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $transaction = $this->transaction;
        $employee = $transaction->employee->first();
        return new Content(
            view: 'transactions.made',
            with: [
                'transaction' => [
                    'employee_full_name' => $employee->first_name . $employee->last_name,
                    'full_name' => $transaction->first_name . $transaction->last_name,
                    'telephone' => $transaction->telephone,
                    'transaction_date' => $transaction->transaction_date,
                ],

                'items' => $transaction->inventories->map(function ($item) {
                    $shelf = $item->shelves->first();
                    return [
                        'nameEn' => $item->nameEn,
                        'nameKh' => $item->nameKh,
                        'stock_quantity' => $shelf->pivot->stock_quantity,
                        'location' => $shelf->name . ' ' . $shelf->bay->name . ' ' . $shelf->bay->warehouse->name,
                    ];
                })->toArray(),
            ],
            text: 'transactions.made-text'
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
