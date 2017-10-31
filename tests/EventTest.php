<?php
namespace Tests;

use Taniko\Scheduler\Event;
use Cake\Chronos\Chronos;

class EventTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function get()
    {
        $date  = new Chronos('2017-04-01 00:00:00');
        $event = new Event($date, $date->addHours(1), 1);
        $this->assertTrue($date->eq($event->start_at));
        $this->assertTrue($date->addHours(1)->eq($event->end_at));
        $this->assertEquals(1, $event->item);
    }
}
