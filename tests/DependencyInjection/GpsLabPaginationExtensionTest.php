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

        self::assertEquals(5, $container->getParameter('pagination.max_navigate'));
        self::assertEquals('page', $container->getParameter('pagination.parameter_name'));
        self::assertEquals(
            'GpsLabPaginationBundle::pagination.html.twig',
            $container->getParameter('pagination.template')
        );
    }

    public function testGetAlias()
    {
        self::assertEquals('gpslab_pagination', $this->extension->getAlias());
    }
}
