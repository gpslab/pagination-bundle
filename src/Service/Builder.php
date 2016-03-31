<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\PaginationBundle\Service;

/**
 * @package AnimeDb\Bundle\PaginationBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Builder
{
    /**
     * The number of pages displayed in the navigation
     *
     * @var int
     */
    protected $max_navigate = Configuration::DEFAULT_LIST_LENGTH;

    /**
     * @param int $max_navigate
     */
    public function __construct($max_navigate)
    {
        $this->max_navigate = $max_navigate;
    }

    /**
     * @param int $total_pages
     * @param int $current_page
     *
     * @return Configuration
     */
    public function paginate($total_pages = 0, $current_page = 1) {
        return (new Configuration($total_pages, $current_page))
            ->setMaxNavigate($this->max_navigate);
    }
}
