<?php

namespace AdvancedNotifications\Tests\Feature;

use AdvancedNotifications\Tests\TestCase;
use AdvancedNotifications\Services\ChannelManagerService;

class ExampleTest extends TestCase
{
    /** @test */
    public function it_can_instantiate_service()
    {
        $service = app('advanced-notifications');
        $this->assertInstanceOf(ChannelManagerService::class, $service);
    }

    /** @test */
    public function it_loads_config()
    {
        $this->assertNotNull(config('advanced-notifications'));
    }
}
