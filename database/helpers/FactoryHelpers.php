<?php

namespace Database\Helpers;

use App\Models\Concert;

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
}