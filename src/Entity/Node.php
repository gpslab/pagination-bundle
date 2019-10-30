<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Entity;

class Node
{
    /**
     * @var int
     */
    private $page = 1;

    /**
     * @var string
     */
    private $link = '';

    /**
     * @var bool
     */
    private $is_current = false;

    /**
     * @param int    $page
     * @param string $link
     * @param bool   $is_current
     */
    public function __construct($page = 1, $link = '', $is_current = false)
    {
        $this->page = $page;
        $this->link = $link;
        $this->is_current = $is_current;
    }

    /**
     * @return bool
     */
    public function isCurrent()
    {
        return $this->is_current;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }
}
