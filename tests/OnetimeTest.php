<?php
namespace Tests;

use Taniko\Scheduler\Scheduler;
use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;

class OnetimeTest extends \PHPUnit\Framework\TestCase
{
    public function testItem()
    {
        $schedule = Scheduler::onetime()->item(1);
        $this->assertEquals(1, $schedule->item);
    }

    public function testOnetime()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::onetime()
            ->when($now)
            ->time(25, 1, 1);
        $end   = $now->addDays(1)->addHours(1)->addMinutes(1)->addSeconds(1);
        $items = $schedule->take(10);
        $this->assertEquals(1, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($end->eq($items[0]['end_at']));
    }

    public function testSince()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::onetime()
            ->when($now)
            ->time(1, 0, 0);
        $items = $schedule->take(10, $now->addSeconds(1));
        $this->assertEquals(0, count($items));
    }

    public function testUntil()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::onetime()
            ->when($now)
            ->time(1, 0, 0);
        $items = $schedule->take(10, null, $now->addMinutes(59));
        $this->assertEquals(0, count($items));
    }
}
