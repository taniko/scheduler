<?php
namespace Taniko\Scheduler;

use Cake\Chronos\Chronos;

class Scheduler
{
    const DEFAULT_TAKE   = 100;

    private $schedules = [];

    public function add(Schedule $s)
    {
        $this->schedules[] = $s;
    }

    public function getSchedules() : array
    {
        return $this->schedules;
    }

    public function exists(string $datetime, $limit = null) : bool
    {
        $result = false;
        $target = new Chronos($datetime);
        $events = $this->take($limit);
        foreach ($events as $key => $event) {
            if ($target->between($event->start, $event->end)) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    public function take(int $limit = null) : array
    {
        $result = [];
        $limit  = $limit ?? self::DEFAULT_TAKE;
        foreach ($this->schedules as $key => $schedule) {
            $result = array_merge($result, $schedule->take($limit));
        }
        usort($result, function ($a, $b) {
            if ($a->start->eq($b->start)) {
                if ($a->end->eq($b->end)) {
                    return 0;
                }
                return $a->end->gte($b->end) ? 1 : -1;
            }
            return $a->start->gte($b->start) ? 1 : -1;
        });
        return $result;
    }
}
