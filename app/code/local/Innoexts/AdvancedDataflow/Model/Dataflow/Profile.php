<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_AdvancedDataflow
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Convert profile
 *
 * @category   Innoexts
 * @package    Innoexts_AdvancedDataflow
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedDataflow_Model_Dataflow_Profile extends Mage_Dataflow_Model_Profile 
{
	/**
     * Retrieve advanced dataflow helper
     * 
     * @return Innoexts_AdvancedDataflow_Helper_Data
     */
    protected function getHelper()
    {
        return Mage::helper('advanceddataflow');
    }
    /**
     * Get adapters
     * 
     * @return array
     */
    protected function _getAdapters()
    {
        return array(
            'product'     => 'catalog/convert_adapter_product', 
            'customer'    => 'customer/convert_adapter_customer', 
            'order'       => 'advanceddataflow/sales_convert_adapter_order', 
        );
    }
    /**
     * Get parsers
     * 
     * @return array
     */
    protected function _getParsers()
    {
        return array(
            'product'     => 'catalog/convert_parser_product', 
            'customer'    => 'customer/convert_parser_customer', 
            'order'       => 'advanceddataflow/sales_convert_parser_order', 
        );
    }
    /**
     * Get default XML parser
     * 
     * @return string
     */
    protected function getDefaultXMLParser()
    {
        return 'advanceddataflow/dataflow_convert_parser_xml';
    }
    /**
     * Get XML parsers
     * 
     * @return array
     */
    protected function getXMLParsers()
    {
        return array(
            'product'     => 'advanceddataflow/catalog_convert_parser_product_xml', 
            'customer'    => 'advanceddataflow/customer_convert_parser_customer_xml', 
            'order'       => 'advanceddataflow/sales_convert_parser_order_xml', 
        );
    }
    /**
     * Get XML parser
     * 
     * @param string $entityType
     * @return string
     */
    protected function getXMLParser($entityType)
    {
        $parsers = $this->getXMLParsers();
        if (isset($parsers[$entityType])) return $parsers[$entityType];
        else return $this->getDefaultXMLParser();
    }
    /**
     * Parse gui data
     * 
     * @return Innoexts_AdvancedDataflow_Model_Dataflow_Profile
     */
    public function _parseGuiData()
    {
        $nl = "\r\n";
        $tab = "    ";
        $import = $this->getDirection() === 'import';
        $p = $this->getGuiData();
        if ($this->getDataTransfer() === 'interactive') {
            $interactiveXml = '<action type="dataflow/convert_adapter_http" method="'.($import ? 'load' : 'save').'">'.$nl;
            $interactiveXml .= '</action>';
            $fileXml = '';
        } else {
            $interactiveXml = '';
            $fileXml = '<action type="dataflow/convert_adapter_io" method="'.($import ? 'load' : 'save').'">'.$nl;
            $fileXml .= $tab.'<var name="type">'.$p['file']['type'].'</var>'.$nl;
            $fileXml .= $tab.'<var name="path">'.$p['file']['path'].'</var>'.$nl;
            $fileXml .= $tab.'<var name="filename"><![CDATA['.$p['file']['filename'].']]></var>'.$nl;
            if ($p['file']['type']==='ftp') {
                $hostArr = explode(':', $p['file']['host']);
                $fileXml .= $tab.'<var name="host"><![CDATA['.$hostArr[0].']]></var>'.$nl;
                if (isset($hostArr[1])) $fileXml .= $tab.'<var name="port"><![CDATA['.$hostArr[1].']]></var>'.$nl;
                if (!empty($p['file']['passive'])) $fileXml .= $tab.'<var name="passive">true</var>'.$nl;
                if ((!empty($p['file']['file_mode'])) && ($p['file']['file_mode'] == FTP_ASCII || $p['file']['file_mode'] == FTP_BINARY)) 
                    $fileXml .= $tab.'<var name="file_mode">'.$p['file']['file_mode'].'</var>'.$nl;
                if (!empty($p['file']['user'])) $fileXml .= $tab.'<var name="user"><![CDATA['.$p['file']['user'].']]></var>'.$nl;
                if (!empty($p['file']['password'])) $fileXml .= $tab.'<var name="password"><![CDATA['.$p['file']['password'].']]></var>'.$nl;
            }
            if ($import) $fileXml .= $tab.'<var name="format"><![CDATA['.$p['parse']['type'].']]></var>'.$nl;
            $fileXml .= '</action>'.$nl.$nl;
        }
        switch ($p['parse']['type']) {
            case 'excel_xml': 
                $parseFileXml = '<action type="dataflow/convert_parser_xml_excel" method="'.($import ? 'parse' : 'unparse').'">'.$nl;
                $parseFileXml .= $tab.'<var name="single_sheet"><![CDATA['.($p['parse']['single_sheet'] !== '' ? $p['parse']['single_sheet'] : '')
                    .']]></var>'.$nl;
                break;
            case 'csv': 
                $parseFileXml = '<action type="dataflow/convert_parser_csv" method="'.($import ? 'parse' : 'unparse').'">'.$nl;
                $parseFileXml .= $tab.'<var name="delimiter"><![CDATA['.$p['parse']['delimiter'].']]></var>'.$nl;
                $parseFileXml .= $tab.'<var name="enclose"><![CDATA['.$p['parse']['enclose'].']]></var>'.$nl;
                break;
            case 'xml': 
                $parseFileXml = '<action type="'.$this->getXMLParser($this->getEntityType()).'" method="'.($import ? 'parse' : 'unparse').'">'.$nl;
        }
        if (isset($p['parse']['fieldnames'])) 
            $parseFileXml .= $tab.'<var name="fieldnames">'.$p['parse']['fieldnames'].'</var>'.$nl;
        $parseFileXmlInter = $parseFileXml;
        $parseFileXml .= '</action>'.$nl.$nl;
        $mapXml = '';
        if (isset($p['map']) && is_array($p['map'])) {
            foreach ($p['map'] as $side => $fields) {
                if (!is_array($fields)) continue;
                foreach ($fields['db'] as $i => $k) {
                    if ($k == '' || $k == '0') {
                        unset($p['map'][$side]['db'][$i]); unset($p['map'][$side]['file'][$i]);
                    }
                }
            }
        }
        $mapXml .= '<action type="dataflow/convert_mapper_column" method="map">'.$nl;
        $map = $p['map'][$this->getEntityType()];
        if (sizeof($map['db']) > 0) {
            $from = $map[$import?'file':'db'];
            $to = $map[$import?'db':'file'];
            $mapXml .= $tab.'<var name="map">'.$nl;
            $parseFileXmlInter .= $tab.'<var name="map">'.$nl;
            foreach ($from as $i=>$f) {
                $mapXml .= $tab.$tab.'<map name="'.$f.'"><![CDATA['.$to[$i].']]></map>'.$nl;
                $parseFileXmlInter .= $tab.$tab.'<map name="'.$f.'"><![CDATA['.$to[$i].']]></map>'.$nl;
            }
            $mapXml .= $tab.'</var>'.$nl;
            $parseFileXmlInter .= $tab.'</var>'.$nl;
        }
        if ($p['map']['only_specified']) {
            $mapXml .= $tab.'<var name="_only_specified">'.$p['map']['only_specified'].'</var>'.$nl;
            $parseFileXmlInter .= $tab.'<var name="_only_specified">'.$p['map']['only_specified'].'</var>'.$nl;
        }
        $mapXml .= '</action>'.$nl.$nl;
        $parsers = $this->_getParsers();
        if ($import) {
            $parseFileXmlInter .= $tab.'<var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
        } else {
            $parseDataXml = '<action type="'.$parsers[$this->getEntityType()].'" method="unparse">'.$nl;
            $parseDataXml .= $tab.'<var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
            if (isset($p['export']['add_url_field'])) 
                $parseDataXml .= $tab.'<var name="url_field"><![CDATA['.$p['export']['add_url_field'].']]></var>'.$nl;
            $parseDataXml .= '</action>'.$nl.$nl;
        }
        $adapters = $this->_getAdapters();
        if ($import) {
            $entityXml = '<action type="'.$adapters[$this->getEntityType()].'" method="save">'.$nl;
            $entityXml .= $tab.'<var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
            $entityXml .= '</action>'.$nl.$nl;
        } else {
            $entityXml = '<action type="'.$adapters[$this->getEntityType()].'" method="load">'.$nl;
            $entityXml .= $tab.'<var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
            foreach ($p[$this->getEntityType()]['filter'] as $f => $v) {
                if (empty($v)) continue;
                if (is_scalar($v)) {
                    $entityXml .= $tab.'<var name="filter/'.$f.'"><![CDATA['.$v.']]></var>'.$nl;
                    $parseFileXmlInter .= $tab.'<var name="filter/'.$f.'"><![CDATA['.$v.']]></var>'.$nl;
                } elseif (is_array($v)) {
                    foreach ($v as $a => $b) {
                        if (strlen($b) == 0) continue;
                        $entityXml .= $tab.'<var name="filter/'.$f.'/'.$a.'"><![CDATA['.$b.']]></var>'.$nl;
                        $parseFileXmlInter .= $tab.'<var name="filter/'.$f.'/'.$a.'"><![CDATA['.$b.']]></var>'.$nl;
                    }
                }
            }
            $entityXml .= '</action>'.$nl.$nl;
        }
        if ($import) {
            $numberOfRecords = isset($p['import']['number_of_records']) ? $p['import']['number_of_records'] : 1;
            $decimalSeparator = isset($p['import']['decimal_separator']) ? $p['import']['decimal_separator'] : ' . ';
            $parseFileXmlInter .= $tab.'<var name="number_of_records">'.$numberOfRecords.'</var>'.$nl;
            $parseFileXmlInter .= $tab.'<var name="decimal_separator"><![CDATA['.$decimalSeparator.']]></var>'.$nl;
            if ($this->getDataTransfer() === 'interactive') {
                $xml = $parseFileXmlInter;
                $xml .= $tab.'<var name="adapter">'.$adapters[$this->getEntityType()].'</var>'.$nl;
                $xml .= $tab.'<var name="method">parse</var>'.$nl;
                $xml .= '</action>';
            } else {
                $xml = $fileXml;
                $xml .= $parseFileXmlInter;
                $xml .= $tab.'<var name="adapter">'.$adapters[$this->getEntityType()].'</var>'.$nl;
                $xml .= $tab.'<var name="method">parse</var>'.$nl;
                $xml .= '</action>';
            }
        } else $xml = $entityXml.$parseDataXml.$mapXml.$parseFileXml.$fileXml.$interactiveXml;
        $this->setGuiData($p);
        $this->setActionsXml($xml);
        return $this;
    }
}