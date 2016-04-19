<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Menupro
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Menupro_Block_Autosub extends Mage_Catalog_Block_Navigation
{
    protected function _renderCategoryMenuItemHtml($category, $level = 0, $childrenWrapClass = '', $parentClass, $ulClass, $aAttribute, $extraElement)
    {
        if (!$category->getIsActive()) {
            return '';
        }
        $html = array();
        // get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = ($children && $childrenCount);
        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);
        // prepare list item html classes
        $classes = array();
        //$classes[] = 'level' . $level;
        //$classes[] = 'nav-' . $this->_getItemPosition($level);
        if ($this->isCategoryActive($category)) {
            $classes[] = 'active';
        }
        $linkClass = '';
        if ($hasActiveChildren) {
            //$classes[] = 'parent mcpdropdown has-submenu ';
			$classes[] = $parentClass;
        }
        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }
        // assemble list item with attributes
        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';
        $html[] = $htmlLi;
        $html[] = '<a ' . (($hasActiveChildren) ? $aAttribute : "") .' href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
        $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
        $html[] = '</a>';
		if ($hasActiveChildren) {
            $html[] = $extraElement;
        }

        // render children
        $htmlChildren = '';
        $j = 0;
        foreach ($activeChildren as $child) {
            $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                $child,
                ($level + 1),
                $childrenWrapClass,
				$parentClass,
                $ulClass,
				$aAttribute,
				$extraElement
            );
            $j++;
        }
        if (!empty($htmlChildren)) {
            if ($childrenWrapClass) {
                $html[] = '<div class="' . $childrenWrapClass . '">';
            }
			$html[] = '<ul class="' . $ulClass .'">';
            $html[] = $htmlChildren;
            $html[] = '</ul>';
            if ($childrenWrapClass) {
                $html[] = '</div>';
            }
        }

        $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;
    }
    /**
     * Render category to html
     *
     * @deprecated deprecated after 1.4
     * @param Mage_Catalog_Model_Category $category
     * @param int Nesting level number
     * @param boolean Whether ot not this item is last, affects list item class
     * @return string
     */
    public function drawItem($category, $level = 0, $childrenWrapClass = '', $parentClass, $ulClass, $aAttribute, $extraElement)
    {
        return $this->_renderCategoryMenuItemHtml($category, $level, $childrenWrapClass = '', $parentClass, $ulClass, $aAttribute, $extraElement);
    }
}