<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests\Service;

use GpsLab\Bundle\PaginationBundle\Service\Builder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
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
     * @var \PHPUnit_Framework_MockObject_MockObject|Request
     */
    private $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbstractQuery
     */
    private $query;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|QueryBuilder
     */
    private $query_builder;

    protected function setUp()
    {
        $this->router = $this->getMockNoConstructor('Symfony\Bundle\FrameworkBundle\Routing\Router');
        $this->request = $this->getMockNoConstructor('Symfony\Component\HttpFoundation\Request');
        $this->query = $this->getMockAbstract('Doctrine\ORM\AbstractQuery', ['getSingleScalarResult']);
        $this->query_builder = $this->getMockNoConstructor('Doctrine\ORM\QueryBuilder');
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

        $this->assertEquals($max_navigate, $config->getMaxNavigate());
        $this->assertEquals($total_pages, $config->getTotalPages());
        $this->assertEquals($current_page, $config->getCurrentPage());
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
            ->will($this->returnSelf())
        ;
        $this->query_builder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with($per_page)
            ->will($this->returnSelf())
        ;

        $builder = new Builder($this->router, $max_navigate, 'page');
        $config = $builder->paginateQuery($this->query_builder, $per_page, $current_page);

        $this->assertEquals($max_navigate, $config->getMaxNavigate());
        $this->assertEquals(ceil($total / $per_page), $config->getTotalPages());
        $this->assertEquals($current_page, $config->getCurrentPage());
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
        $this->currentPage('foo', 'page');

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequest($this->request, 10);
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException
     */
    public function testPaginateRequestLowPageNumber()
    {
        $this->currentPage(0, 'p');

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequest($this->request, 10, 'p');
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException
     */
    public function testPaginateRequestOutOfRange()
    {
        $this->currentPage(150, 'p');

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequest($this->request, 10, 'p');
    }

    public function testPaginateRequest()
    {
        $max_navigate = 6;
        $total_pages = 10;
        $parameter_name = 'p';
        $route = '_route';
        $route_params = ['_route_params'];
        $reference_type = UrlGeneratorInterface::ABSOLUTE_URL;

        $this->currentPage(null, 'p');
        $this->request
            ->expects($this->at(1))
            ->method('get')
            ->with('_route')
            ->will($this->returnValue($route))
        ;
        $this->request
            ->expects($this->at(2))
            ->method('get')
            ->with('_route_params')
            ->will($this->returnValue($route_params))
        ;

        $that = $this;
        $this->router
            ->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnCallback(function ($_route, $_route_params, $_reference_type) use (
                $that,
                $route,
                $route_params,
                $reference_type
            ) {
                $that->assertEquals($reference_type, $_reference_type);
                $that->assertEquals($route, $_route);
                $that->assertEquals($route_params, array_intersect($_route_params, $route_params));

                return $_route.http_build_query($_route_params);
            }))
        ;

        $builder = new Builder($this->router, $max_navigate, 'page');
        $config = $builder->paginateRequest($this->request, $total_pages, $parameter_name, $reference_type);

        $this->assertEquals($max_navigate, $config->getMaxNavigate());
        $this->assertEquals($total_pages, $config->getTotalPages());
        $this->assertEquals(1, $config->getCurrentPage());
        $this->assertEquals($route.http_build_query($route_params), $config->getFirstPageLink());
        $this->assertInstanceOf('Closure', $config->getPageLink());
        $page_number = 3;
        $this->assertEquals(
            $route.http_build_query($route_params + [$parameter_name => $page_number]),
            call_user_func($config->getPageLink(), $page_number)
        );
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\IncorrectPageNumberException
     */
    public function testPaginateRequesQuerytIncorrectPage()
    {
        $this->currentPage('foo', 'page');
        $this->countQuery(10);

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequestQuery($this->request, $this->query_builder, 5);
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException
     */
    public function testPaginateRequestQueryLowPageNumber()
    {
        $this->currentPage(0, 'p');
        $this->countQuery(10);

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequestQuery($this->request, $this->query_builder, 5, 'p');
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException
     */
    public function testPaginateRequestQueryOutOfRange()
    {
        $this->currentPage(150, 'p');
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
        $route_params = ['_route_params'];
        $reference_type = UrlGeneratorInterface::ABSOLUTE_URL;

        $this->currentPage($current_page, 'p');
        $this->request
            ->expects($this->at(1))
            ->method('get')
            ->with('_route')
            ->will($this->returnValue($route))
        ;
        $this->request
            ->expects($this->at(2))
            ->method('get')
            ->with('_route_params')
            ->will($this->returnValue($route_params))
        ;

        $this->countQuery($total);
        $this->query_builder
            ->expects($this->once())
            ->method('setFirstResult')
            ->with(($current_page - 1) * $per_page)
            ->will($this->returnSelf())
        ;
        $this->query_builder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with($per_page)
            ->will($this->returnSelf())
        ;

        $that = $this;
        $this->router
            ->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnCallback(function ($_route, $_route_params, $_reference_type) use (
                $that,
                $route,
                $route_params,
                $reference_type
            ) {
                $that->assertEquals($reference_type, $_reference_type);
                $that->assertEquals($route, $_route);
                $that->assertEquals($route_params, array_intersect($_route_params, $route_params));

                return $_route.http_build_query($_route_params);
            }))
        ;

        $builder = new Builder($this->router, $max_navigate, 'page');
        $config = $builder->paginateRequestQuery(
            $this->request,
            $this->query_builder,
            $per_page,
            $parameter_name,
            $reference_type
        );

        $this->assertEquals($max_navigate, $config->getMaxNavigate());
        $this->assertEquals(ceil($total / $per_page), $config->getTotalPages());
        $this->assertEquals($current_page, $config->getCurrentPage());
        $this->assertEquals($route.http_build_query($route_params), $config->getFirstPageLink());
        $this->assertInstanceOf('Closure', $config->getPageLink());
        $page_number = 3;
        $this->assertEquals(
            $route.http_build_query($route_params + [$parameter_name => $page_number]),
            call_user_func($config->getPageLink(), $page_number)
        );
    }

    /**
     * @param int    $current_page
     * @param string $parameter_name
     */
    private function currentPage($current_page, $parameter_name = 'page')
    {
        $this->request
            ->expects($this->at(0))
            ->method('get')
            ->with($parameter_name)
            ->will($this->returnValue($current_page))
        ;
    }

    /**
     * @param int $total
     */
    private function countQuery($total)
    {
        $this->query
            ->expects($this->once())
            ->method('getSingleScalarResult')
            ->will($this->returnValue($total));

        $this->query_builder
            ->expects($this->once())
            ->method('getRootAliases')
            ->will($this->returnValue(['a', 'b']));
        $this->query_builder
            ->expects($this->once())
            ->method('select')
            ->with('COUNT(a)')
            ->will($this->returnSelf());
        $this->query_builder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($this->query));
    }
}
