<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use DateTime;

/**
 * Dates helper
 */
class DatesHelper extends Helper
{
    /**
     * Get the number of days between the current date and the given date.
     *
     * @param string $then The date to compare with the current date.
     * @return int The number of days between the two dates.
     */
    public function daysAgo(string $then): int
    {
        $now = new DateTime();
        $diff = $now->diff(new DateTime($then));

        return $diff->days;
    }
}
