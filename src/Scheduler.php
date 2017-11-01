<?php
namespace Taniko\Scheduler;

use Cake\Chronos\Chronos;
use Taniko\Scheduler\Schedule\Schedule;
use Taniko\Scheduler\Schedule\{
    Onetime,
    Daily,
    Weekly,
    Monthly,
    Relative
};

class Scheduler
{
    const DEFAULT_TAKE   = 100;

    private $schedules = [];

    public function add(Schedule $s)
    {
        $this->schedules[] = $s;
    }

    public static function onetime() : Onetime
    {
        return new Onetime();
    }

    public static function daily() : Daily
    {
        return new Daily();
    }

    public static function weekly() : Weekly
    {
        return new Weekly();
    }

    public static function monthly() : Monthly
    {
        return new Monthly;
    }

    public static function relative(int $relative_param, int $dow) : Relative
    {
        return new Relative($relative_param, $dow);
    }


    public function getSchedules() : array
    {
        return $this->schedules;
    }

    public function exists(Chronos $target, Chronos $since = null, Chronos $until = null, int $limit = null) : bool
    {
        $result = false;
        $items  = $this->take($limit, $since, $until);
        foreach ($items as $key => $item) {
            if ($target->between($item['start_at'], $item['end_at'])) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    public function take(int $limit = null, Chronos $since = null, Chronos $until = null) : array
    {
        $result = [];
        $limit  = $limit ?? self::DEFAULT_TAKE;
        foreach ($this->schedules as $key => $schedule) {
            $result = array_merge($result, $schedule->take($limit));
        }
        usort($result, function ($a, $b) {
            if ($a['start_at']->eq($b['start_at'])) {
                if ($a['end_at']->eq($b['end_at'])) {
                    return 0;
                }
                return $a['end_at']->gte($b['end_at']) ? 1 : -1;
            }
            return $a['start_at']->gte($b['start_at']) ? 1 : -1;
        });
        return array_slice($result, 0, $limit);
    }
}
