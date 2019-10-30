<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Tests\Service;

use GpsLab\Bundle\PaginationBundle\Service\Configuration;
use GpsLab\Bundle\PaginationBundle\Tests\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $config;

    protected function setUp()
    {
        $this->config = new Configuration(150, 33);
    }

    public function testDefaultPageLink()
    {
        self::assertEquals('?page=%d', $this->config->getPageLink());
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return [
            [10, 1],
            [150, 33],
        ];
    }

    /**
     * @dataProvider getConfigs
     *
     * @param int $total_pages
     * @param int $current_page
     */
    public function testConstruct($total_pages, $current_page)
    {
        $config = new Configuration($total_pages, $current_page);
        self::assertEquals($total_pages, $config->getTotalPages());
        self::assertEquals($current_page, $config->getCurrentPage());
    }

    /**
     * @dataProvider getConfigs
     *
     * @param int $total_pages
     * @param int $current_page
     */
    public function testCreate($total_pages, $current_page)
    {
        $config = Configuration::create($total_pages, $current_page);
        self::assertEquals($total_pages, $config->getTotalPages());
        self::assertEquals($current_page, $config->getCurrentPage());
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return [
            [
                150,
                10,
                'getTotalPages',
                'setTotalPages',
            ],
            [
                33,
                1,
                'getCurrentPage',
                'setCurrentPage',
            ],
            [
                Configuration::DEFAULT_LIST_LENGTH,
                Configuration::DEFAULT_LIST_LENGTH + 5,
                'getMaxNavigate',
                'setMaxNavigate',
            ],
            [
                Configuration::DEFAULT_PAGE_LINK,
                'page_%s.html',
                'getPageLink',
                'setPageLink',
            ],
            [
                Configuration::DEFAULT_PAGE_LINK,
                function ($number) {
                    return 'page_'.$number.'.html';
                },
                'getPageLink',
                'setPageLink',
            ],
            [
                '',
                '/index.html',
                'getFirstPageLink',
                'setFirstPageLink',
            ],
        ];
    }

    /**
     * @dataProvider getMethods
     *
     * @param mixed  $default
     * @param mixed  $new
     * @param string $getter
     * @param string $setter
     */
    public function testSetGet($default, $new, $getter, $setter)
    {
        self::assertEquals($default, call_user_func([$this->config, $getter]));
        self::assertEquals($this->config, call_user_func([$this->config, $setter], $new));
        self::assertEquals($new, call_user_func([$this->config, $getter]));
    }

    public function testGetView()
    {
        $view = $this->config->getView();
        self::assertInstanceOf('GpsLab\Bundle\PaginationBundle\Service\View', $view);

        // test lazy load
        $this->config->setPageLink('?p=%s');
        self::assertEquals($view, $this->config->getView());
    }
}
