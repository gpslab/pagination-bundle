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
    /**
     * @var GpsLabPaginationExtension
     */
    private $extension;

    protected function setUp()
    {
        $this->extension = new GpsLabPaginationExtension();
    }

    public function testLoad()
    {
        $container = new ContainerBuilder();
        $this->extension->load([], $container);

        $this->assertEquals(5, $container->getParameter('pagination.max_navigate'));
        $this->assertEquals('page', $container->getParameter('pagination.parameter_name'));
        $this->assertEquals(
            'GpsLabPaginationBundle::pagination.html.twig',
            $container->getParameter('pagination.template')
        );
    }

    public function testGetAlias()
    {
        $this->assertEquals('gpslab_pagination', $this->extension->getAlias());
    }
}
