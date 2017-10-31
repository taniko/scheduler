<?php
namespace Tests;

use Cake\Chronos\Chronos;
use Taniko\Scheduler\Schedule;
use Taniko\Scheduler\Scheduler;

class SchedulerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function schedules()
    {
        $scheduler = new Scheduler();
        $schedule  = new Schedule();
        $date      = new Chronos('2017-04-01 00:00:00');
        $schedule->weekly()->set($date, $date->addHours(1))->repeat(3)->interval(1);
        $scheduler->add($schedule);
        $this->assertEquals(1, count($scheduler->getSchedules()));
    }

    /**
     * @test
     */
    public function exists()
    {
        $scheduler = new Scheduler();

        $date      = new Chronos('2017-04-01 00:00:00');
        $schedule  = new Schedule();
        $schedule->weekly()->set($date, $date->addHours(1))->repeat(3);
        $scheduler->add($schedule);

        $date      = new Chronos('2017-04-02 00:00:00');
        $schedule  = new Schedule();
        $schedule->weekly()->set($date, $date->addHours(1))->repeat(3);
        $scheduler->add($schedule);

        $target = new Chronos('2017-04-01 00:00:00');
        $this->assertTrue($scheduler->exists($target));
        $this->assertTrue($scheduler->exists($target->addDays(1)));
        $this->assertFalse($scheduler->exists($target->addDays(2)));
        $this->assertTrue($scheduler->exists($target->addWeeks(1)));
    }

    /**
     * @test
     */
    public function take()
    {
        $scheduler = new Scheduler();

        $date      = new Chronos('2017-04-01 00:00:00');
        $schedule  = new Schedule();
        $schedule->weekly()->set($date, $date->addHours(1))->repeat(3);
        $scheduler->add($schedule);

        $date      = new Chronos('2017-04-02 00:00:00');
        $schedule  = new Schedule();
        $schedule->weekly()->set($date, $date->addHours(1))->repeat(3);
        $scheduler->add($schedule);

        $date      = new Chronos('2017-04-02 00:00:00');
        $schedule  = new Schedule();
        $schedule->weekly()->set($date, $date->addHours(1))->repeat(1);
        $scheduler->add($schedule);

        $date      = new Chronos('2017-04-02 00:00:00');
        $schedule  = new Schedule();
        $schedule->weekly()->set($date, $date->addHours(2))->repeat(1);
        $scheduler->add($schedule);

        $events = $scheduler->take();
        $this->assertEquals(8, count($events));
        $this->assertTrue($events[0]->start_at->lte($events[1]->start_at));
    }
}
