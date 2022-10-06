<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp() : void
    {
        parent::setUp();

        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
    }

    protected function fromURL(string $url) : self
    {
        session()->setPreviousUrl($url);
        return $this;
    }
}
