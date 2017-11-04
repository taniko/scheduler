<?php
namespace Taniko\Scheduler\Schedule;

use Cake\Chronos\Chronos;

class Weekly extends Schedule
{
    public function __construct()
    {
        $this->type = parent::WEEKLY;
    }
    
    /**
     * get times
     * @param  int    $count  maximum number of items to take
     * @param  Cake\Chronos\Chronos|null $since  start date of search range
     * @param  Cake\Chronos\Chronos|null $until  end date of search range
     * @return array
     */
    protected function getTimes(int $count, Chronos $since = null, Chronos $until = null) : array
    {
        $result = [];
        $start_at = $this->datetime;
        $end_at   = $this->datetime->addSeconds($this->time);
        $repeat   = 0;

        // advance time to $since
        if (isset($since)) {
            while ($start_at->lt($since)) {
                $start_at = $start_at->addWeeks($this->interval);
                $end_at   = $end_at->addWeeks($this->interval);
                $repeat++;
            }
        }

        $i = 0;
        $flag_until = isset($until);
        $flag_rpt   = isset($this->repeat);
        do {
            if ($flag_rpt && $repeat >= $this->repeat || $flag_until && $until->lt($end_at)) {
                break;
            }
            $result[] = [
                'start_at' => $start_at,
                'end_at'   => $end_at
            ];
            $start_at = $start_at->addWeeks($this->interval);
            $end_at   = $end_at->addWeeks($this->interval);
            $i++;
            $repeat++;
        } while ($i < $count);
        return $result;
    }
}
