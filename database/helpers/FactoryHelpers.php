<?php

namespace Database\Helpers;

use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;

class FactoryHelpers
{
    /**
     *  Create a published concert
     *  @param array $overrides
     *  @return Concert
     */
    public static function createPublished(array $overrides = []) : Concert
    {
        $concert = Concert::factory()->create($overrides);
        $concert->publish();
        return $concert;
    }

    /**
     *  Create an unpublished concert
     *  @param array $overrides
     *  @return Concert
     */
    public static function createUnpublished(array $overrides = []) : Concert
    {
        $concert = Concert::factory()->unpublished()->create($overrides);
        return $concert;
    }

    /**
     *  Create an order with a given amount of tickets for a given concert
     *  @param Concert $concert
     *  @param array $overrides
     *  @param int $ticketQuantity
     *  @return Order
     */
    public static function createOrderForConcert(
        Concert $concert, array $overrides = [], int $ticketQuantity = 1
    ) : Order
    {
        $order = Order::factory()->create($overrides);

        Ticket::factory()->count($ticketQuantity)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id
        ]);

        return $order;
    }
}