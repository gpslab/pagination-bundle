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

use AnimeDb\Bundle\PaginationBundle\Entity\Node;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package AnimeDb\Bundle\PaginationBundle\Service
 * @author Peter Gribanov <info@peter-gribanov.ru>
 */
class View implements \IteratorAggregate
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var Node|null
     */
    protected $first = null;

    /**
     * @var Node|null
     */
    protected $prev = null;

    /**
     * @var Node
     */
    protected $current;

    /**
     * @var Node|null
     */
    protected $next = null;

    /**
     * @var Node|null
     */
    protected $last = null;

    /**
     * @var ArrayCollection|null
     */
    protected $list = null;

    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config) {
        $this->config = $config;
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
     * @return ArrayCollection|null
     */
    public function getIterator()
    {
        if (!($this->list instanceof ArrayCollection)) {
            $this->list = new ArrayCollection();

            if ($this->getTotal() <= 1) {
                return $this->list;
            }

            // definition of offset to the left and to the right of the selected page
            $left_offset = floor(($this->config->getMaxNavigate() - 1) / 2);
            $right_offset = ceil(($this->config->getMaxNavigate() - 1) / 2);
            // adjustment, if the offset is too large left
            if ($this->config->getCurrentPage() - $left_offset < 1) {
                $offset = abs($this->config->getCurrentPage() - 1 - $left_offset);
                $left_offset = $left_offset - $offset;
                $right_offset = $right_offset + $offset;
            }
            // adjustment, if the offset is too large right
            if ($this->config->getCurrentPage() + $right_offset > $this->getTotal()) {
                $offset = abs($this->getTotal() - $this->config->getCurrentPage() - $right_offset);
                $left_offset = $left_offset + $offset;
                $right_offset = $right_offset - $offset;
            }
            // determining the first and last pages in paging based on the current page and offset
            $page_from = $this->config->getCurrentPage() - $left_offset;
            $page_to = $this->config->getCurrentPage() + $right_offset;
            $page_from = $page_from > 1 ? $page_from : 1;

            // build list
            for ($page = $page_from; $page <= $page_to; $page++) {
                if ($page == $this->config->getCurrentPage()) {
                    $this->list->add($this->getCurrent());
                } else {
                    $this->list->add(new Node($page, $this->buildLink($page)));
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
