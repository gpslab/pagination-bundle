<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

// hook for support PHPUnit 5.7 and newer versions #21

namespace {
    use PHPUnit\Framework\TestCase;

    if (!class_exists('PHPUnit_Framework_TestCase') && class_exists('PHPUnit\Framework\TestCase')) {
        class PHPUnit_Framework_TestCase extends TestCase
        {
        }
    }
}

namespace GpsLab\Bundle\PaginationBundle\Tests
{
    class TestCase extends \PHPUnit_Framework_TestCase
    {
        /**
         * @param string $class_name
         *
         * @return \PHPUnit_Framework_MockObject_MockObject
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
         * @param array  $methods
         *
         * @return \PHPUnit_Framework_MockObject_MockObject
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
}
