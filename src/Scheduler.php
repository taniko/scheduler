<?php
namespace Taniko\Scheduler;

use Cake\Chronos\Chronos;
use Taniko\Scheduler\Schedule\{
    Schedule,
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

    /**
     * add Schedule
     * @param Taniko\Scheduler\Schedule\Schedule $schedule schedule instance
     */
    public function add(Schedule $schedule)
    {
        $this->schedules[] = $schedule;
    }

    /**
     * generate onetime schedule instance
     * @return Taniko\Scheduler\Schedule\Onetime onetime schedule
     */
    public static function onetime() : Onetime
    {
        return new Onetime();
    }

    /**
     * generate daily schedule instance
     * @return Taniko\Scheduler\Schedule\Daily daily schedule
     */
    public static function daily() : Daily
    {
        return new Daily();
    }

    /**
     * generate wkkely schedule instance
     * @return Taniko\Scheduler\Schedule\Weekly wkkely schedule
     */
    public static function weekly() : Weekly
    {
        return new Weekly();
    }

    /**
     * generate monthly schedule instance
     * @return Taniko\Scheduler\Schedule\Monthly monthly schedule
     */
    public static function monthly() : Monthly
    {
        return new Monthly;
    }

    /**
     * generate ralative schedule instance
     * @param  int      $relative_param relative param
     * @param  int      $dow            day of week
     * @return Taniko\Scheduler\Schedule\Relative
     */
    public static function relative(int $relative_param, int $dow) : Relative
    {
        return new Relative($relative_param, $dow);
    }

    /**
     * get schedules
     * @return Taniko\Scheduler\Schedule\Schedule[]
     */
    public function getSchedules() : array
    {
        return $this->schedules;
    }

    /**
     * check if there is schedule at the target time
     * @param  Cake\Chronos\Chronos $target target datetime
     * @param  Cake\Chronos\Chronos|null  $since  start date of search range
     * @param  Cake\Chronos\Chronos|null  $until  end date of search range
     * @param  int|null      $limit  maximum number of items to search
     * @return bool
     */
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

    /**
     * get times from added schedules
     * @param  int|null $limit maximum number of items to take
     * @param  Cake\Chronos\Chronos|null  $since  start date of search range
     * @param  Cake\Chronos\Chronos|null  $until  end date of search range
     * @return array
     */
    public function take(int $limit = null, Chronos $since = null, Chronos $until = null) : array
    {
        $result = [];
        $limit  = $limit ?? self::DEFAULT_TAKE;
        foreach ($this->schedules as $key => $schedule) {
            $result = array_merge($result, $schedule->take($limit, $since, $until));
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

    /**
     * generate schedule instance from array
     * @param  array    $params schedule parameters
     * @return Taniko\Scheduler\Schedule\Schedule
     */
    public static function fromArray(array $params) : Schedule
    {
        $schedule = null;
        switch ($params['type']) {
            case Schedule::ONETIME:
                $schedule = self::onetime()->init($params);
                break;

            case Schedule::DAILY:
                $schedule = self::daily()->init($params);
                break;

            case Schedule::WEEKLY:
                $schedule = self::weekly()->init($params);
                break;

            case Schedule::MONTHLY:
                $schedule = self::monthly()->init($params);
                break;

            case Schedule::RELATIVE:
                $schedule = self::relative()->init($params);
                break;

            default:
                throw new \InvalidArgumentException;
                break;
        }
        return $schedule;
    }
}
