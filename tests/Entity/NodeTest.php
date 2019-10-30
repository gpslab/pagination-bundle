<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests\Entity;

use GpsLab\Bundle\PaginationBundle\Entity\Node;
use GpsLab\Bundle\PaginationBundle\Tests\TestCase;

class NodeTest extends TestCase
{
    /**
     * @return array
     */
    public function getNodes()
    {
        return [
            [1, '', false],
            [4, 'http://example.com/?p=4', true],
        ];
    }

    /**
     * @dataProvider getNodes
     *
     * @param int    $page
     * @param string $link
     * @param bool   $is_current
     */
    public function test($page, $link, $is_current)
    {
        $node = new Node($page, $link, $is_current);
        self::assertEquals($page, $node->getPage());
        self::assertEquals($link, $node->getLink());
        self::assertEquals($is_current, $node->isCurrent());
    }
}
