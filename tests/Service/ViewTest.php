<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use GpsLab\Bundle\PaginationBundle\Entity\Node;
use GpsLab\Bundle\PaginationBundle\Service\Configuration;
use GpsLab\Bundle\PaginationBundle\Service\NavigateRange;
use GpsLab\Bundle\PaginationBundle\Service\View;
use GpsLab\Bundle\PaginationBundle\Tests\TestCase;

class ViewTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Configuration
     */
    private $config;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|NavigateRange
     */
    private $range;

    /**
     * @var View
     */
    private $view;

    protected function setUp()
    {
        $this->config = $this->getMockNoConstructor('GpsLab\Bundle\PaginationBundle\Service\Configuration');
        $this->range = $this->getMockNoConstructor('GpsLab\Bundle\PaginationBundle\Service\NavigateRange');

        $this->view = new View($this->config, $this->range);
    }

    public function testGetTotal()
    {
        $this->config
            ->expects($this->once())
            ->method('getTotalPages')
            ->willReturn('110')
        ;

        self::assertEquals(110, $this->view->getTotal());
    }

    /**
     * @return array
     */
    public function getFailNodes()
    {
        return [
            ['getFirst', 1],
            ['getPrev', 1],
            ['getNext', 110],
            ['getLast', 110],
        ];
    }

    /**
     * @dataProvider getFailNodes
     *
     * @param string $method
     * @param int    $current_page
     */
    public function testGetNodeFail($method, $current_page)
    {
        $this->config
            ->expects($this->any())
            ->method('getTotalPages')
            ->willReturn(110)
        ;
        $this->config
            ->expects($this->any())
            ->method('getCurrentPage')
            ->willReturn($current_page)
        ;

        self::assertNull(call_user_func([$this->view, $method]));
    }

    /**
     * @return array
     */
    public function getPageLinks()
    {
        return [
            ['page_%s.html'],
            [function ($number) {
                return 'page_'.$number.'.html';
            }],
        ];
    }

    /**
     * @return array
     */
    public function getFirstPageLinks()
    {
        return [
            ['page_%s.html', ''],
            ['page_%s.html', '/index.html'],
            [function ($number) {
                return 'page_'.$number.'.html';
            }, ''],
            [function ($number) {
                return 'page_'.$number.'.html';
            }, '/index.html'],
        ];
    }

    /**
     * @param string|callable $page_link
     * @param int             $number
     *
     * @return string
     */
    protected function getLink($page_link, $number)
    {
        return is_callable($page_link) ? call_user_func($page_link, $number) : sprintf($page_link, $number);
    }

    /**
     * @dataProvider getFirstPageLinks
     *
     * @param string|callable $page_link
     * @param string          $first_page_link
     */
    public function testGetFirst($page_link, $first_page_link)
    {
        $this->config
            ->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn(10);
        $this->config
            ->expects($first_page_link ? $this->atLeastOnce() : $this->once())
            ->method('getFirstPageLink')
            ->willReturn($first_page_link);
        $this->config
            ->expects($first_page_link ? $this->never() : $this->atLeastOnce())
            ->method('getPageLink')
            ->willReturn($page_link);

        $node = $this->view->getFirst();
        self::assertInstanceOf('GpsLab\Bundle\PaginationBundle\Entity\Node', $node);
        self::assertEquals(1, $node->getPage());
        if ($first_page_link) {
            self::assertEquals($first_page_link, $node->getLink());
        } else {
            self::assertEquals($this->getLink($page_link, 1), $node->getLink());
        }
    }

    /**
     * @dataProvider getPageLinks
     *
     * @param string|callable $page_link
     */
    public function testGetPrev($page_link)
    {
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getCurrentPage')
            ->willReturn(5);
        $this->config
            ->expects($this->never())
            ->method('getFirstPageLink')
            ->willReturn('');
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getPageLink')
            ->willReturn($page_link);

        $node = $this->view->getPrev();
        self::assertInstanceOf('GpsLab\Bundle\PaginationBundle\Entity\Node', $node);
        self::assertEquals(4, $node->getPage());
        self::assertEquals($this->getLink($page_link, 4), $node->getLink());
    }

    /**
     * @dataProvider getFirstPageLinks
     *
     * @param string|callable $page_link
     * @param string          $first_page_link
     */
    public function testGetCurrent($page_link, $first_page_link)
    {
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getCurrentPage')
            ->willReturn(1);
        $this->config
            ->expects($first_page_link ? $this->atLeastOnce() : $this->once())
            ->method('getFirstPageLink')
            ->willReturn($first_page_link);
        $this->config
            ->expects($first_page_link ? $this->never() : $this->atLeastOnce())
            ->method('getPageLink')
            ->willReturn($page_link);

        $node = $this->view->getCurrent();
        self::assertInstanceOf('GpsLab\Bundle\PaginationBundle\Entity\Node', $node);
        self::assertEquals(1, $node->getPage());
        if ($first_page_link) {
            self::assertEquals($first_page_link, $node->getLink());
        } else {
            self::assertEquals($this->getLink($page_link, 1), $node->getLink());
        }
    }

    /**
     * @dataProvider getPageLinks
     *
     * @param string|callable $page_link
     */
    public function testGetNext($page_link)
    {
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getCurrentPage')
            ->willReturn(5);
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getTotalPages')
            ->willReturn(10);
        $this->config
            ->expects($this->never())
            ->method('getFirstPageLink')
            ->willReturn('');
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getPageLink')
            ->willReturn($page_link);

        $node = $this->view->getNext();
        self::assertInstanceOf('GpsLab\Bundle\PaginationBundle\Entity\Node', $node);
        self::assertEquals(6, $node->getPage());
        self::assertEquals($this->getLink($page_link, 6), $node->getLink());
    }

    /**
     * @dataProvider getPageLinks
     *
     * @param string|callable $page_link
     */
    public function testGetLast($page_link)
    {
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getCurrentPage')
            ->willReturn(5);
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getTotalPages')
            ->willReturn(10);
        $this->config
            ->expects($this->never())
            ->method('getFirstPageLink')
            ->willReturn('');
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getPageLink')
            ->willReturn($page_link);

        $node = $this->view->getLast();
        self::assertInstanceOf('GpsLab\Bundle\PaginationBundle\Entity\Node', $node);
        self::assertEquals(10, $node->getPage());
        self::assertEquals($this->getLink($page_link, 10), $node->getLink());
    }

    /**
     * @return array
     */
    public function getNodes()
    {
        return [
            [
                2,
                '/?page=%s',
                null,
                new ArrayCollection([
                    new Node(1, '/?page=1', true),
                    new Node(2, '/?page=2'),
                ]),
            ],
            [
                2,
                '/?page=%s',
                null,
                new ArrayCollection([
                    new Node(1, '/?page=1'),
                    new Node(2, '/?page=2', true),
                ]),
            ],
            [
                10,
                '/?page=%s',
                null,
                new ArrayCollection([
                    new Node(1, '/?page=1', true),
                    new Node(2, '/?page=2'),
                    new Node(3, '/?page=3'),
                    new Node(4, '/?page=4'),
                    new Node(5, '/?page=5'),
                ]),
            ],
            [
                10,
                '/?page=%s',
                null,
                new ArrayCollection([
                    new Node(6, '/?page=6'),
                    new Node(7, '/?page=7'),
                    new Node(8, '/?page=8'),
                    new Node(9, '/?page=9'),
                    new Node(10, '/?page=10', true),
                ]),
            ],
            [
                10,
                '/?page=%s',
                null,
                new ArrayCollection([
                    new Node(3, '/?page=3'),
                    new Node(4, '/?page=4'),
                    new Node(5, '/?page=5', true),
                    new Node(6, '/?page=6'),
                    new Node(7, '/?page=7'),
                ]),
            ],
            [
                10,
                function ($number) {
                    return sprintf('/?page=%s', $number);
                },
                '/',
                new ArrayCollection([
                    new Node(4, '/?page=4'),
                    new Node(5, '/?page=5', true),
                    new Node(6, '/?page=6'),
                    new Node(7, '/?page=7'),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider getNodes
     *
     * @param int             $total_pages
     * @param string|\Closure $page_link
     * @param string          $first_page_link
     * @param ArrayCollection $list
     */
    public function testGetIterator($total_pages, $page_link, $first_page_link, $list)
    {
        $current_page = 1;
        foreach ($list as $node) {
            /** @var $node Node */
            if ($node->isCurrent()) {
                $current_page = $node->getPage();
            }
        }

        $left_offset = $current_page - $list->first()->getPage();
        $right_offset = $list->last()->getPage() - $current_page;

        if ($list->first()->getPage() === 1) {
            $this->config
                ->expects($this->once())
                ->method('getFirstPageLink')
                ->willReturn($first_page_link);
        } else {
            $this->config
                ->expects($this->never())
                ->method('getFirstPageLink');
        }

        $this->config
            ->expects($this->once())
            ->method('getTotalPages')
            ->willReturn($total_pages);
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getCurrentPage')
            ->willReturn($current_page);
        $this->config
            ->expects($this->atLeastOnce())
            ->method('getPageLink')
            ->willReturn($page_link);

        $this->range
            ->expects($this->once())
            ->method('getLeftOffset')
            ->willReturn($left_offset);
        $this->range
            ->expects($this->once())
            ->method('getRightOffset')
            ->willReturn($right_offset);

        self::assertEquals($list, $this->view->getIterator());
    }

    public function testGetIteratorEmpty()
    {
        $this->config
            ->expects($this->once())
            ->method('getTotalPages')
            ->willReturn(1);
        $this->config
            ->expects($this->never())
            ->method('getCurrentPage');
        $this->config
            ->expects($this->never())
            ->method('getPageLink');
        $this->config
            ->expects($this->never())
            ->method('getFirstPageLink');

        $this->range
            ->expects($this->never())
            ->method('getLeftOffset');
        $this->range
            ->expects($this->never())
            ->method('getRightOffset');

        self::assertEquals(new ArrayCollection(), $this->view->getIterator());
    }
}
