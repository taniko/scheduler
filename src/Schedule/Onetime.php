<?php
namespace Taniko\Scheduler\Schedule;

use Cake\Chronos\Chronos;

class Onetime extends Schedule
{
    public function __construct()
    {
        $this->type = parent::ONETIME;
    }
    protected function getTimes(int $count, Chronos $since = null, Chronos $until = null) : array
    {
        $end = $this->datetime->addSeconds($this->time);
        if (!is_null($since) && $since->gt($this->datetime)) {
            return [];
        } elseif (!is_null($until) && $until->lt($end)) {
            return [];
        } else {
            return [[
                'start_at' => $this->datetime,
                'end_at'   => $end,
            ]];
        }
    }
}
