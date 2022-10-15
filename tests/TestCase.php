<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Mockery;
use PHPUnit\Framework\Assert;
use Illuminate\Database\Eloquent\Collection;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp() : void
    {
        parent::setUp();

        // Instruct mockery to disable mocking for methods that don't exist
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);

        // When used in a closure the $this keyword will refer to the class of the method that
        // accepts the closure as a param, in this case TestResponse.
        TestResponse::macro('data', function($key) {
            return $this->original->getData()[$key];
        });

        // Phpunit assertions are all static methods so we can call them directly from the class.
        // $this context refers to Collection.
        Collection::macro('assertContains', function($value) {
            Assert::assertTrue(
                $this->contains($value), 
                "Failed asserting that the collection contained the specified value"
            );
        });

        Collection::macro('assertNotContains', function($value) {
            Assert::assertFalse(
                $this->contains($value),
                "Failed asserting that the collection does not contain the specified value"
            );
        });

        // We can zip the two collections together which results in a 2D array with each internal
        // element composed of the two elements at the same positions in the corresponding collections.
        // We then check each pair to see if they are equal. $this refers to the collection.
        Collection::macro('assertEquals', function($items) {
            Assert::assertEquals(count($this), count($items));
            $this->zip($items)->each(function($pair) {
                list($a, $b) = $pair;
                Assert::assertTrue($a->is($b));
            });
        });
    }

    protected function fromURL(string $url) : self
    {
        session()->setPreviousUrl($url);
        return $this;
    }
}
