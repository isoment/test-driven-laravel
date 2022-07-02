<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
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
        $concert = Concert::published()->findOrFail($concertId);

        request()->validate([
            'email' => 'required|email',
            'ticket_quantity' => 'required|integer|min:1',
            'payment_token' => 'required'
        ]);

        try {
            $this->paymentGateway->charge(
                request('ticket_quantity') * $concert->ticket_price, 
                request('payment_token')
            );
    
            $order = $concert->orderTickets(
                request('email'), 
                request('ticket_quantity')
            );
        } catch(PaymentFailedException $e) {
            return response()->json([], 422);
        }

        return response()->json([], 201);
    }
}
