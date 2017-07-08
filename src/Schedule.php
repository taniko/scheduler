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
    const DEFAULT_REPEAT = 50;

    private $type     = self::ONE_TIME;
    private $repeat   = self::DEFAULT_REPEAT;
    private $interval = 0;
    private $relative = 0;
    private $param    = 0;
    private $item     = null;
    private $limit    = null;
    private $start    = null;
    private $end      = null;

    /**
     * create a new schedule
     * @param array $setting
     */
    public function __construct(array $setting = null)
    {
        $this->type     = $setting['type']      ?? $this->type;
        $this->repeat   = $setting['repeat']    ?? $this->repeat;
        $this->interval = $setting['interval']  ?? $this->interval;
        $this->relative = $setting['relative']  ?? $this->relative;
        $this->param    = $setting['param']     ?? $this->param;
        $this->item     = $setting['item']      ?? $this->item;
        $this->limit    = $setting['limit']     ?? $this->limit;
        $this->start    = is_a($setting['start'], Chronos::class) ? $setting['start'] : null;
        $this->end      = is_a($setting['end'], Chronos::class)   ? $setting['end']   : null;
    }

    /**
     * reset properties
     */
    private function reset()
    {
        $this->type  = self::ONE_TIME;
        $this->param = 0;
        $this->relative = 0;
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
        $this->start = new Chronos($start);
        $this->end   = new Chronos($end);
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
     * @param  int    $limit
     * @return bool
     */
    public function exists(string $datetime, int $limit = null) : bool
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

    /**
     * take events
     * @param  int $limit
     * @return array array of Taniko\Scheduler\Event
     */
    public function take(int $limit = null) : array
    {
        $result = [];
        $limit = $limit ?? self::DEFAULT_TAKE;
        switch ($this->type) {
            case self::ONE_TIME:
                $result = $this->getOnetime();
                break;

            case self::DAILY:
                $result = $this->getDaily($limit);
                break;

            case self::WEEKLY:
                $result = $this->getWeekly($limit);
                break;

            case self::MONTHLY:
                $result = $this->getMonthly($limit);
                break;
        }
        foreach ($result as $key => $item) {
            $result[$key] = new Event(
                $item['start'],
                $item['end'],
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
        return $this->start;
    }

    /**
     * get end time
     * @return Chronos
     */
    public function getEnd() : Chronos
    {
        return $this->end;
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
        $start  = $this->start;
        $end    = $this->end;
        do {
            $result[] = [
                'start' => $start,
                'end'   => $end
            ];
            $start = $start->{$forward}($interval);
            $end   = $end->{$forward}($interval);
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
            'start' => $this->start,
            'end'   => $this->end
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
        return [
            'type'      => $this->type,
            'repeat'    => $this->repeat,
            'interval'  => $this->interval,
            'relative'  => $this->relative,
            'param'     => $this->param,
            'item'      => $this->item,
            'limit'     => $this->limit,
            'start'     => $this->start,
            'end'       => $this->end
        ];
    }
}
