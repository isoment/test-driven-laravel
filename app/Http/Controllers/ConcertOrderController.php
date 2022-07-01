<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Models\Concert;
use Illuminate\Http\Request;

class ConcertOrderController extends Controller
{
    private PaymentGateway $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $concert = Concert::find($concertId);

        $ticketQuantity = request('ticket_quantity');

        $amount = $ticketQuantity * $concert->ticket_price;

        $token = request('payment_token');

        $this->paymentGateway->charge($amount, $token);

        return response()->json([], 201);
    }
}
