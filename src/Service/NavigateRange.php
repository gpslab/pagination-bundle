<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Bundle\PaginationBundle\Service;

class NavigateRange
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var int
     */
    private $left_offset = -1;

    /**
     * @var int
     */
    private $right_offset = -1;

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
     * @return self
     */
    private function buildOffset()
    {
        if ($this->left_offset < 0) {
            $this->definitionOffset();
            $this->adjustmentLargeLeftOffset();
            $this->adjustmentLargeRightOffset();
            $this->adjustmentLowerLeftOffset();
        }

        return $this;
    }

    /**
     * Definition of offset to the left and to the right of the selected page.
     */
    private function definitionOffset()
    {
        $this->left_offset = (int) floor(($this->config->getMaxNavigate() - 1) / 2);
        $this->right_offset = (int) ceil(($this->config->getMaxNavigate() - 1) / 2);
    }

    /**
     * Adjustment, if the offset is too large left.
     */
    private function adjustmentLargeLeftOffset()
    {
        if ($this->config->getCurrentPage() - $this->left_offset < 1) {
            $offset = abs($this->config->getCurrentPage() - 1 - $this->left_offset);
            $this->left_offset -= $offset;
            $this->right_offset += $offset;
        }
    }

    /**
     * Adjustment, if the offset is too large right.
     */
    private function adjustmentLargeRightOffset()
    {
        if ($this->config->getCurrentPage() + $this->right_offset > $this->config->getTotalPages()) {
            $offset = abs(
                $this->config->getTotalPages() -
                $this->config->getCurrentPage() -
                $this->right_offset
            );
            $this->left_offset += $offset;
            $this->right_offset -= $offset;
        }
    }

    /**
     * Left offset should point not lower of the first page.
     */
    private function adjustmentLowerLeftOffset()
    {
        if ($this->left_offset >= $this->config->getCurrentPage()) {
            $this->left_offset = $this->config->getCurrentPage() - 1;
        }
    }
}
