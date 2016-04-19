<?php

class Perception_Bannerpro_Helper_Image extends Mage_Core_Helper_Abstract
{
    protected $_model;
    protected $_scheduleResize = false;
    protected $_scheduleWatermark = false;
    protected $_scheduleRotate = false;
    protected $_angle;
    protected $_watermark;
    protected $_watermarkPosition;
    protected $_watermarkSize;
    protected $_imageFile;
    protected $_placeholder;
	protected $set_background_color;
	protected $_view_mode;
    /**
     * Reset all previos data
     */
    protected function _reset()
    {
        $this->_model = null;
        $this->_scheduleResize = false;
        $this->_scheduleWatermark = false;
        $this->_scheduleRotate = false;
        $this->_angle = null;
        $this->_watermark = null;
        $this->_watermarkPosition = null;
        $this->_watermarkSize = null;
        $this->_imageFile = null;
        return $this;
    }

    public function init($imageFile, $mode="detail")
    {
    	$this->_reset();
    	$this->_view_mode = $mode;
        $this->_setModel(Mage::getModel('bannerpro/image'));
        $this->keepTransparency(true);
        $this->_getModel()->setBackgroundColor($this->getStoreBackgroundColor());
		$this->setImageFile($imageFile);
        return $this;
    }

    /**
     * Schedule resize of the image
     * $width *or* $height can be null - in this case, lacking dimension will be calculated.
     *
     * @see Mage_Catalog_Model_Product_Image
     * @param int $width
     * @param int $height
     * @return Perception_Bannerpro_Helper_Image
     */
    public function resize($width, $height = null, $background = true)
    {
        $this->_getModel()->setWidth($width)->setHeight($height);
        $this->_scheduleResize = true;
        $this->set_background_color = $background;
        return $this;
    }


    /**
     * Guarantee, that image picture width/height will not be distorted.
     * Applicable before calling resize()
     * It is true by default.
     *
     * @see Mage_Catalog_Model_Product_Image
     * @param bool $flag
     * @return Perception_Bannerpro_Helper_Image
     */
    public function keepAspectRatio($flag)
    {
        $this->_getModel()->setKeepAspectRatio($flag);
        return $this;
    }

    /**
     * Guarantee, that image will have dimensions, set in $width/$height
     * Applicable before calling resize()
     * Not applicable, if keepAspectRatio(false)
     *
     * $position - TODO, not used for now - picture position inside the frame.
     *
     * @see Mage_Catalog_Model_Product_Image
     * @param bool $flag
     * @param array $position
     * @return Perception_Bannerpro_Helper_Image
     */
    public function keepFrame($flag, $position = array('center', 'middle'))
    {
        $this->_getModel()->setKeepFrame($flag);
        return $this;
    }

    /**
     * Guarantee, that image will not lose transparency if any.
     * Applicable before calling resize()
     * It is true by default.
     *
     * $alphaOpacity - TODO, not used for now
     *
     * @see Mage_Catalog_Model_Product_Image
     * @param bool $flag
     * @param int $alphaOpacity
     * @return Perception_Bannerpro_Helper_Image
     */
    public function keepTransparency($flag, $alphaOpacity = null)
    {
        $this->_getModel()->setKeepTransparency($flag);
        return $this;
    }

    /**
     * Guarantee, that image picture will not be bigger, than it was.
     * Applicable before calling resize()
     * It is false by default
     *
     * @param bool $flag
     * @return Perception_Bannerpro_Helper_Image
     */
    public function constrainOnly($flag)
    {
        $this->_getModel()->setConstrainOnly($flag);
        return $this;
    }

    /**
     * Set color to fill image frame with.
     * Applicable before calling resize()
     * The keepTransparency(true) overrides this (if image has transparent color)
     * It is white by default.
     *
     * @param array $colorRGB
     * @return Perception_Bannerpro_Helper_Image
     */
    public function backgroundColor($colorRGB)
    {
        // assume that 3 params were given instead of array
        if (!is_array($colorRGB)) {
            $colorRGB = func_get_args();
        }
        $this->_getModel()->setBackgroundColor($colorRGB);
        return $this;
    }

    public function rotate($angle)
    {
        $this->setAngle($angle);
        $this->_getModel()->setAngle($angle);
        $this->_scheduleRotate = true;
        return $this;
    }

    public function watermark($fileName, $position, $size=null)
    {
        $this->setWatermark($fileName)
            ->setWatermarkPosition($position)
            ->setWatermarkSize($size);
        $this->_scheduleWatermark = true;
        return $this;
    }

    public function placeholder($fileName)
    {
        $this->_placeholder = $fileName;
    }

