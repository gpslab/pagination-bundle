<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\PaginationBundle\Service;

use AnimeDb\Bundle\PaginationBundle\Entity\Node;
use Doctrine\Common\Collections\ArrayCollection;

class View implements \IteratorAggregate
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var NavigateRange
     */
    protected $range;

    /**
     * @var Node|null
     */
    protected $first;

    /**
     * @var Node|null
     */
    protected $prev;

    /**
     * @var Node
     */
    protected $current;

    /**
     * @var Node|null
     */
    protected $next;

    /**
     * @var Node|null
     */
    protected $last;

    /**
     * @var ArrayCollection|null
     */
    protected $list;

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
     * @return ArrayCollection
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
                    if ($page == $this->config->getCurrentPage()) {
                        $this->list->add($this->getCurrent());
                    } else {
                        $this->list->add(new Node($page, $this->buildLink($page)));
                    }
                    $page++;
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
    protected function buildLink($page)
    {
        if ($page == 1 && $this->config->getFirstPageLink()) {
            return $this->config->getFirstPageLink();
        }

        if (is_callable($this->config->getPageLink())) {
            return call_user_func($this->config->getPageLink(), $page);
        } else {
            return sprintf($this->config->getPageLink(), $page);
        }
    }
}
