<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests\Exception;

use GpsLab\Bundle\PaginationBundle\Exception\IncorrectPageNumberException;
use GpsLab\Bundle\PaginationBundle\Tests\TestCase;

class IncorrectPageNumberExceptionTest extends TestCase
{
    public function testIncorrect()
    {
        $current_page = -5;
        $message = sprintf('Incorrect "%s" page number.', $current_page);

        $exception = IncorrectPageNumberException::incorrect($current_page);

        self::assertInstanceOf('GpsLab\Bundle\PaginationBundle\Exception\IncorrectPageNumberException', $exception);
        self::assertInstanceOf('Symfony\Component\HttpKernel\Exception\NotFoundHttpException', $exception);
        self::assertEquals($message, $exception->getMessage());
    }
}
