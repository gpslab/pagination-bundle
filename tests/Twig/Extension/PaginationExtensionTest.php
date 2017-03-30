<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests\Twig\Extension;

use GpsLab\Bundle\PaginationBundle\Service\Configuration;
use GpsLab\Bundle\PaginationBundle\Twig\Extension\PaginationExtension;

class PaginationExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PaginationExtension
     */
    protected $extension;

    /**
     * @var string
     */
    protected $template = 'foo';

    protected function setUp()
    {
        $this->extension = new PaginationExtension($this->template);
    }

    public function testGetFunctions()
    {
        $functions = $this->extension->getFunctions();

        $this->assertInternalType('array', $functions);
        $this->assertEquals(1, count($functions));
        $this->assertInstanceOf('Twig_SimpleFunction', $functions[0]);
    }

    public function testRender()
    {
        $expected = 'bar';
        $view = 'baz';
        /* @var $env \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment */
        $env = $this
            ->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();
        $env
            ->expects($this->once())
            ->method('render')
            ->with($this->template, ['pagination' => $view])
            ->will($this->returnValue($expected));

        /* @var $configuration \PHPUnit_Framework_MockObject_MockObject|Configuration */
        $configuration = $this
            ->getMockBuilder('GpsLab\Bundle\PaginationBundle\Service\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $configuration
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view));

        $this->assertEquals($expected, $this->extension->renderPagination(
            $env,
            $configuration
        ));
    }

    public function testRenderChangeTemplate()
    {
        $expected = 'bar';
        $view = 'baz';
        $template = 'my_template';
        /* @var $env \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment */
        $env = $this
            ->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();
        $env
            ->expects($this->once())
            ->method('render')
            ->with($template, ['pagination' => $view, 'my_params' => 12345])
            ->will($this->returnValue($expected));

        /* @var $configuration \PHPUnit_Framework_MockObject_MockObject|Configuration */
        $configuration = $this
            ->getMockBuilder('GpsLab\Bundle\PaginationBundle\Service\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $configuration
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view));

        $this->assertEquals($expected, $this->extension->renderPagination(
            $env,
            $configuration,
            $template,
            ['my_params' => 12345]
        ));
    }

    public function testRenderNoOverrideTemplateParams()
    {
        $expected = 'bar';
        $view = 'baz';
        /* @var $env \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment */
        $env = $this
            ->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();
        $env
            ->expects($this->once())
            ->method('render')
            ->with($this->template, ['pagination' => $view])
            ->will($this->returnValue($expected));

        /* @var $configuration \PHPUnit_Framework_MockObject_MockObject|Configuration */
        $configuration = $this
            ->getMockBuilder('GpsLab\Bundle\PaginationBundle\Service\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $configuration
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view));

        $this->assertEquals($expected, $this->extension->renderPagination(
            $env,
            $configuration,
            null,
            ['pagination' => 12345]
        ));
    }

    public function testGetName()
    {
        $this->assertEquals('gpslab_pagination_extension', $this->extension->getName());
    }
}
