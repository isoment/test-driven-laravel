<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RandomOrderConfirmationNumberGeneratorTest extends TestCase
{
    /*
        An order confirmation should contain uppercase letters and numbers that are not ambiguous

            ABCDEFGHJKLMNPQRSTUVWXYZ
            23456789

        Must be 24 chars long

        All of them must be unique
    */
}