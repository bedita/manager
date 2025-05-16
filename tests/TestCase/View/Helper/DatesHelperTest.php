<?php
declare(strict_types=1);

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\DatesHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * App\View\Helper\DatesHelper Test Case
 *
 * @coversDefaultClass \App\View\Helper\DatesHelper
 */
#[CoversClass(DatesHelper::class)]
#[CoversMethod(DatesHelper::class, 'daysAgo')]
class DatesHelperTest extends TestCase
{
    /**
     * Test `daysAgo` method.
     *
     * @return void
     */
    public function testDaysAgo(): void
    {
        $view = new View();
        $helper = new DatesHelper($view);

        // Test with a date 5 days ago
        $date = (new \DateTime())->modify('-5 days')->format('Y-m-d');
        $result = $helper->daysAgo($date);
        $this->assertEquals(5, $result);

        // Test with a date 10 days ago
        $date = (new \DateTime())->modify('-10 days')->format('Y-m-d');
        $result = $helper->daysAgo($date);
        $this->assertEquals(10, $result);

        // Test with today's date
        $date = (new \DateTime())->format('Y-m-d');
        $result = $helper->daysAgo($date);
        $this->assertEquals(0, $result);
    }
}
