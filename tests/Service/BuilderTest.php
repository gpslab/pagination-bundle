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

class BuilderTest extends \PHPUnit_Framework_TestCase
{
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
        $builder = new Builder($max_navigate);
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
        $query = $this
            ->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSingleScalarResult'])
            ->getMockForAbstractClass();
        $query
            ->expects($this->once())
            ->method('getSingleScalarResult')
            ->will($this->returnValue($total));

        /** @var $query_builder \PHPUnit_Framework_MockObject_MockObject|QueryBuilder */
        $query_builder = $this
            ->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
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

        $builder = new Builder($max_navigate);
        $config = $builder->paginateQuery($query_builder, $per_page, $current_page);
        $this->assertEquals($max_navigate, $config->getMaxNavigate());
        $this->assertEquals(ceil($total / $per_page), $config->getTotalPages());
        $this->assertEquals($current_page, $config->getCurrentPage());
    }
}
