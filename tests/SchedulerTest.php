<?php
namespace Tests;

use Cake\Chronos\Chronos;
use Taniko\Scheduler\Scheduler;
use Taniko\Scheduler\Schedule\Onetime;

class SchedulerTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSchedules()
    {
        $scheduler = new Scheduler();
        $date      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::onetime()
            ->when($date)
            ->time(1, 0, 0);
        $scheduler->add($schedule);
        $this->assertEquals(1, count($scheduler->getSchedules()));
    }

    public function testTake()
    {
        $scheduler = new Scheduler();

        $date      = new Chronos('2017-04-01 00:00:00');
        $scheduler->add(Scheduler::weekly()->when($date)->time(1, 0, 0));

        $date      = new Chronos('2017-04-01 00:00:00');
        $scheduler->add(Scheduler::weekly()->when($date)->time(1, 0, 0));

        $date      = new Chronos('2017-04-02 00:00:00');
        $scheduler->add(Scheduler::weekly()->when($date)->time(1, 0, 0));

        $date      = new Chronos('2017-04-02 00:00:00');
        $scheduler->add(Scheduler::weekly()->when($date)->time(0, 30, 0));

        $items = $scheduler->take(8);
        $this->assertEquals(8, count($items));
        $this->assertTrue($items[0]['start_at']->lte($items[1]['start_at']));
    }

    public function testExists()
    {
        $scheduler = new Scheduler();

        $date      = new Chronos('2017-04-01 00:00:00');
        $schedule  = Scheduler::weekly()
            ->when($date)
            ->time(1, 0, 0);
        $scheduler->add($schedule);

        $date      = new Chronos('2017-04-02 00:00:00');
        $schedule  = Scheduler::weekly()
            ->when($date)
            ->time(1, 0, 0);
        $scheduler->add($schedule);

        $target = new Chronos('2017-04-01 00:00:00');
        $this->assertTrue($scheduler->exists($target));
        $this->assertTrue($scheduler->exists($target->addDays(1)));
        $this->assertFalse($scheduler->exists($target->addDays(2)));
        $this->assertTrue($scheduler->exists($target->addWeeks(1)));
    }

    public function testFromArray()
    {
        $now      = new Chronos('2017-04-01 00:00:00');
        $schedule = Scheduler::onetime()->when($now)->time(1, 0, 0)->item(1);
        $params   = $schedule->toArray();
        $schedule = Scheduler::fromArray($params);
        $this->assertEquals(Onetime::class, get_class($schedule));
    }
}
