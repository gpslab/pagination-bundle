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
use GpsLab\Bundle\PaginationBundle\Tests\TestCase;
use GpsLab\Bundle\PaginationBundle\Twig\Extension\PaginationExtension;

class PaginationExtensionTest extends TestCase
{
    /**
     * @var PaginationExtension
     */
    private $extension;

    /**
     * @var string
     */
    private $template = 'foo';

    protected function setUp()
    {
        $this->extension = new PaginationExtension($this->template);
    }

    public function testGetFunctions()
    {
        $functions = $this->extension->getFunctions();

        self::assertInternalType('array', $functions);
        self::assertCount(1, $functions);
        self::assertInstanceOf('Twig\TwigFunction', $functions[0]);
    }

    public function testRender()
    {
        $expected = 'bar';
        $view = 'baz';
        /* @var $env \PHPUnit_Framework_MockObject_MockObject|\Twig\Environment */
        $env = $this->getMockNoConstructor('Twig\Environment');
        $env
            ->expects($this->once())
            ->method('render')
            ->with($this->template, ['pagination' => $view])
            ->willReturn($expected);

        /* @var $configuration \PHPUnit_Framework_MockObject_MockObject|Configuration */
        $configuration = $this->getMockNoConstructor('GpsLab\Bundle\PaginationBundle\Service\Configuration');
        $configuration
            ->expects($this->once())
            ->method('getView')
            ->willReturn($view);

        self::assertEquals($expected, $this->extension->renderPagination(
            $env,
            $configuration
        ));
    }

    public function testRenderChangeTemplate()
    {
        $expected = 'bar';
        $view = 'baz';
        $template = 'my_template';
        /* @var $env \PHPUnit_Framework_MockObject_MockObject|\Twig\Environment */
        $env = $this->getMockNoConstructor('Twig\Environment');
        $env
            ->expects($this->once())
            ->method('render')
            ->with($template, ['pagination' => $view, 'my_params' => 12345])
            ->willReturn($expected);

        /* @var $configuration \PHPUnit_Framework_MockObject_MockObject|Configuration */
        $configuration = $this->getMockNoConstructor('GpsLab\Bundle\PaginationBundle\Service\Configuration');
        $configuration
            ->expects($this->once())
            ->method('getView')
            ->willReturn($view);

        self::assertEquals($expected, $this->extension->renderPagination(
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
        /* @var $env \PHPUnit_Framework_MockObject_MockObject|\Twig\Environment */
        $env = $this->getMockNoConstructor('Twig\Environment');
        $env
            ->expects($this->once())
            ->method('render')
            ->with($this->template, ['pagination' => $view])
            ->willReturn($expected);

        /* @var $configuration \PHPUnit_Framework_MockObject_MockObject|Configuration */
        $configuration = $this->getMockNoConstructor('GpsLab\Bundle\PaginationBundle\Service\Configuration');
        $configuration
            ->expects($this->once())
            ->method('getView')
            ->willReturn($view);

        self::assertEquals($expected, $this->extension->renderPagination(
            $env,
            $configuration,
            null,
            ['pagination' => 12345]
        ));
    }

    public function testRenderWithCustomMaxNavigate()
    {
        $old_max_navigate = 5;
        $new_max_navigate = 10;
        $expected = 'bar';

        $configuration = new Configuration();
        $configuration->setTotalPages(100);
        $configuration->setMaxNavigate($old_max_navigate);

        /* @var $env \PHPUnit_Framework_MockObject_MockObject|\Twig\Environment */
        $env = $this->getMockNoConstructor('Twig\Environment');
        $env
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($template, array $context) use ($expected, $configuration, $new_max_navigate) {
                self::assertSame($this->template, $template);
                self::assertArrayHasKey('pagination', $context);
                self::assertSame(['pagination'], array_keys($context), 'Context has only "pagination" key.');
                $view = $context['pagination'];
                self::assertInstanceOf('GpsLab\Bundle\PaginationBundle\Service\View', $view);
                self::assertNotEquals($configuration->getView(), $view);
                self::assertCount($new_max_navigate, iterator_to_array($view));

                return $expected;
            });

        self::assertSame($expected, $this->extension->renderPagination(
            $env,
            $configuration,
            null,
            [],
            $new_max_navigate
        ));
        self::assertSame($old_max_navigate, $configuration->getMaxNavigate(), 'The max navigate is not changed in original object.');
    }

    public function testGetName()
    {
        self::assertEquals('gpslab_pagination_extension', $this->extension->getName());
    }
}
