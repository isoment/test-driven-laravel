<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Exceptions\NotEnoughTicketsException;
use App\Mail\OrderConfirmationEmail;
use App\Models\Concert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ConcertOrderController extends Controller
{
    private PaymentGateway $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store(int $concertId) : JsonResponse
    {
        $concert = Concert::published()->findOrFail($concertId);

        request()->validate([
            'email' => 'required|email',
            'ticket_quantity' => 'required|integer|min:1',
            'payment_token' => 'required'
        ]);

        try {
            // Reserve tickets
            $reservation = $concert->reserveTickets(request('ticket_quantity'), request('email'));

            // Complete the reservation
            $order = $reservation->complete(
                $this->paymentGateway, 
                request('payment_token'), 
                $concert->user->stripe_account_id
            );

            // Send an email after purchase
            Mail::to($order->email)->send(new OrderConfirmationEmail($order));
    
            return response()->json($order, 201);
        } catch(PaymentFailedException $e) {
            $reservation->cancel();
            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([], 422);
        }
    }
}
