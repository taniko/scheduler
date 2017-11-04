<?php
namespace Taniko\Scheduler\Schedule;

use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;

abstract class Schedule
{
    // type
    const ONETIME  = 1;
    const DAILY    = 2;
    const WEEKLY   = 3;
    const MONTHLY  = 4;
    const RELATIVE = 5;

    private $fillable = [
        'type',
        'time',
        'datetime',
        'interval',
        'repeat',
        'dow',
        'relative_param',
    ];

    protected $type     = null;
    protected $time     = null;
    protected $datetime = null;
    protected $interval = 1;
    protected $repeat   = null;
    protected $dow      = null;
    protected $relative_param = null;

    public $item;

    abstract protected function getTimes(int $count, Chronos $since = null, Chronos $until = null) : array;

    /**
     * get times
     * @param  int    $count  maximum number of items to take
     * @param  Cake\Chronos\Chronos|null $since  start date of search range
     * @param  Cake\Chronos\Chronos|null $until  end date of search range
     * @return array
     */
    public function take(int $count = 1, string $since = null, string $until = null) : array
    {
        return $this->getTimes(
            $count,
            is_null($since) ? null : new Chronos($since),
            is_null($until) ? null : new Chronos($until)
        );
    }

    /**
     * set schedule time
     * @param  int      $hour   hours
     * @param  int      $minute minutes
     * @param  int      $second seconds
     * @return Taniko\Scheduler\Schedule\Schedule
     */
    public function time(int $hour, int $minute, int $second) : Schedule
    {
        $this->time = 3600 * $hour + 60 * $minute + $second;
        return $this;
    }

    /**
     * set schedule start time
     * @param  string   $datetime start datetime
     * @return Taniko\Scheduler\Schedule\Schedule
     */
    public function when(string $datetime) : Schedule
    {
        $this->datetime = new Chronos($datetime);
        return $this;
    }

    /**
     * set schedule item
     * @param  mixed   $item schedule item
     * @return Taniko\Scheduler\Schedule\Schedule
     */
    public function item($item) : Schedule
    {
        $this->item = $item;
        return $this;
    }

    /**
     * set number of schedule interval
     * @param  int      $interval schedule interval
     * @return Taniko\Scheduler\Schedule\Schedule
     */
    public function interval(int $interval) : Schedule
    {
        $this->interval = $interval;
        return $this;
    }

    /**
     * set number of repeat
     * @param  int      $repeat number of repeat
     * @return Taniko\Scheduler\Schedule\Schedule
     */
    public function repeat(int $repeat) : Schedule
    {
        $this->repeat = $repeat;
        return $this;
    }

    /**
     * get schedule parameters as array
     * @return array schedule params
     */
    public function toArray() : array
    {
        $result = [];
        foreach ($this->fillable as $name) {
            $result[$name] = $this->{$name};
        }
        return $result;
    }

    /**
     * set schedule parameters from array
     * @param  array    $params schedule parameters
     * @return Taniko\Scheduler\Schedule\Schedule
     */
    public function init(array $params) : Schedule
    {
        foreach ($this->fillable as $name) {
            $this->{$name} = $params[$name] ?? null;
        }
        return $this;
    }
}
