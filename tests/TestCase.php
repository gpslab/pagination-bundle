<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @param string $class_name
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MockObject
     */
    protected function getMockNoConstructor($class_name)
    {
        return $this
            ->getMockBuilder($class_name)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
    }

    /**
     * @param string $class_name
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MockObject
     */
    protected function getMockAbstract($class_name, array $methods)
    {
        return $this
            ->getMockBuilder($class_name)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMockForAbstractClass();
    }
}
