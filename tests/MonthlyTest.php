<?php
namespace Tests;

use Taniko\Scheduler\Scheduler;
use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;

class MonthlyTest extends \PHPUnit\Framework\TestCase
{
    public function testMonthly()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::monthly()->when($now)->time(1, 0, 0);
        $items    = $schedule->take(10);

        $this->assertEquals(10, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($now->addHours(1)->eq($items[0]['end_at']));
        $this->assertTrue($now->addMonths(1)->eq($items[1]['start_at']));
        $this->assertTrue($now->addMonths(1)->addHours(1)->eq($items[1]['end_at']));
    }

    public function testInterval()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::monthly()->when($now)->time(1, 0, 0)->interval(2);
        $items    = $schedule->take(10);

        $this->assertEquals(10, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($now->addHours(1)->eq($items[0]['end_at']));
        $this->assertTrue($now->addMonths(2)->eq($items[1]['start_at']));
        $this->assertTrue($now->addMonths(2)->addHours(1)->eq($items[1]['end_at']));
    }

    public function testSince()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::monthly()->when($now)->time(1, 0, 0);
        $items    = $schedule->take(10, $now->addSeconds(1));

        $this->assertEquals(10, count($items));
        $this->assertTrue($now->addMonths(1)->eq($items[0]['start_at']));
        $this->assertTrue($now->addMonths(1)->addHours(1)->eq($items[0]['end_at']));
    }

    public function testUntil()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::monthly()->when($now)->time(1, 0, 0);
        $items = $schedule->take(10, null, $now->addMonths(1)->addHours(2));

        $this->assertEquals(2, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($now->addHours(1)->eq($items[0]['end_at']));
        $this->assertTrue($now->addMonths(1)->eq($items[1]['start_at']));
        $this->assertTrue($now->addMonths(1)->addHours(1)->eq($items[1]['end_at']));
    }
}
