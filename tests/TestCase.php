<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\EventSourcing\Projections\Projection;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->mock(Projection::class, function ($mock) {
            $mock->shouldReceive('isWriteable')->andReturnTrue();
        });
    }
}
