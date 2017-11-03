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

    protected $type     = null;
    protected $time     = null;
    protected $datetime = null;
    protected $interval = 1;
    protected $repeat   = null;
    protected $dow      = null;
    protected $relative_param = null;

    public $item;

    abstract protected function getTimes(int $count, Chronos $since = null, Chronos $until = null) : array;

    public function take(int $count = 1, string $since = null, string $until = null) : array
    {
        return $this->getTimes(
            $count,
            is_null($since) ? null : new Chronos($since),
            is_null($until) ? null : new Chronos($until)
        );
    }

    public function time(int $hour, int $minute, int $second) : Schedule
    {
        $this->time = 3600 * $hour + 60 * $minute + $second;
        return $this;
    }

    public function when(string $datetime) : Schedule
    {
        $this->datetime = new Chronos($datetime);
        return $this;
    }

    public function item($item) : Schedule
    {
        $this->item = $item;
        return $this;
    }

    public function interval(int $interval) : Schedule
    {
        $this->interval = $interval;
        return $this;
    }

    public function repeat(int $repeat) : Schedule
    {
        $this->repeat = $repeat;
        return $this;
    }
}
