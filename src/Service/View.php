<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use GpsLab\Bundle\PaginationBundle\Entity\Node;

class View implements \IteratorAggregate
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var NavigateRange
     */
    private $range;

    /**
     * @var Node|null
     */
    private $first;

    /**
     * @var Node|null
     */
    private $prev;

    /**
     * @var Node|null
     */
    private $current;

    /**
     * @var Node|null
     */
    private $next;

    /**
     * @var Node|null
     */
    private $last;

    /**
     * @var ArrayCollection|null
     */
    private $list;

    /**
     * @param Configuration $config
     * @param NavigateRange $range
     */
    public function __construct(Configuration $config, NavigateRange $range)
    {
        $this->config = $config;
        $this->range = $range;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->config->getTotalPages();
    }

    /**
     * @return Node|null
     */
    public function getFirst()
    {
        if (!$this->first && $this->config->getCurrentPage() > 1) {
            $this->first = new Node(1, $this->buildLink(1));
        }

        return $this->first;
    }

    /**
     * @return Node|null
     */
    public function getPrev()
    {
        if (!$this->prev && $this->config->getCurrentPage() > 1) {
            $this->prev = new Node(
                $this->config->getCurrentPage() - 1,
                $this->buildLink($this->config->getCurrentPage() - 1)
            );
        }

        return $this->prev;
    }

    /**
     * @return Node
     */
    public function getCurrent()
    {
        if (!$this->current) {
            $this->current = new Node(
                $this->config->getCurrentPage(),
                $this->buildLink($this->config->getCurrentPage()),
                true
            );
        }

        return $this->current;
    }

    /**
     * @return Node|null
     */
    public function getNext()
    {
        if (!$this->next && $this->config->getCurrentPage() < $this->getTotal()) {
            $this->next = new Node(
                $this->config->getCurrentPage() + 1,
                $this->buildLink($this->config->getCurrentPage() + 1)
            );
        }

        return $this->next;
    }

    /**
     * @return Node|null
     */
    public function getLast()
    {
        if (!$this->last && $this->config->getCurrentPage() < $this->getTotal()) {
            $this->last = new Node($this->getTotal(), $this->buildLink($this->getTotal()));
        }

        return $this->last;
    }

    /**
     * @return ArrayCollection|Node[]
     */
    public function getIterator()
    {
        if (!($this->list instanceof ArrayCollection)) {
            $this->list = new ArrayCollection();

            if ($this->getTotal() > 1) {
                // determining the first and last pages in paging based on the current page and offset
                $page = $this->config->getCurrentPage() - $this->range->getLeftOffset();
                $page_to = $this->config->getCurrentPage() + $this->range->getRightOffset();

                while ($page <= $page_to) {
                    $this->list->add(new Node(
                        $page,
                        $this->buildLink($page),
                        $page === $this->config->getCurrentPage()
                    ));
                    ++$page;
                }
            }
        }

        return $this->list;
    }

    /**
     * @param int $page
     *
     * @return string
     */
    private function buildLink($page)
    {
        if ($page === 1 && $this->config->getFirstPageLink()) {
            return $this->config->getFirstPageLink();
        }

        if (is_callable($this->config->getPageLink())) {
            return call_user_func($this->config->getPageLink(), $page);
        }

        return sprintf($this->config->getPageLink(), $page);
    }
}
