<?php
class MST_Menupro_Block_Adminhtml_Messages extends Mage_Core_Block_Messages
{
	const LICENSE_CHECK = "license_invalid";
	public function setMenuMessage( $type, $message) {
		$data = array(
			'type' => $type,
			'message' => $message
		);
		Mage::getSingleton("core/session")->setMenuMessage($data);
	}
	public function getMenuMessage() {
		return Mage::getSingleton("core/session")->getMenuMessage();
	}

	public function showMenuMessage()
    {
		$types = array(
            Mage_Core_Model_Message::ERROR,
            Mage_Core_Model_Message::WARNING,
            Mage_Core_Model_Message::NOTICE,
            Mage_Core_Model_Message::SUCCESS
        );
        $html = '';
		$message = $this->getMenuMessage();
		$showMessage = Mage::getSingleton('adminhtml/session')->getShowMessage();
		if (($message['message'] != "") && ((int) $showMessage != 0 /*|| $showMessage === self::LICENSE_CHECK*/)) {
			foreach ($types as $type) {
				if ( $type == $message['type'] ) {
					if ( !$html ) {
						$html .= '<' . $this->_messagesFirstLevelTagName . ' class="messages">';
					}
					$html .= '<' . $this->_messagesSecondLevelTagName . ' class="' . $type . '-msg">';
					$html .= '<' . $this->_messagesFirstLevelTagName . '>';

					$html.= '<' . $this->_messagesSecondLevelTagName . '>';
					$html.= '<' . $this->_messagesContentWrapperTagName . '>';
					$html.= ($this->_escapeMessageFlag) ? $this->htmlEscape($message['message']) : $message['message'];
					$html.= '</' . $this->_messagesContentWrapperTagName . '>';
					$html.= '</' . $this->_messagesSecondLevelTagName . '>';
					
					$html .= '</' . $this->_messagesFirstLevelTagName . '>';
					$html .= '</' . $this->_messagesSecondLevelTagName . '>';
				}
			}
			if ( $html) {
				$html .= '</' . $this->_messagesFirstLevelTagName . '>';
			}
		}
		if ($showMessage != self::LICENSE_CHECK) {
			Mage::getSingleton('adminhtml/session')->setShowMessage(0);
		}
        return $html;
    }
}