    public function getPlaceholder()
    {
        if (!$this->_placeholder) {
            $attr = $this->_getModel()->getDestinationSubdir();
            $this->_placeholder = 'images/catalog/product/placeholder/'.$attr.'.jpg';
        }
        return $this->_placeholder;
    }

    public function __toString()
    {
    	try {
            if( $this->getImageFile() ) {				
                $this->_getModel()->setBaseFile( $this->getImageFile() );
            }
            
            if(!$this->set_background_color || $this->_view_mode=='detail'?!strlen(Mage::getStoreConfig('bannerpro/info/photo_background_color')):!strlen(Mage::getStoreConfig('bannerpro/info/simple_photo_background_color'))){
				$info = getimagesize($this->_getModel()->getBaseFile());
				
				$oldWidth 	= $info[0];
				$oldHeight 	= $info[1];
				$newWidth = $this->_getModel()->getWidth();
				$newHeight = $this->_getModel()->getHeight();
	
				if($oldWidth*1.0/$newWidth < $oldHeight*1.0/$newHeight) $newWidth = 1.0*$oldWidth * $newHeight/$oldHeight;
				else $newHeight = 1.0*$oldHeight * $newWidth/$oldWidth;
				
				$this->_getModel()->setWidth($newWidth);
				$this->_getModel()->setHeight($newHeight);
            }
            
            if( $this->_getModel()->isCached() ) {
                return $this->_getModel()->getUrl();
            } else {
                if( $this->_scheduleRotate ) {
                    $this->_getModel()->rotate( $this->getAngle() );
                }

                if ($this->_scheduleResize) {
                    $this->_getModel()->resize();
                }

                if( $this->_scheduleWatermark ) {
                    $this->_getModel()
                        ->setWatermarkPosition( $this->getWatermarkPosition() )
                        ->setWatermarkSize($this->parseSize($this->getWatermarkSize()))
                        ->setWatermark($this->getWatermark(), $this->getWatermarkPosition());
                } else {
                    if( $watermark = Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_image") ) {
                        $this->_getModel()
                            ->setWatermarkPosition( $this->getWatermarkPosition() )
                            ->setWatermarkSize($this->parseSize($this->getWatermarkSize()))
                            ->setWatermark($watermark, $this->getWatermarkPosition());
                    }
                }
                $url = $this->_getModel()->saveFile()->getUrl();
            }
        } catch( Exception $e ) {
			//Mage::log($e);
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
    }

    /**
     * Enter description here...
     *
     * @return Perception_Bannerpro_Helper_Image
     */
    protected function _setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Catalog_Model_Product_Image
     */
    protected function _getModel()
    {
        return $this->_model;
    }

    protected function setAngle($angle)
    {
        $this->_angle = $angle;
        return $this;
    }

    protected function getAngle()
    {
        return $this->_angle;
    }

    protected function setWatermark($watermark)
    {
        $this->_watermark = $watermark;
        return $this;
    }

    protected function getWatermark()
    {
        return $this->_watermark;
    }

    protected function setWatermarkPosition($position)
    {
        $this->_watermarkPosition = $position;
        return $this;
    }

    protected function getWatermarkPosition()
    {
        if( $this->_watermarkPosition ) {
            return $this->_watermarkPosition;
        } else {
            return Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position");
        }
    }

    public function setWatermarkSize($size)
    {
        $this->_watermarkSize = $size;
        return $this;
    }

    protected function getWatermarkSize()
    {
        if( $this->_watermarkSize ) {
            return $this->_watermarkSize;
        } else {
            return Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size");
        }
    }

    protected function setImageFile($file)
    {
        $this->_imageFile = $file;
        return $this;
    }

    protected function getImageFile()
    {
        return $this->_imageFile;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    protected function parseSize($string)
    {
        $size = explode('x', strtolower($string));
        if( sizeof($size) == 2 ) {
            return array(
                'width' => ($size[0] > 0) ? $size[0] : null,
                'heigth' => ($size[1] > 0) ? $size[1] : null,
            );
        }
        return false;
    }
    public function getStoreBackgroundColor()
    {
    	$color = $this->_view_mode=="detail"?explode(',',Mage::getStoreConfig('bannerpro/info/photo_background_color')):explode(',',Mage::getStoreConfig('bannerpro/info/simple_photo_background_color'));
    	if(sizeof($color)==3)
    	{
    		foreach($color as $item){
    			if(!is_numeric($item) || $item >255) return array(192, 192, 192);
    		}
    		return array((int)$color[0], (int)$color[1], (int)$color[2]);
    	}
    	return array(192, 192, 192);
    }
}