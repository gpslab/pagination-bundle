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
        $container
            ->expects($this->at(0))
            ->method('setParameter')
            ->with('pagination.max_navigate', 5)
        ;
        $container
            ->expects($this->at(1))
            ->method('setParameter')
            ->with('pagination.parameter_name', 'page')
        ;
        $container
            ->expects($this->at(2))
            ->method('setParameter')
            ->with('pagination.template', 'GpsLabPaginationBundle::pagination.html.twig')
        ;

        $extension = new GpsLabPaginationExtension();
        $extension->load([], $container);
    }

    public function testGetAlias()
    {
        $extension = new GpsLabPaginationExtension();
        $this->assertEquals('gpslab_pagination', $extension->getAlias());
    }
}
