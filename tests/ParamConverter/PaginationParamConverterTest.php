<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests\ParamConverter;

use GpsLab\Bundle\PaginationBundle\ParamConverter\PaginationParamConverter;
use GpsLab\Bundle\PaginationBundle\Service\Configuration;
use GpsLab\Bundle\PaginationBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class PaginationParamConverterTest extends TestCase
{
    const MAX_NAVIGATE = 10;

    const PARAMETER_NAME = 'p';

    /**
     * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject|MockObject
     */
    private $router;

    /**
     * @var ParamConverter|\PHPUnit_Framework_MockObject_MockObject|MockObject
     */
    private $configuration;

    /**
     * @var PaginationParamConverter
     */
    private $converter;

    protected function setUp()
    {
        $this->router = $this->getMockNoConstructor('Symfony\Component\Routing\RouterInterface');
        $this->configuration = $this->getMockNoConstructor('Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter');

        $this->converter = new PaginationParamConverter($this->router, self::MAX_NAVIGATE, self::PARAMETER_NAME);
    }

    public function testSupports()
    {
        $this->configuration
            ->expects(self::once())
            ->method('getClass')
            ->willReturn('GpsLab\Bundle\PaginationBundle\Service\Configuration');

        self::assertTrue($this->converter->supports($this->configuration));
    }

    public function testNotSupports()
    {
        $this->configuration
            ->expects(self::once())
            ->method('getClass')
            ->willReturn('stdClass');

        self::assertFalse($this->converter->supports($this->configuration));
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            [
                [],
                [],
                [],
                self::MAX_NAVIGATE,
                self::PARAMETER_NAME,
                UrlGeneratorInterface::ABSOLUTE_PATH,
            ],
            [
                [
                    'max_navigate' => 5,
                ],
                [],
                [],
                5,
                self::PARAMETER_NAME,
                UrlGeneratorInterface::ABSOLUTE_PATH,
            ],
            [
                [
                    'parameter_name' => 'page',
                ],
                [],
                [],
                self::MAX_NAVIGATE,
                'page',
                UrlGeneratorInterface::ABSOLUTE_PATH,
            ],
            [
                [
                    'reference_type' => 'absolute_url',
                ],
                [],
                [],
                self::MAX_NAVIGATE,
                self::PARAMETER_NAME,
                UrlGeneratorInterface::ABSOLUTE_PATH,
            ],
            [
                [
                    'reference_type' => 'absolute_path',
                ],
                [],
                [],
                self::MAX_NAVIGATE,
                self::PARAMETER_NAME,
                UrlGeneratorInterface::ABSOLUTE_PATH,
            ],
            [
                [
                    'reference_type' => 'relative_path',
                ],
                [],
                [],
                self::MAX_NAVIGATE,
                self::PARAMETER_NAME,
                UrlGeneratorInterface::RELATIVE_PATH,
            ],
            [
                [
                    'reference_type' => 'network_path',
                ],
                [],
                [],
                self::MAX_NAVIGATE,
                self::PARAMETER_NAME,
                UrlGeneratorInterface::NETWORK_PATH,
            ],
            [
                [],
                [
                    'foo' => 'bar',
                ],
                [],
                self::MAX_NAVIGATE,
                self::PARAMETER_NAME,
                UrlGeneratorInterface::ABSOLUTE_PATH,
            ],
            [
                [],
                [],
                [
                    'foo' => 'bar',
                ],
                self::MAX_NAVIGATE,
                self::PARAMETER_NAME,
                UrlGeneratorInterface::ABSOLUTE_PATH,
            ],
            [
                [],
                [
                    'foo' => 'bar',
                ],
                [
                    'foo' => 'baz',
                ],
                self::MAX_NAVIGATE,
                self::PARAMETER_NAME,
                UrlGeneratorInterface::ABSOLUTE_PATH,
            ],
            [
                [],
                [
                    self::PARAMETER_NAME => 'bar',
                ],
                [
                    self::PARAMETER_NAME => 'baz',
                ],
                self::MAX_NAVIGATE,
                self::PARAMETER_NAME,
                UrlGeneratorInterface::ABSOLUTE_PATH,
            ],
        ];
    }

    /**
     * @dataProvider getOptions
     *
     * @param array  $options
     * @param array  $query
     * @param array  $route_params
     * @param int    $max_navigate
     * @param string $parameter_name
     * @param string $reference_type
     */
    public function testApply(
        array $options,
        array $query,
        array $route_params,
        $max_navigate,
        $parameter_name,
        $reference_type
    ) {
        $route = 'my_route';
        $prop_name = 'pagination';
        $first_page_link = 'first_page_link';
        $page_link = 'page_link';
        $page_number = 1;

        $expected_route_params = array_merge($query, $route_params);
        unset($expected_route_params[$parameter_name]);

        $this->configuration
            ->expects(self::once())
            ->method('getOptions')
            ->willReturn($options);
        $this->configuration
            ->expects(self::once())
            ->method('getName')
            ->willReturn($prop_name);

        $this->router
            ->expects(self::at(0))
            ->method('generate')
            ->with($route, $expected_route_params, $reference_type)
            ->willReturn($first_page_link);
        $this->router
            ->expects(self::at(1))
            ->method('generate')
            ->with($route, [$parameter_name => $page_number] + $expected_route_params, $reference_type)
            ->willReturn($page_link);

        $request = new Request($query, [], [
            '_route' => $route,
            '_route_params' => $route_params,
        ]);

        self::assertTrue($this->converter->apply($request, $this->configuration));

        self::assertTrue($request->attributes->has($prop_name));
        /* @var $configuration Configuration */
        $configuration = $request->attributes->get($prop_name);
        self::assertInstanceOf('GpsLab\Bundle\PaginationBundle\Service\Configuration', $configuration);
        self::assertSame($max_navigate, $configuration->getMaxNavigate());
        self::assertSame($first_page_link, $configuration->getFirstPageLink());
        $callable_page_link = $configuration->getPageLink();
        self::assertInternalType('callable', $callable_page_link);
        self::assertSame($page_link, $callable_page_link($page_number));
    }
}
