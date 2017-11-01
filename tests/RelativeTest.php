<?php
namespace Tests;

use Taniko\Scheduler\Scheduler;
use Taniko\Scheduler\Schedule\Relative;
use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;

class RelativejjTest extends \PHPUnit\Framework\TestCase
{
    public function testRelative()
    {
        $now = new Chronos('2017-04-01 23:30:00');
        $schedule = Scheduler::relative(Relative::FIRST, Relative::SATURDAY)
            ->when($now)
            ->time(1, 0, 0);
        $items = $schedule->take(10);
        $this->assertEquals(10, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($now->addHours(1)->eq($items[0]['end_at']));
        $this->assertTrue(
            $now->addMonths(1)
            ->nthOfMonth(1, ChronosInterface::SATURDAY)
            ->addSeconds(23 * 3600 + 30 * 60 + 0)
            ->eq($items[1]['start_at'])
        );
        $this->assertTrue(
            $now->addMonths(1)
            ->nthOfMonth(1, ChronosInterface::SATURDAY)
            ->addSeconds(24 * 3600 + 30 * 60 + 0)
            ->eq($items[1]['end_at'])
        );
    }

    public function testInterval()
    {
        $now = new Chronos('2017-04-01 23:30:00');
        $schedule = Scheduler::relative(Relative::FIRST, Relative::SATURDAY)
            ->when($now)
            ->time(1, 0, 0)
            ->interval(2);
        $items = $schedule->take(10);
        $this->assertEquals(10, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($now->addHours(1)->eq($items[0]['end_at']));
        $this->assertTrue(
            $now->addMonths(2)
            ->nthOfMonth(1, ChronosInterface::SATURDAY)
            ->addSeconds(23 * 3600 + 30 * 60 + 0)
            ->eq($items[1]['start_at'])
        );
        $this->assertTrue(
            $now->addMonths(2)
            ->nthOfMonth(1, ChronosInterface::SATURDAY)
            ->addSeconds(24 * 3600 + 30 * 60 + 0)
            ->eq($items[1]['end_at'])
        );
    }

    public function testSince()
    {
        $now = new Chronos('2017-04-01 23:30:00');
        $schedule = Scheduler::relative(Relative::FIRST, Relative::SATURDAY)
            ->when($now)
            ->time(1, 0, 0);
        $items = $schedule->take(10, new Chronos('2017-05-06 23:30:00'));
        $this->assertEquals(10, count($items));
        $this->assertTrue(
            $now->addMonths(1)
            ->nthOfMonth(1, ChronosInterface::SATURDAY)
            ->addSeconds(23 * 3600 + 30 * 60 + 0)
            ->eq($items[0]['start_at'])
        );
        $this->assertTrue(
            $now->addMonths(1)
            ->nthOfMonth(1, ChronosInterface::SATURDAY)
            ->addSeconds(24 * 3600 + 30 * 60 + 0)
            ->eq($items[0]['end_at'])
        );
    }

    public function testUntil()
    {
        $now = new Chronos('2017-04-01 23:30:00');
        $schedule = Scheduler::relative(Relative::FIRST, Relative::SATURDAY)
            ->when($now)
            ->time(1, 0, 0);
        $items = $schedule->take(10, null, new Chronos('2017-05-06 23:30:00'));
        $this->assertEquals(1, count($items));
        $this->assertTrue($now->eq($items[0]['start_at']));
        $this->assertTrue($now->addHours(1)->eq($items[0]['end_at']));
    }
}
