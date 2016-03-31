<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\PaginationBundle\Tests\Twig\Extension;

use AnimeDb\Bundle\PaginationBundle\Twig\Extension\PaginationExtension;
use AnimeDb\Bundle\PaginationBundle\Service\Configuration;

/**
 * Class PaginationExtensionTest
 * @package AnimeDb\Bundle\PaginationBundle\Tests\Twig\Extension
 */
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
        $this->assertInstanceOf('\Twig_SimpleFunction', $functions[0]);
    }

    public function testRender()
    {
        $expected = 'bar';
        $view = 'baz';
        /* @var $env \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment */
        $env = $this
            ->getMockBuilder('\Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();
        $env
            ->expects($this->once())
            ->method('render')
            ->with($this->template, ['pagination' => $view])
            ->will($this->returnValue($expected));

        /* @var $configuration \PHPUnit_Framework_MockObject_MockObject|Configuration */
        $configuration = $this
            ->getMockBuilder('\AnimeDb\Bundle\PaginationBundle\Service\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $configuration
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view));

        $this->assertEquals($expected, $this->extension->render(
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
            ->getMockBuilder('\Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();
        $env
            ->expects($this->once())
            ->method('render')
            ->with($template, ['pagination' => $view, 'my_params' => 12345])
            ->will($this->returnValue($expected));

        /* @var $configuration \PHPUnit_Framework_MockObject_MockObject|Configuration */
        $configuration = $this
            ->getMockBuilder('\AnimeDb\Bundle\PaginationBundle\Service\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $configuration
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view));

        $this->assertEquals($expected, $this->extension->render(
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
            ->getMockBuilder('\Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();
        $env
            ->expects($this->once())
            ->method('render')
            ->with($this->template, ['pagination' => $view])
            ->will($this->returnValue($expected));

        /* @var $configuration \PHPUnit_Framework_MockObject_MockObject|Configuration */
        $configuration = $this
            ->getMockBuilder('\AnimeDb\Bundle\PaginationBundle\Service\Configuration')
            ->disableOriginalConstructor()
            ->getMock();
        $configuration
            ->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($view));

        $this->assertEquals($expected, $this->extension->render(
            $env,
            $configuration,
            null,
            ['pagination' => 12345]
        ));
    }

    public function testGetName()
    {
        $this->assertEquals('anime_db_pagination_extension', $this->extension->getName());
    }
}