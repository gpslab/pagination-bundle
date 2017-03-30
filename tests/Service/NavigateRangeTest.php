<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests\Service;

use GpsLab\Bundle\PaginationBundle\Service\Configuration;
use GpsLab\Bundle\PaginationBundle\Service\NavigateRange;

class NavigateRangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Configuration
     */
    protected $config;

    /**
     * @var NavigateRange
     */
    protected $range;

    protected function setUp()
    {
        $this->config = $this
            ->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->range = new NavigateRange($this->config);
    }

    /**
     * @return array
     */
    public function getOffsets()
    {
        return [
            [5, 1, 2, 0, 1],
            [5, 2, 2, 1, 0],
            [5, 1, 10, 0, 4],
            [5, 2, 10, 1, 3],
            [5, 3, 10, 2, 2],
            [5, 4, 10, 2, 2],
            [5, 8, 10, 2, 2],
            [5, 9, 10, 3, 1],
            [5, 10, 10, 4, 0],
            [5, 1, 1, 0, 0], // list pages is empty
        ];
    }

    /**
     * @dataProvider getOffsets
     *
     * @param int $max_navigate
     * @param int $current_page
     * @param int $total_pages
     * @param int $left_offset
     * @param int $right_offset
     */
    public function testBuildOffset($max_navigate, $current_page, $total_pages, $left_offset, $right_offset)
    {
        $this->config
            ->expects($this->exactly(2)) // test cache build result
            ->method('getMaxNavigate')
            ->will($this->returnValue($max_navigate));
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getCurrentPage')
            ->will($this->returnValue($current_page));
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getTotalPages')
            ->will($this->returnValue($total_pages));

        $this->assertEquals($left_offset, $this->range->getLeftOffset());
        $this->assertEquals($right_offset, $this->range->getRightOffset());
    }
}
