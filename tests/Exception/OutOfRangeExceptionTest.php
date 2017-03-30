<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests\Exception;

use GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException;

class OutOfRangeExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testIncorrect()
    {
        $current_page = -5;
        $total_pages = 10;
        $message = sprintf('Select page "%s" is out of range "%s".', $current_page, $total_pages);

        $exception = OutOfRangeException::out($current_page, $total_pages);

        $this->assertInstanceOf('GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException', $exception);
        $this->assertEquals($message, $exception->getMessage());
    }
}
