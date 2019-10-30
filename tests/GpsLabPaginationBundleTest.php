<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests;

use GpsLab\Bundle\PaginationBundle\GpsLabPaginationBundle;

class GpsLabPaginationBundleTest extends TestCase
{
    public function testGetContainerExtension()
    {
        $bundle = new GpsLabPaginationBundle();
        $extension = $bundle->getContainerExtension();

        self::assertInstanceOf(
            'GpsLab\Bundle\PaginationBundle\DependencyInjection\GpsLabPaginationExtension',
            $extension
        );
    }
}
