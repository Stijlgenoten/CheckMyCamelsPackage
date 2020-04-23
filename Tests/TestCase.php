<?php

namespace Stijlgenoten\CheckMyCamels\Tests;

use Stijlgenoten\CheckMyCamels\CheckMyCamelsServiceProvider as CheckMyCamels;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // $this->artisan('migrate', ['--database' => 'testbench'])->run();
    }

    protected function getPackageProviders($app)
    {
        return [CheckMyCamels::class];
    }
}
