<?php
namespace Taniko\Scheduler\Schedule;

use Cake\Chronos\Chronos;

class Weekly extends Schedule
{
    public function __construct()
    {
        $this->type = parent::WEEKLY;
    }
    protected function getTimes(int $count, Chronos $since = null, Chronos $until = null) : array
    {
        $result = [];
        $start_at = $this->datetime;
        $end_at   = $this->datetime->addSeconds($this->time);

        // advance time to $since
        if (isset($since)) {
            while ($start_at->lt($since)) {
                $start_at = $start_at->addWeeks($this->interval);
                $end_at   = $end_at->addWeeks($this->interval);
            }
        }

        $i = 0;
        $flag = isset($until);
        do {
            if ($flag && $until->lt($end_at)) {
                break;
            }
            $result[] = [
                'start_at' => $start_at,
                'end_at'   => $end_at
            ];
            $start_at = $start_at->addWeeks($this->interval);
            $end_at   = $end_at->addWeeks($this->interval);
            $i++;
        } while ($i < $count);
        return $result;
    }
}
