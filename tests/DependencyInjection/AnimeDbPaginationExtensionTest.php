<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\PaginationBundle\Tests\DependencyInjection;

use AnimeDb\Bundle\PaginationBundle\DependencyInjection\AnimeDbPaginationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test DependencyInjection
 *
 * @package AnimeDb\Bundle\PaginationBundle\Tests\DependencyInjection
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AnimeDbPaginationExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {

        /* @var $container \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder */
        $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerBuilder');

        $di = new AnimeDbPaginationExtension();
        $di->load([], $container);
    }
}
