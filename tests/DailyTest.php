<?php
namespace Tests;

use Taniko\Scheduler\Scheduler;
use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;

class DailytTest extends \PHPUnit\Framework\TestCase
{
    public function testEveryDay()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::daily()->when($now)->time(1, 0, 0);
        $items = $schedule->take(10);
        $this->assertEquals(10, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($now->addHours(1)->eq($items[0]['end_at']));
        $this->assertTrue($now->addDays(9)->eq($items[9]['start_at']));
        $this->assertTrue($now->addDays(9)->addHours(1)->eq($items[9]['end_at']));
    }

    public function testRepeat()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::daily()->when($now)->time(1, 0, 0)->repeat(2);
        $items = $schedule->take(10);
        $this->assertEquals(2, count($items));
    }

    public function testInterval()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::daily()->when($now)->time(1, 0, 0)->interval(2);
        $items = $schedule->take(10);
        $this->assertEquals(10, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($now->addHours(1)->eq($items[0]['end_at']));
        $this->assertTrue($now->addDays(18)->eq($items[9]['start_at']));
        $this->assertTrue($now->addDays(18)->addHours(1)->eq($items[9]['end_at']));
    }

    public function testSince()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::daily()->when($now)->time(1, 0, 0);
        $items = $schedule->take(10, $now->addSeconds(1));

        $this->assertEquals(10, count($items));
        $this->assertTrue($now->addDays(1)->eq($items[0]['start_at']));
        $this->assertTrue($now->addDays(1)->addHours(1)->eq($items[0]['end_at']));
        $this->assertTrue($now->addDays(10)->eq($items[9]['start_at']));
        $this->assertTrue($now->addDays(10)->addHours(1)->eq($items[9]['end_at']));
    }

    public function testUntil()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::daily()->when($now)->time(1, 0, 0);
        $items = $schedule->take(10, null, $now->addDays(8)->addHours(2));

        $this->assertEquals(9, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($now->addHours(1)->eq($items[0]['end_at']));
        $this->assertTrue($now->addDays(8)->eq($items[8]['start_at']));
        $this->assertTrue($now->addDays(8)->addHours(1)->eq($items[8]['end_at']));
    }
}
