<?php
namespace Tests;

use Taniko\Scheduler\Schedule;
use Cake\Chronos\Chronos;

class ScheduleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function set()
    {
        $schedule = new Schedule();
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule->onetime()->set($now, $now->addDays(1));
        $this->assertTrue($now->eq($schedule->getStart()));
        $this->assertTrue($now->addDays(1)->eq($schedule->getEnd()));
    }

    /**
     * @test
     */
    public function exists()
    {
        $schedule = new Schedule();
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule->onetime()->set($now, $now->addDays(1));
        $this->assertTrue($schedule->exists($now));
        $this->assertTrue($schedule->exists($now->addHours(23)->addMinutes(59)));
        $this->assertFalse($schedule->exists($now->addDays(2)));
    }

    /**
     * @test
     */
    public function item()
    {
        $schedule = new Schedule();
        $this->assertEquals(1, $schedule->item(1)->getItem());
    }

    /**
     * @test
     */
    public function daily()
    {
        $schedule = new Schedule();
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule->daily()->set($now, $now->addHours(1))->repeat(3)->interval(1);
        $this->assertTrue($schedule->exists($now));
        $this->assertTrue($schedule->exists($now->addHours(1)));
        $this->assertFalse($schedule->exists($now->addMinutes(61)));
        $this->assertFalse($schedule->exists($now->addDays(1)));
        $this->assertTrue($schedule->exists($now->addDays(2)));
        $this->assertEquals(3, count($schedule->take()));
    }

    /**
     * @test
     */
    public function weekly()
    {
        $schedule = new Schedule();
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule->weekly()->set($now, $now->addHours(1))->repeat(3)->interval(1);
        $this->assertTrue($schedule->exists($now));
        $this->assertTrue($schedule->exists($now->addWeeks(2)));
        $this->assertTrue($schedule->exists($now->addWeeks(4)));
        $this->assertFalse($schedule->exists($now->addWeeks(1)));
        $this->assertFalse($schedule->exists($now->addWeeks(3)));
        $this->assertEquals(3, count($schedule->take()));
    }

    /**
     * @test
     */
    public function monthly()
    {
        $schedule = new Schedule();
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule->monthly()->set($now, $now->addHours(1))->repeat(3)->interval(1);
        $this->assertTrue($schedule->exists($now));
        $this->assertTrue($schedule->exists($now->addMonths(2)));
        $this->assertTrue($schedule->exists($now->addMonths(4)));
        $this->assertFalse($schedule->exists($now->addMonths(1)));
        $this->assertFalse($schedule->exists($now->addMonths(3)));
        $this->assertEquals(3, count($schedule->take()));
    }

    /**
     * @test
     */
    public function toArray()
    {
        $schedule = new Schedule();
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule->onetime()->set($now, $now->addDays(1))->interval(1);
        $s = $schedule->toArray();
        $this->assertEquals(Schedule::ONE_TIME, $s['type']);
        $this->assertEquals(1, $s['interval']);
        $this->assertTrue($now->eq($s['start']));
        $this->assertTrue($now->addDays(1)->eq($s['end']));
    }
}
