<?php
namespace Taniko\Scheduler\Schedule;

use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;

class Relative extends Schedule
{
    // relative param
    const FIRST  = 1;
    const SECOND = 2;
    const THIRD  = 4;
    const FOURTH = 8;
    const LAST   = 16;

    // day of week
    const SUNDAY    = 1;
    const MONDAY    = 2;
    const TUESDAY   = 4;
    const WEDNESDAY = 8;
    const THURSDAY  = 16;
    const FRIDAY    = 32;
    const SATURDAY  = 64;

    public function __construct(int $relative_param, int $dow)
    {
        $this->type           = parent::RELATIVE;
        $this->relative_param = $relative_param;
        $this->dow            = $dow;
    }
    protected function getTimes(int $count, Chronos $since = null, Chronos $until = null) : array
    {
        $since      = $since ?? $this->datetime;
        $result     = [];
        $second     = $this->datetime->hour * 3600 + $this->datetime->minute * 60 + $this->datetime->second;
        $start_at   = $this->datetime;

        $start_at = $start_at
            ->nthOfMonth(self::s2cnth($this->relative_param), self::s2dcdow($this->dow))
            ->addSeconds($second);

        $i      = 0;
        $repeat = 0;
        $flag   = isset($until);
        $rp     = self::s2cnth($this->relative_param);
        $dow    = self::s2dcdow($this->dow);
        $flag_rpt   = isset($this->repeat);
        
        do {
            if ($flag_rpt && $repeat >= $this->repeat) {
                break;
            } elseif ($start_at->lt($since)) {
                $start_at = $start_at
                    ->addMonths($this->interval)
                    ->nthOfMonth($rp, $dow)
                    ->addSeconds($second);
                $repeat++;
                continue;
            }
            $end_at   = $start_at->addSeconds($this->time);
            if ($flag && $until->lt($end_at)) {
                break;
            }
            $result[] = [
                'start_at' => $start_at,
                'end_at'   => $end_at
            ];
            $start_at = $start_at
                ->addMonths($this->interval)
                ->nthOfMonth($rp, $dow)
                ->addSeconds($second);
            $i++;
            $repeat++;
        } while ($i < $count);
        return $result;
    }

    /**
     * convert relative param to carbon interface value
     * @param  int $value relative param
     * @throws InvalidArgumentException
     * @return int        carbon interface value
     */
    public static function s2cnth(int $value) : int
    {
        switch ($value) {
            case self::FIRST:
                $value = 1;
                break;

            case self::SECOND:
                $value = 2;
                break;

            case self::THIRD:
                $value = 3;
                break;

            case self::FOURTH:
                $value = 4;
                break;

            case self::LAST:
                $value = 5;
                break;

            default:
                throw new \InvalidArgumentException;
                break;
        }
        return $value;
    }

    /**
     * convert day of week to carbon interface value
     * @param  int $value day of week
     * @throws InvalidArgumentException
     * @return int        carbon interface value
     */
    public static function s2dcdow(int $value) : int
    {
        switch ($value) {
            case self::SUNDAY:
                $value = ChronosInterface::SUNDAY;
                break;

            case self::MONDAY:
                $value = ChronosInterface::MONDAY;
                break;

            case self::TUESDAY:
                $value = ChronosInterface::TUESDAY;
                break;

            case self::WEDNESDAY:
                $value = ChronosInterface::WEDNESDAY;
                break;

            case self::THURSDAY:
                $value = ChronosInterface::THURSDAY;
                break;

            case self::FRIDAY:
                $value = ChronosInterface::FRIDAY;
                break;

            case self::SATURDAY:
                $value = ChronosInterface::SATURDAY;
                break;

            default:
                throw new \InvalidArgumentException;
                break;
        }
        return $value;
    }
}
