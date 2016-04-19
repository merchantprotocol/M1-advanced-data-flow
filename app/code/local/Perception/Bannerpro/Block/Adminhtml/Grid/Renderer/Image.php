<?php

class Perception_Bannerpro_Block_Adminhtml_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if($row->getFilename()==""){
            return "";
        }
        else{
            return "<img src='".Mage::getBaseUrl("media").$row->getFilename()."' width='50' height='50'/>";
        }
    }
} 