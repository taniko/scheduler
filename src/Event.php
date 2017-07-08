<?php
namespace Taniko\Scheduler;

use Cake\Chronos\Chronos;

class Event
{
    private $start;
    private $end;
    private $item;

    public function __construct(Chronos $start, Chronos $end, $item = null)
    {
        $this->start = $start;
        $this->end   = $end;
        $this->item  = $item;
    }

    public function __get(string $name)
    {
        return $this->{$name} ?? null;
    }
}
