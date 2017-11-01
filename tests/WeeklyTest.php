<?php
namespace Tests;

use Taniko\Scheduler\Scheduler;
use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;

class WeeklyTest extends \PHPUnit\Framework\TestCase
{
    public function testWeekly()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::weekly()->when($now)->time(1, 0, 0);
        $items    = $schedule->take(10);

        $this->assertEquals(10, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($now->addHours(1)->eq($items[0]['end_at']));
        $this->assertTrue($now->addDays(7)->eq($items[1]['start_at']));
        $this->assertTrue($now->addDays(7)->addHours(1)->eq($items[1]['end_at']));
    }

    public function testInterval()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::weekly()->when($now)->time(1, 0, 0)->interval(2);
        $items    = $schedule->take(10);

        $this->assertEquals(10, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($now->addHours(1)->eq($items[0]['end_at']));
        $this->assertTrue($now->addDays(14)->eq($items[1]['start_at']));
        $this->assertTrue($now->addDays(14)->addHours(1)->eq($items[1]['end_at']));
    }

    public function testSince()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::weekly()->when($now)->time(1, 0, 0);
        $items    = $schedule->take(10, $now->addSeconds(1));

        $this->assertEquals(10, count($items));
        $this->assertTrue($now->addWeeks(1)->eq($items[0]['start_at']));
        $this->assertTrue($now->addWeeks(1)->addHours(1)->eq($items[0]['end_at']));
    }

    public function testUntil()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::weekly()->when($now)->time(1, 0, 0);
        $items = $schedule->take(10, null, $now->addDays(8)->addHours(2));

        $this->assertEquals(2, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($now->addHours(1)->eq($items[0]['end_at']));
        $this->assertTrue($now->addWeeks(1)->eq($items[1]['start_at']));
        $this->assertTrue($now->addWeeks(1)->addHours(1)->eq($items[1]['end_at']));
    }
}
