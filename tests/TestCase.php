<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp() : void
    {
        parent::setUp();

        TestResponse::macro('data', function($key) {
            return $this->original->getData()[$key];
        });

        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
    }

    protected function fromURL(string $url) : self
    {
        session()->setPreviousUrl($url);
        return $this;
    }
}
