<?php
namespace Taniko\Scheduler;

use Cake\Chronos\Chronos;

class Event
{
    private $start_at;
    private $end_at;
    private $item;

    public function __construct(Chronos $start_at, Chronos $end_at, $item = null)
    {
        $this->start_at = $start_at;
        $this->end_at   = $end_at;
        $this->item     = $item;
    }

    public function __get(string $name)
    {
        return $this->{$name} ?? null;
    }
}
