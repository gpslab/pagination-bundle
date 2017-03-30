<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests\DependencyInjection;

use GpsLab\Bundle\PaginationBundle\DependencyInjection\GpsLabPaginationExtension;
use GpsLab\Bundle\PaginationBundle\Tests\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GpsLabPaginationExtensionTest extends TestCase
{
    public function testLoad()
    {
        /* @var $container \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder */
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $extension = new GpsLabPaginationExtension();
        $extension->load([], $container);
    }

    public function testGetAlias()
    {
        $extension = new GpsLabPaginationExtension();
        $this->assertEquals('gpslab_pagination', $extension->getAlias());
    }
}
