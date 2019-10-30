<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests\Service;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use GpsLab\Bundle\PaginationBundle\Service\Builder;
use GpsLab\Bundle\PaginationBundle\Tests\TestCase;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BuilderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Router
     */
    private $router;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbstractQuery
     */
    private $query;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|QueryBuilder
     */
    private $query_builder;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $query_params = [
        'foo' => 'bar',
        'p' => 2,
    ];

    protected function setUp()
    {
        $this->router = $this->getMockNoConstructor('Symfony\Bundle\FrameworkBundle\Routing\Router');
        $this->query = $this->getMockAbstract('Doctrine\ORM\AbstractQuery', ['getSingleScalarResult']);
        $this->query_builder = $this->getMockNoConstructor('Doctrine\ORM\QueryBuilder');

        $this->request = new Request($this->query_params);
    }

    public function testDefaultPageLink()
    {
        $builder = new Builder($this->router, 5, 'p');

        self::assertEquals('?p=%d', $builder->paginate(10, 3)->getPageLink());
    }

    /**
     * @return array
     */
    public function getPaginateData()
    {
        return [
            [5, 10, 1],
            [10, 150, 33],
        ];
    }

    /**
     * @dataProvider getPaginateData
     *
     * @param int $max_navigate
     * @param int $total_pages
     * @param int $current_page
     */
    public function testPaginate($max_navigate, $total_pages, $current_page)
    {
        $builder = new Builder($this->router, $max_navigate, 'page');
        $config = $builder->paginate($total_pages, $current_page);

        self::assertEquals($max_navigate, $config->getMaxNavigate());
        self::assertEquals($total_pages, $config->getTotalPages());
        self::assertEquals($current_page, $config->getCurrentPage());
    }

    /**
     * @return array
     */
    public function getPaginateQueryData()
    {
        return [
            [5, 5, 10, 1],
            [10, 10, 150, 7],
        ];
    }

    /**
     * @dataProvider getPaginateQueryData
     *
     * @param int $max_navigate
     * @param int $per_page
     * @param int $total
     * @param int $current_page
     */
    public function testPaginateQuery($max_navigate, $per_page, $total, $current_page)
    {
        $this->countQuery($total);

        $this->query_builder
            ->expects($this->once())
            ->method('setFirstResult')
            ->with(($current_page - 1) * $per_page)
            ->willReturnSelf()
        ;
        $this->query_builder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with($per_page)
            ->willReturnSelf()
        ;

        $builder = new Builder($this->router, $max_navigate, 'page');
        $config = $builder->paginateQuery($this->query_builder, $per_page, $current_page);

        self::assertEquals($max_navigate, $config->getMaxNavigate());
        self::assertEquals(ceil($total / $per_page), $config->getTotalPages());
        self::assertEquals($current_page, $config->getCurrentPage());
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException
     */
    public function testPaginateQueryOutOfRange()
    {
        $total = 10;
        $per_page = 5;
        $current_page = 150;

        $this->countQuery($total);

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateQuery($this->query_builder, $per_page, $current_page);
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\IncorrectPageNumberException
     */
    public function testPaginateRequestIncorrectPage()
    {
        $this->request = new Request(array_merge($this->query_params, [
            'page' => 'foo',
        ]));

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequest($this->request, 10);
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException
     */
    public function testPaginateRequestLowPageNumber()
    {
        $this->request = new Request(array_merge($this->query_params, [
            'p' => 0,
        ]));

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequest($this->request, 10, 'p');
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException
     */
    public function testPaginateRequestOutOfRange()
    {
        $this->request = new Request(array_merge($this->query_params, [
            'p' => 150,
        ]));

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequest($this->request, 10, 'p');
    }

    public function testPaginateRequest()
    {
        $max_navigate = 6;
        $total_pages = 10;
        $parameter_name = 'p';
        $route = '_route';
        $route_params = ['foo' => 'baz', '_route_params' => 123];
        $all_params = array_merge($this->query_params, $route_params);
        $reference_type = UrlGeneratorInterface::ABSOLUTE_URL;
        $this->request = new Request(array_merge($this->query_params, [
            'p' => null,
        ]), [], [
            '_route' => $route,
            '_route_params' => $route_params,
        ]);

        $that = $this;
        $this->router
            ->expects($this->atLeastOnce())
            ->method('generate')
            ->willReturnCallback(function ($_route, $_route_params, $_reference_type) use (
                $that,
                $route,
                $reference_type
            ) {
                $that->assertEquals($reference_type, $_reference_type);
                $that->assertEquals($route, $_route);

                return $_route.http_build_query($_route_params);
            })
        ;

        $builder = new Builder($this->router, $max_navigate, 'page');
        $config = $builder->paginateRequest($this->request, $total_pages, $parameter_name, $reference_type);

        self::assertEquals($max_navigate, $config->getMaxNavigate());
        self::assertEquals($total_pages, $config->getTotalPages());
        self::assertEquals(1, $config->getCurrentPage());
        unset($all_params['p']);
        self::assertEquals($route.http_build_query($all_params), $config->getFirstPageLink());
        self::assertInstanceOf('Closure', $config->getPageLink());
        $page_number = 3;
        self::assertEquals(
            $route.http_build_query($all_params + [$parameter_name => $page_number]),
            call_user_func($config->getPageLink(), $page_number)
        );
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\IncorrectPageNumberException
     */
    public function testPaginateRequesQuerytIncorrectPage()
    {
        $this->request = new Request(array_merge($this->query_params, [
            'page' => 'foo',
        ]));
        $this->countQuery(10);

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequestQuery($this->request, $this->query_builder, 5);
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException
     */
    public function testPaginateRequestQueryLowPageNumber()
    {
        $this->request = new Request(array_merge($this->query_params, [
            'p' => 0,
        ]));
        $this->countQuery(10);

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequestQuery($this->request, $this->query_builder, 5, 'p');
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException
     */
    public function testPaginateRequestQueryOutOfRange()
    {
        $this->request = new Request(array_merge($this->query_params, [
            'p' => 150,
        ]));
        $this->countQuery(10);

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequestQuery($this->request, $this->query_builder, 5, 'p');
    }

    public function testPaginateRequestQuery()
    {
        $per_page = 10;
        $current_page = 7;
        $max_navigate = 6;
        $total = 150;
        $parameter_name = 'p';
        $route = '_route';
        $route_params = ['foo' => 'baz', '_route_params'];
        $all_params = array_merge($this->query_params, $route_params);
        $reference_type = UrlGeneratorInterface::ABSOLUTE_URL;
        $this->request = new Request(array_merge($this->query_params, [
            'p' => $current_page,
        ]), [], [
            '_route' => $route,
            '_route_params' => $route_params,
        ]);

        $this->countQuery($total);
        $this->query_builder
            ->expects($this->once())
            ->method('setFirstResult')
            ->with(($current_page - 1) * $per_page)
            ->willReturnSelf()
        ;
        $this->query_builder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with($per_page)
            ->willReturnSelf()
        ;

        $that = $this;
        $this->router
            ->expects($this->atLeastOnce())
            ->method('generate')
            ->willReturnCallback(function ($_route, $_route_params, $_reference_type) use (
                $that,
                $route,
                $reference_type
            ) {
                $that->assertEquals($reference_type, $_reference_type);
                $that->assertEquals($route, $_route);

                return $_route.http_build_query($_route_params);
            })
        ;

        $builder = new Builder($this->router, $max_navigate, 'page');
        $config = $builder->paginateRequestQuery(
            $this->request,
            $this->query_builder,
            $per_page,
            $parameter_name,
            $reference_type
        );

        self::assertEquals($max_navigate, $config->getMaxNavigate());
        self::assertEquals(ceil($total / $per_page), $config->getTotalPages());
        self::assertEquals($current_page, $config->getCurrentPage());
        unset($all_params['p']);
        self::assertEquals($route.http_build_query($all_params), $config->getFirstPageLink());
        self::assertInstanceOf('Closure', $config->getPageLink());
        $page_number = 3;
        self::assertEquals(
            $route.http_build_query($all_params + [$parameter_name => $page_number]),
            call_user_func($config->getPageLink(), $page_number)
        );
    }

    /**
     * @param int $total
     */
    private function countQuery($total)
    {
        $this->query
            ->expects($this->once())
            ->method('getSingleScalarResult')
            ->willReturn($total);

        $this->query_builder
            ->expects($this->once())
            ->method('getRootAliases')
            ->willReturn(['a', 'b']);
        $this->query_builder
            ->expects($this->once())
            ->method('select')
            ->with('COUNT(a)')
            ->willReturnSelf();
        $this->query_builder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->query);
    }
}
