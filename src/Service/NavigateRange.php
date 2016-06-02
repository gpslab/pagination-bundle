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
 * NavigateRange
 * @package AnimeDb\Bundle\PaginationBundle\Service
 */
class NavigateRange
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var int
     */
    protected $left_offset = -1;

    /**
     * @var int
     */
    protected $right_offset = -1;

    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * @return int
     */
    public function getLeftOffset()
    {
        return $this->buildOffset()->left_offset;
    }

    /**
     * @return int
     */
    public function getRightOffset()
    {
        return $this->buildOffset()->right_offset;
    }

    /**
     * @return NavigateRange
     */
    protected function buildOffset()
    {
        if ($this->config->getTotalPages() == 1) {
            throw new \RangeException('Impossible calculate offsets from empty pages list');
        }

        if ($this->left_offset < 0 && $this->right_offset < 0) {
            // definition of offset to the left and to the right of the selected page
            $this->left_offset = (int) floor(($this->config->getMaxNavigate() - 1) / 2);
            $this->right_offset = (int) ceil(($this->config->getMaxNavigate() - 1) / 2);

            // adjustment, if the offset is too large left
            if ($this->config->getCurrentPage() - $this->left_offset < 1) {
                $offset = (int) abs($this->config->getCurrentPage() - 1 - $this->left_offset);
                $this->left_offset = $this->left_offset - $offset;
                $this->right_offset = $this->right_offset + $offset;
            }

            // adjustment, if the offset is too large right
            if ($this->config->getCurrentPage() + $this->right_offset > $this->config->getTotalPages()) {
                $offset = (int) abs($this->config->getTotalPages() - $this->config->getCurrentPage() - $this->right_offset);
                $this->left_offset = $this->left_offset + $offset;
                $this->right_offset = $this->right_offset - $offset;
            }

            // left offset should point not lower of the first page
            if ($this->left_offset >= $this->config->getCurrentPage()) {
                $this->left_offset = $this->config->getCurrentPage() - 1;
            }
        }

        return $this;
    }
}
