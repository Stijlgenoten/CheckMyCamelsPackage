<?php

namespace Stijlgenoten\CheckMyCamels\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Stijlgenoten\CheckMyCamels\CheckMyCamels;
use Stijlgenoten\CheckMyCamels\Tests\TestCase;

class ArtisanCommandTest extends TestCase
{
    
    /** @test */
    public function the_actual_command_is_loaded()
    {
        $this->assertTrue(class_exists( \Stijlgenoten\CheckMyCamels\Commands\CheckMyCamelsCommand::class));
    }

 	/** @test */
    public function the_command_is_executable()
    {
    	$this->artisan('check-my-camels')
			->expectsOutput('Checking...')
			->expectsOutput(' > No strange camels in the house!')
			->expectsOutput('Done!')
			->assertExitCode(0);
    }
}