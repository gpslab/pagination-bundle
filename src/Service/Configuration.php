<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Service;

class Configuration
{
    /**
     * Length of the list of pagination defaults.
     *
     * @var int
     */
    const DEFAULT_LIST_LENGTH = 5;

    /**
     * @var int
     */
    const DEFAULT_PAGE_LINK = '?page=%d';

    /**
     * @var int
     */
    private $total_pages = 0;

    /**
     * @var int
     */
    private $current_page = 1;

    /**
     * @var View|null
     */
    private $view;

    /**
     * The number of pages displayed in the navigation.
     *
     * @var int
     */
    private $max_navigate = self::DEFAULT_LIST_LENGTH;

    /**
     * @var string|callable
     */
    private $page_link = self::DEFAULT_PAGE_LINK;

    /**
     * @var string
     */
    private $first_page_link = '';

    /**
     * @param int $total_pages
     * @param int $current_page
     */
    public function __construct($total_pages = 0, $current_page = 1)
    {
        $this->setCurrentPage($current_page);
        $this->setTotalPages($total_pages);
    }

    /**
     * @param int $total_pages
     * @param int $current_page
     *
     * @return self
     */
    public static function create($total_pages = 0, $current_page = 1)
    {
        return new static($total_pages, $current_page);
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->total_pages;
    }

    /**
     * @param int $total_pages
     *
     * @return self
     */
    public function setTotalPages($total_pages)
    {
        $this->total_pages = $total_pages;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->current_page;
    }

    /**
     * @param int $current_page
     *
     * @return self
     */
    public function setCurrentPage($current_page)
    {
        $this->current_page = $current_page;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxNavigate()
    {
        return $this->max_navigate;
    }

    /**
     * @param int $max_navigate
     *
     * @return self
     */
    public function setMaxNavigate($max_navigate)
    {
        $this->max_navigate = $max_navigate;

        return $this;
    }

    /**
     * @return string|callable
     */
    public function getPageLink()
    {
        return $this->page_link;
    }

    /**
     * Set page link.
     *
     * Basic reference, for example `page_%s.html` where %s page number, or
     * callback function which takes one parameter - the number of the page.
     *
     * <code>
     * function ($number) {
     *     return 'page_'.$number.'.html';
     * }
     * </code>
     *
     * @param string|callable $page_link
     *
     * @return self
     */
    public function setPageLink($page_link)
    {
        $this->page_link = $page_link;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstPageLink()
    {
        return $this->first_page_link;
    }

    /**
     * @param string $first_page_link
     *
     * @return self
     */
    public function setFirstPageLink($first_page_link)
    {
        $this->first_page_link = $first_page_link;

        return $this;
    }

    /**
     * @return View
     */
    public function getView()
    {
        if (!$this->view) {
            $this->view = new View($this, new NavigateRange($this));
        }

        return $this->view;
    }
}
