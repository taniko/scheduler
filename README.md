# scheduler
[![Build Status](https://travis-ci.org/taniko/scheduler.svg?branch=master)](https://travis-ci.org/taniko/scheduler)

PHP Scheduler. Support onetime, daily, weekly, monthly, and relative schedule.

## installation
```sh
composer require taniko/scheduler
```

## Usage
```php
<?php
require 'vendor/autoload.php';
use Cake\Chronos\Chronos;
use Taniko\Scheduler\Scheduler;
use Taniko\Scheduler\Schedule\Relative;

$date     = new Chronos('2017-04-01 00:00:00');
$schedule = Scheduler::weekly()
    ->when($date)
    ->time(1, 0, 0)
    ->interval(2)
    ->repeat(3);
$items = $schedule->take(10);

$schedule = Scheduler::relative(Relative::FIRST, Relative::SATURDAY)
    ->when($date)
    ->time(1, 0, 0);
$items = $schedule->take(3);

```
You can get start_at and end_at like below
```
# weekly
Array
(
    [0] => Array
        (
            [start_at] => Cake\Chronos\Chronos Object
                (
                    [time] => 2017-04-01 00:00:00.000000
                    [timezone] => Asia/Tokyo
                    [hasFixedNow] =>
                )

            [end_at] => Cake\Chronos\Chronos Object
                (
                    [time] => 2017-04-01 01:00:00.000000
                    [timezone] => Asia/Tokyo
                    [hasFixedNow] =>
                )

        )

    [1] => Array
        (
            [start_at] => Cake\Chronos\Chronos Object
                (
                    [time] => 2017-04-15 00:00:00.000000
                    [timezone] => Asia/Tokyo
                    [hasFixedNow] =>
                )

            [end_at] => Cake\Chronos\Chronos Object
                (
                    [time] => 2017-04-15 01:00:00.000000
                    [timezone] => Asia/Tokyo
                    [hasFixedNow] =>
                )

        )

    [2] => Array
        (
            [start_at] => Cake\Chronos\Chronos Object
                (
                    [time] => 2017-04-29 00:00:00.000000
                    [timezone] => Asia/Tokyo
                    [hasFixedNow] =>
                )

            [end_at] => Cake\Chronos\Chronos Object
                (
                    [time] => 2017-04-29 01:00:00.000000
                    [timezone] => Asia/Tokyo
                    [hasFixedNow] =>
                )

        )

)

# relative
Array
(
    [0] => Array
        (
            [start_at] => Cake\Chronos\Chronos Object
                (
                    [time] => 2017-04-01 00:00:00.000000
                    [timezone] => Asia/Tokyo
                    [hasFixedNow] =>
                )

            [end_at] => Cake\Chronos\Chronos Object
                (
                    [time] => 2017-04-01 01:00:00.000000
                    [timezone] => Asia/Tokyo
                    [hasFixedNow] =>
                )

        )

    [1] => Array
        (
            [start_at] => Cake\Chronos\Chronos Object
                (
                    [time] => 2017-05-06 00:00:00.000000
                    [timezone] => Asia/Tokyo
                    [hasFixedNow] =>
                )

            [end_at] => Cake\Chronos\Chronos Object
                (
                    [time] => 2017-05-06 01:00:00.000000
                    [timezone] => Asia/Tokyo
                    [hasFixedNow] =>
                )

        )

    [2] => Array
        (
            [start_at] => Cake\Chronos\Chronos Object
                (
                    [time] => 2017-06-03 00:00:00.000000
                    [timezone] => Asia/Tokyo
                    [hasFixedNow] =>
                )

            [end_at] => Cake\Chronos\Chronos Object
                (
                    [time] => 2017-06-03 01:00:00.000000
                    [timezone] => Asia/Tokyo
                    [hasFixedNow] =>
                )

        )

)
```
