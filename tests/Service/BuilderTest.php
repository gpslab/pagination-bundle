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

class BuilderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Router
     */
    private $router;

    protected function setUp()
    {
        $this->router = $this->getMockNoConstructor('Symfony\Bundle\FrameworkBundle\Routing\Router');
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
        /** @var $query \PHPUnit_Framework_MockObject_MockObject|AbstractQuery */
        $query = $this->getMockAbstract('Doctrine\ORM\AbstractQuery', ['getSingleScalarResult']);
        $query
            ->expects($this->once())
            ->method('getSingleScalarResult')
            ->will($this->returnValue($total));

        /** @var $query_builder \PHPUnit_Framework_MockObject_MockObject|QueryBuilder */
        $query_builder = $this->getMockNoConstructor('Doctrine\ORM\QueryBuilder');
        $query_builder
            ->expects($this->once())
            ->method('getRootAliases')
            ->will($this->returnValue(['a', 'b']));
        $query_builder
            ->expects($this->once())
            ->method('select')
            ->with('COUNT(a)')
            ->will($this->returnSelf());
        $query_builder
            ->expects($this->once())
            ->method('setFirstResult')
            ->with(($current_page - 1) * $per_page)
            ->will($this->returnSelf());
        $query_builder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with($per_page)
            ->will($this->returnSelf());
        $query_builder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query));

        $builder = new Builder($this->router, $max_navigate, 'page');
        $config = $builder->paginateQuery($query_builder, $per_page, $current_page);

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

        /** @var $query \PHPUnit_Framework_MockObject_MockObject|AbstractQuery */
        $query = $this->getMockAbstract('Doctrine\ORM\AbstractQuery', ['getSingleScalarResult']);
        $query
            ->expects($this->once())
            ->method('getSingleScalarResult')
            ->will($this->returnValue($total));

        /** @var $query_builder \PHPUnit_Framework_MockObject_MockObject|QueryBuilder */
        $query_builder = $this->getMockNoConstructor('Doctrine\ORM\QueryBuilder');
        $query_builder
            ->expects($this->once())
            ->method('getRootAliases')
            ->will($this->returnValue(['a', 'b']));
        $query_builder
            ->expects($this->once())
            ->method('select')
            ->with('COUNT(a)')
            ->will($this->returnSelf());
        $query_builder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query));

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateQuery($query_builder, $per_page, $current_page);
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\IncorrectPageNumberException
     */
    public function testPaginateRequestIncorrectPage()
    {
        /* @var $request \PHPUnit_Framework_MockObject_MockObject|Request */
        $request = $this->getMockNoConstructor('Symfony\Component\HttpFoundation\Request');
        $request
            ->expects($this->once())
            ->method('get')
            ->with('page')
            ->will($this->returnValue('foo'))
        ;

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequest($request, 10);
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException
     */
    public function testPaginateRequestLowPageNumber()
    {
        /* @var $request \PHPUnit_Framework_MockObject_MockObject|Request */
        $request = $this->getMockNoConstructor('Symfony\Component\HttpFoundation\Request');
        $request
            ->expects($this->once())
            ->method('get')
            ->with('p')
            ->will($this->returnValue(0))
        ;

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequest($request, 10, 'p');
    }

    /**
     * @expectedException \GpsLab\Bundle\PaginationBundle\Exception\OutOfRangeException
     */
    public function testPaginateRequestOutOfRange()
    {
        /* @var $request \PHPUnit_Framework_MockObject_MockObject|Request */
        $request = $this->getMockNoConstructor('Symfony\Component\HttpFoundation\Request');
        $request
            ->expects($this->once())
            ->method('get')
            ->with('p')
            ->will($this->returnValue(150))
        ;

        $builder = new Builder($this->router, 5, 'page');
        $builder->paginateRequest($request, 10, 'p');
    }
}
