<?php

namespace Tests\Unit\Mail;

use App\Mail\OrderConfirmationEmail as MailOrderConfirmationEmail;
use App\Models\Order;
use Illuminate\Mail\Mailable;
use Tests\TestCase;

class OrderConfirmationEmailTest extends TestCase
{
    /**
     *  @test
     */
    public function email_contains_a_link_to_the_order_confirmation_page()
    {
        $order = Order::factory()->make([
            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);

        $email = new MailOrderConfirmationEmail($order);

        $rendered = $email->render();

        $this->assertStringContainsString(url('/orders/ORDERCONFIRMATION1234'), $rendered);
    }

    /**
     *  @test
     */
    public function the_email_has_a_subject()
    {
        $order = Order::factory()->make();

        $email = new MailOrderConfirmationEmail($order);

        $this->assertEquals("Your ticket(s) order", $email->build()->subject);
    }

    /**
     *  A helper to render the email to get the html.
     *  This is not necessary in modern versions of laravel since 
     *  there is a render method built in.
     *  @param Illuminate\Mail\Mailable
     *  @return string
     */
    private function render(Mailable $mailable) : string
    {
        $mailable->build();

        return view($mailable->view, $mailable->buildViewData())->render();
    }
}