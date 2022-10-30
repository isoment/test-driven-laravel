<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(string $confirmationNumber) : View
    {
        $order = Order::findByConfirmationNumber($confirmationNumber);

        return view('orders.show', [
            'order' => $order
        ]);
    }
}
