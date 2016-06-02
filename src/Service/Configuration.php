<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\PaginationBundle\Service;

/**
 * @package AnimeDb\Bundle\PaginationBundle\Service
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class Configuration
{
    /**
     * Length of the list of pagination defaults
     *
     * @var int
     */
    const DEFAULT_LIST_LENGTH = 5;

    /**
     * @var int
     */
    const DEFAULT_PAGE_LINK = '%s';

    /**
     * @var int
     */
    protected $total_pages = 0;

    /**
     * @var int
     */
    protected $current_page = 1;

    /**
     * @var View
     */
    protected $view;

    /**
     * The number of pages displayed in the navigation
     *
     * @var int
     */
    protected $max_navigate = self::DEFAULT_LIST_LENGTH;

    /**
     * @var string|callback
     */
    protected $page_link = self::DEFAULT_PAGE_LINK;

    /**
     * @var string
     */
    protected $first_page_link = '';

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
     * @return Configuration
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
     * @return Configuration
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
     * @return Configuration
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
     * @return Configuration
     */
    public function setMaxNavigate($max_navigate)
    {
        $this->max_navigate = $max_navigate;
        return $this;
    }

    /**
     * @return string|callback
     */
    public function getPageLink()
    {
        return $this->page_link;
    }

    /**
     * Set page link
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
     * @param string|callback $page_link
     *
     * @return Configuration
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
     * @return Configuration
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
            $this->view = new View($this);
        }
        return $this->view;
    }
}
