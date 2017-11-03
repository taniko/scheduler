<?php
namespace Tests;

use Cake\Chronos\Chronos;
use Taniko\Scheduler\Scheduler;
use Taniko\Scheduler\Schedule\Schedule;
use Taniko\Scheduler\Schedule\Onetime;

class ScheduleTest extends \PHPUnit\Framework\TestCase
{
    public function testItem()
    {
        $schedule = Scheduler::onetime()->item(1);
        $this->assertEquals(1, $schedule->item);
    }

    public function testToArray()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::onetime()->when($now)->time(1, 0, 0)->item(1);
        $params   = $schedule->toArray();
        $this->assertEquals(Schedule::ONETIME, $params['type']);
        $this->assertTrue($now->eq($params['datetime']));
    }
}
