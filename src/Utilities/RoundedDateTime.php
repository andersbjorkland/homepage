<?php

namespace App\Utilities;

use \DateTime;

/**
 * Returns an DateTime-object that's rounded to next close hour.
 */
class RoundedDateTime extends DateTime {
    public function __construct()
    {
        parent::__construct("now");
        $minutes = $this->format("i");
        $this->modify("+1 hour");
        $this->modify("-" . $minutes . " minutes");
    }
}