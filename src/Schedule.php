<?php
namespace Taniko\Scheduler;

use Cake\Chronos\Chronos;

class Schedule
{
    // type
    const ONE_TIME = 1;
    const DAILY    = 2;
    const WEEKLY   = 3;
    const MONTHLY  = 4;

    const DEFAULT_TAKE   = 100;

    private $name     = 'Untitled';
    private $enabled  = true;
    private $type     = self::ONE_TIME;
    private $repeat   = 1;
    private $interval = 0;
    private $item     = null;
    private $start_at = null;
    private $end_at   = null;
    private $limit    = null;
    private $fillable = [
        'name',
        'enabled',
        'type',
        'repeat',
        'interval',
        'item',
        'start_at',
        'end_at',
        'limit',
    ];
    private $cast = [
        'start_at',
        'end_at',
        'limit',
    ];

    /**
     * create a new schedule
     * @param array $setting
     */
    public function __construct(array $setting = null)
    {
        foreach ($this->fillable as $key) {
            $this->{$key} = $setting[$key] ?? $this->{$key};
        }
        foreach ($this->cast as $key) {
            $this->{$key} = new Chronos($this->{$key});
        }
    }

    /**
     * reset properties
     */
    private function reset()
    {
        $this->type  = self::ONE_TIME;
    }

    /**
     * set to onetime
     * @return Schedule
     */
    public function onetime() : Schedule
    {
        $this->reset();
        $this->type = self::ONE_TIME;
        return $this;
    }

    /**
     * set to daily
     * @return Schedule
     */
    public function daily() : Schedule
    {
        $this->reset();
        $this->type = self::DAILY;
        return $this;
    }

    /**
     * set to weekly
     * @return Schedule
     */
    public function weekly() : Schedule
    {
        $this->reset();
        $this->type = self::WEEKLY;
        return $this;
    }

    /**
     * set to monthly
     * @return Schedule
     */
    public function monthly() : Schedule
    {
        $this->reset();
        $this->type  = self::MONTHLY;
        return $this;
    }

    /**
     * set start datetime and end datetime
     * @param  string   $start
     * @param  string   $end
     * @return Schedule
     */
    public function set(string $start, string $end) : Schedule
    {
        $this->start_at = new Chronos($start);
        $this->end_at   = new Chronos($end);
        return $this;
    }

    /**
     * set repeat count
     * @param  int      $repeat
     * @return Schedule
     */
    public function repeat(int $repeat) : Schedule
    {
        $this->repeat = $repeat;
        return $this;
    }

    /**
     * set interval
     * @param  int      $interval
     * @return Schedule
     */
    public function interval(int $interval) : Schedule
    {
        $this->interval = $interval >= 0 ? $interval : 0;
        return $this;
    }

    /**
     * set item
     * @param  mixed    $item
     * @return Schedule
     */
    public function item($item) : Schedule
    {
        $this->item = $item;
        return $this;
    }

    /**
     * check datetime exists in the events
     * @param  string $datetime
     * @param  int    $count
     * @return bool
     */
    public function exists(string $datetime, int $count = null) : bool
    {
        $result = false;
        $target = new Chronos($datetime);
        $events = $this->take($count);
        foreach ($events as $key => $event) {
            if ($target->between($event->start_at, $event->end_at)) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    /**
     * take events
     * @param  int $count
     * @return array array of Taniko\Scheduler\Event
     */
    public function take(int $count = null) : array
    {
        $result = [];
        $count = $count ?? self::DEFAULT_TAKE;
        switch ($this->type) {
            case self::ONE_TIME:
                $result = $this->getOnetime();
                break;

            case self::DAILY:
                $result = $this->getDaily($count);
                break;

            case self::WEEKLY:
                $result = $this->getWeekly($count);
                break;

            case self::MONTHLY:
                $result = $this->getMonthly($count);
                break;
        }
        foreach ($result as $key => $item) {
            $result[$key] = new Event(
                $item['start_at'],
                $item['end_at'],
                $this->item
            );
        }
        return $result;
    }

    /**
     * get item
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * get start time
     * @return Chronos
     */
    public function getStart() : Chronos
    {
        return $this->start_at;
    }

    /**
     * get end time
     * @return Chronos
     */
    public function getEnd() : Chronos
    {
        return $this->end_at;
    }

    /**
     * get event times
     * @param  int    $limit
     * @param  int    $interval
     * @param  string $forward
     * @return array
     */
    private function getTimes(int $limit, int $interval, string $forward) : array
    {
        $result = [];
        $count  = 0;
        $start_at  = $this->start_at;
        $end_at    = $this->end_at;
        do {
            $result[] = [
                'start_at' => $start_at,
                'end_at'   => $end_at
            ];
            $start_at = $start_at->{$forward}($interval);
            $end_at   = $end_at->{$forward}($interval);
            $count++;
        } while ($count < $limit && $count < $this->repeat);
        return $result;
    }

    /**
     * get onetime time
     * @return array
     */
    private function getOnetime() : array
    {
        return [[
            'start_at' => $this->start_at,
            'end_at'   => $this->end_at
        ]];
    }

    /**
     * get daily times
     * @param  int   $limit
     * @return array
     */
    private function getDaily(int $limit) : array
    {
        return $this->getTimes($limit, $this->interval + 1, 'addDays');
    }

    /**
     * get weekly times
     * @param  int   $limit
     * @return array
     */
    private function getWeekly(int $limit) : array
    {
        return $this->getTimes($limit, $this->interval + 1, 'addWeeks');
    }

    /**
     * get monthly times
     * @param  int   $limit
     * @return array
     */
    private function getMonthly(int $limit) : array
    {
        return $this->getTimes($limit, $this->interval + 1, 'addMonths');
    }

    /**
     * output setting as array
     * @return array
     */
    public function toArray() : array
    {
        $result = [];
        foreach ($this->fillable as $name) {
            $result[$name] = $this->{$name};
        }
        return $result;
    }
}
