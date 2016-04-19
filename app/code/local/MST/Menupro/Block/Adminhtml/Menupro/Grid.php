<?php
class MST_Menupro_Block_Adminhtml_Menupro_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('menuproGrid');
        //$this->setDefaultSort('position');
        //$this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        //$this->setTemplate('menupro/grid.phtml');
    }
    protected function _prepareLayout()
	{
	    $this->unsetChild('reset_filter_button');
	    $this->unsetChild('search_button');
	}
}