<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Menupro
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Menupro_Block_Menu extends MST_Menupro_Block_Base {
	public function getMenuHtml($groupId) {
		$isDisableFrontEnd = Mage::getStoreConfig('menupro/setting/enable');
		if ($isDisableFrontEnd == 0) {
			return;
		}
		/* Get Group Permission Id of current login */
		$permission = Mage::helper ( "menupro" )->authenticate ();
		$menuCollection = $this->getMenuCollection ( $groupId, $permission);

		$DHTML = "";
		$switcher = 0;
		$groupInfo = $this->getGroupInfo($groupId);
		$_groupPosition = $groupInfo['position'];

		$arrow = $this->getArrow($_groupPosition);
		$arrowForSub = $this->getSubArrow($_groupPosition);
		/* <div id="mobile-header">
			<a id="responsive-menu-button" href="#sidr-main">Menu</a>
		</div> */
		$groupInfo = $this->getGroupInfo($groupId);
		/* if($groupInfo['responsive'] =='menu-creator-pro-side-panel'){
		$DHTML .= '<div id="mobile-header-'.$groupId.'"><a id="responsive-menu-button-'.$groupId.'" href="#menu-group-html-'.$groupId.'">Menu</a></div>';
		} */
		if ($menuCollection != NULL) {
			$DHTML .='<div id="menu-group-'.$groupId.'">';
			if (str_replace(' ','',$groupInfo['responsive']) == "menu-creator-pro-rp-switcherside-panel") {
					$DHTML .= '<ul class="responsive-menu-button menu-creator-pro"><li class="switcher"><a id="responsive-menu-button-'.$groupId.'" href="#menu-group-html-'.$groupId.'"><i class="mcp-icon fa-bars"></i>Menu</a></li></ul>';
			}
                        if ($groupId==1) {
                            $DHTML .= "<ul class='level0 " . $this->getGroupOptions($groupId) . "'>";
                        } else {
                            $DHTML .= "<ul class='level0 " . /*$this->getGroupOptions($groupId) . */"'>";
                        }
			foreach ( $menuCollection as $menu ) {
				if ($menu->getParentId () == 0) {
					$data = $this->getMenuData ( $menu, $permission);
					//If responsive of group is switcher
					$groupInfo = $this->getGroupInfo($groupId);
					if ($groupInfo['responsive'] == "menu-creator-pro-rp-switcher") {
						if ($switcher == 0) {
							$DHTML .= '<li class="switcher"><a href="#"><i class="mcp-icon fa-bars"></i>Menu</a></li>';
						}
						$switcher++;
					}
					
					// ==== Level 0 ====== //
					
					$DHTML .= '<li class="' . $data ['liClasses'] . ' level0 col_'. $menu->getDropdownColumns() .'">';
					if ($data ['block'] != '') {
						$DHTML .= "<div class='static-block ". $data['liClasses'] ."'>" . $data ['block'] . "</div>";
					} else {
						if (count ( $data ['childcollection'] ) > 0) {
							$aContent = $data ['aIcon'] . $data ['aImage'] . $this->__($data ['aText']);
							$DHTML .= '<a title="' . $data['aTitle'] . '" href=" ' . $data ['aHref'] . '" target="' . $data ['target'] . '"> ' . $aContent . '</a>' . $arrow;
						} else {
							/**
							 * With menu level = 0; when select autosub; show dropdown
							 * menu like default*
							 */
							$dataHover = $caret = $dropdownT  = $plus = "";
							if ($data['isAutoShowSub'] == true) {
								$dataHover = $this->_data_hover;
								$dropdownT = $this->_dropdown_toggle;
								$caret = $arrow;
								$plus = $this->_plus;
							}
							$aContent = $data ['aIcon'] . $data ['aImage'] . $this->__($data ['aText']);
							$DHTML .= '<a title="'. $data['aTitle'] .'" href="' . $data ['aHref'] . '" target="' . $data ['target'] . '"' . $dataHover . '>' . $aContent .'</a>' . $caret;
							if ($data['isAutoShowSub'] == true) {
								//Auto show sub menu in level 0;
								$DHTML .= "<div class='grid-container3'>";
								$DHTML .= $this->autoSub ( $menu->getUrlValue (), $menu->getAutosub (), false, 3, $arrowForSub, $arrowForSub );
								$DHTML .= "</div>";
							}
						}   
					}
					
					if (count ( $data ['childcollection'] ) > 0) {
						$menuColumn = $menu->getDropdownColumns();
						if ($menuColumn == 1) {
							// 1 column = 3
							$menuColumn = 3;
						}
						$DHTML .= "<div class='grid-container" . $menuColumn . "'>";
						foreach ( $data ['childcollection'] as $menu1 ) {
							// ======= Sub Header Item (as Level 1) =========//
							$headerData = $this->getMenuData ( $menu1, $permission);
							// Get dropdown as column unit
							$menu1Column = $menu1->getDropdownColumns();
							if ($menu1Column == 1) {
								$menu1Column = 3;
							}
							$DHTML .= "<div class='grid-column grid-column" . $menu1Column . " " . $menu1->getClassSubfix() . " '>";
							if ($menu1->getType () != 6) {
								if ($headerData ['hide_sub_header'] != 1) {
									// Check hide header item or not
									if ($menu1->getType () == 8) {
										$DHTML .= "<span class='divider'></span>";
									} else {
										$aContent = $headerData ['aIcon'] . $headerData ['aImage'] . $headerData ['aText'];
										$DHTML .= '<a title="' . $headerData['aTitle'] . '" class="nav-header" href="' . $headerData ['aHref'] . '" target="' . $headerData ['target'] . '">' . $aContent . '</a>';
									}
								}
								//Not show menu has type = static block//
								$normalArray = array();
								foreach ($headerData['childcollection'] as $menuItem) {
									if ($menuItem->getType() != 6)	{
										$normalArray [] = $menuItem->getMenuId();
									}								
								}
								if (count ( $normalArray ) > 0) {
									$DHTML .= "<ul class='level1'>";
									//$DHTML .= $this->getAutoSubMenuLi ( $menu1->getUrlValue (), $menu1->getAutosub () );
									foreach ( $headerData['childcollection'] as $menu2 ) {
										if (in_array ( $menu2->getMenuId (), $normalArray )) {
											if ($menu2->getType () == 8) {
												$DHTML .= "<li class='divider'></li>";
											} else {
												$data1 = $this->getMenuData ( $menu2, $permission);
												// =========== Level 2 ===============
												$hasSub = $caret = "";
												if (count ( $data1 ['childcollection'] ) > 0 || $data1 ['isAutoShowSub'] == true) {
													$hasSub = "has-submenu";
													$caret = $arrowForSub;
												}
												$DHTML .= "<li class='" . $data1 ['liClasses'] . $hasSub . "'>";
												if (count ( $data1 ['childcollection'] ) > 0) {
													$aContent = $data1 ['aIcon'] . $data1 ['aImage'] . $data1 ['aText'];
													$DHTML .= '<a title="'. $data1['aTitle'] .'" href="' . $data1 ['aHref'] . '" target="' . $data1 ['target'] . '">' . $aContent . '</a>' . $caret;
													$DHTML .= "<div class='grid-container" . $menu1Column . "'>";//Get dropdown_columns value of nav-header
													$DHTML .= "<ul class='level2'>";
													//$DHTML .= $this->getAutoSubMenuLi ( $menu2->getUrlValue (), $menu2->getAutosub () );
													foreach ( $data1 ['childcollection'] as $menu3 ) {
														if ($menu3->getType () == 8) {
															$DHTML .= "<li class='divider'></li>";
														} else {
															$data2 = $this->getMenuData ( $menu3, $permission);
															// ============ Level 3
															$hasSub = $caret = "";
															if (count ( $data2 ['childcollection'] ) > 0 || $data2 ['isAutoShowSub'] == true) {
																$hasSub = "has-submenu";
																$caret = $arrowForSub;
															}
															$DHTML .= "<li class='" . $data2 ['liClasses'] . $hasSub . "'>";
															if (count ( $data2 ['childcollection'] ) > 0) {
																$aContent = $data2 ['aIcon'] . $data2 ['aImage'] . $data2 ['aText'];
																$DHTML .= '<a title="' . $data2['aTitle'] . '" href="' . $data2 ['aHref'] . '" target="' . $data2 ['target'] . '">' . $aContent . '</a>' . $caret;
																$DHTML .= "<div class='grid-container" . $menu1Column . "'>";
																$DHTML .= "<ul class='level3'>";
																//$DHTML .= $this->getAutoSubMenuLi ( $menu3->getUrlValue (), $menu3->getAutosub () );
																foreach ( $data2 ['childcollection'] as $menu4 ) {
																	$data3 = $this->getMenuData ( $menu4, $permission);
																	if ($menu4->getType () == 8) {
																		$DHTML .= "<li class='divider'></li>";
																	} else {
																		// =============== Level 4
																		$hasSub = $caret = "";
																		if (count ( $data3 ['childcollection'] ) > 0 || $data3 ['isAutoShowSub'] == true) {
																			$hasSub = "has-submenu";
																			$caret = $arrowForSub;
																		}
																		$DHTML .= "<li class='" . $data3 ['liClasses'] . $hasSub . "'>";
																		if (count ( $data3 ['childcollection'] ) > 0) {
																			$aContent = $data3 ['aIcon'] . $data3 ['aImage'] . $data3 ['aText'];
																			$DHTML .= '<a title="' . $data3['aTitle'] . '" href="' . $data3 ['aHref'] . '" target="' . $data3 ['target'] . '">' . $aContent . '</a>' . $caret;
																			$DHTML .= "<div class='grid-container" . $menu1Column . "'>";
																			$DHTML .= "<ul class='level4'>";
																			//$DHTML .= $this->getAutoSubMenuLi ( $menu4->getUrlValue (), $menu4->getAutosub () );
																			foreach ( $data3 ['childcollection'] as $menu5 ) {
																				if ($menu5->getType () == 8) {
																					$DHTML .= "<li class='divider'></li>";
																				} else {
																					$data4 = $this->getMenuData ( $menu5, $permission);
																					// ========
																					// Level
																					// 5
																					// ================//
																					$hasSub = $caret = "";
																					if (count ( $data4 ['childcollection'] ) > 0 || $data4 ['isAutoShowSub'] == true) {
																						$hasSub = "has-submenu";
																						$caret = $arrowForSub;
																					}
																					$DHTML .= "<li class='" . $data4 ['liClasses'] . $hasSub . "'>";
																					$aContent = $data4 ['aIcon'] . $data4 ['aImage'] . $data4 ['aText'];
																					$DHTML .= '<a title="' . $data4['aTitle'] . '" href="' . $data4 ['aHref'] . '" target="' . $data4 ['target'] . '">' . $aContent . '</a>' . $caret;
																					$DHTML .= "</li>";
																				}
																			}
																			$DHTML .= "</ul>";
																			$DHTML .= "</div>";
																		} else {
																			if ($data3 ['block'] != "") {
																				$DHTML .= "<div class='static-block ". $data3['liClasses'] ."'>" . $data3['block'] . "</div>";
																			} else {
																				$dataHover = $dropdownT = $plus = "";
																				if ($data3 ['isAutoShowSub'] == true) {
																					$dataHover = $this->_data_hover;
																					$dropdownT = $this->_dropdown_toggle;
																					$plus = $arrowForSub;
																				}
																				$aContent = $data3 ['aIcon'] . $data3 ['aImage'] . $data3 ['aText'];
																				$DHTML .= '<a title="'. $data3['aTitle'] .'" href="' . $data3 ['aHref'] . '" target="' . $data3 ['target'] . '"' . $dataHover . '>' . $aContent . '</a>' . $plus;
																			}
																			if ($data3['isAutoShowSub']) {
																				$DHTML .= "<div class='grid-container3'>";
																				$DHTML .= $this->autoSub ( $menu4->getUrlValue (), $menu4->getAutosub (), false, 3, $arrowForSub );
																				$DHTML .= "</div>";
																			}
																		}
																		$DHTML .= '</li>';
																	}
																}
																$DHTML .= "</ul>";
																$DHTML .= "</div>";
															} else {
																if ($data2 ['block'] != "") {
																	$DHTML .= "<div class='static-block ". $data2['liClasses'] ."'>" . $data2 ['block'] . "</div>";
																} else {
																	$dataHover = $dropdownT = $plus = "";
																	if ($data2 ['isAutoShowSub'] == true) {
																		$dataHover = $this->_data_hover;
																		$dropdownT = $this->_dropdown_toggle;
																		$plus = $arrowForSub;
																	}
																	$aContent = $data2 ['aIcon'] . $data2 ['aImage'] . $data2 ['aText'];
																	$DHTML .= '<a title="'. $data2['aTitle'] .'" href="' . $data2 ['aHref'] . '" target="' . $data2 ['target'] . '"' . $dataHover . '>' . $aContent . '</a>' . $plus;
																}
																if ($data2['isAutoShowSub']) {
																	$DHTML .= "<div class='grid-container3'>";
																	$DHTML .= $this->autoSub ( $menu3->getUrlValue (), $menu3->getAutosub (), false, 3, $arrowForSub );
																	$DHTML .= "</div>";
																}
															}
															$DHTML .= "</li>";
														}
													}
													$DHTML .= "</ul>";
													$DHTML .= "</div>";
												} else {
													/**Check menu has selected auto show sub and has sub menu**/
													$dataHover = $dropdownT = $plus = "";
													if ($data1 ['isAutoShowSub'] == true) {
														$dataHover = $this->_data_hover;
														$dropdownT = $this->_dropdown_toggle;
														$plus = $arrowForSub;
													}
													$aContent = $data1 ['aIcon'] . $data1 ['aImage'] . $data1 ['aText'];
													$DHTML .= '<a title="'. $data1['aTitle'] .'" href="' . $data1 ['aHref'] . '" target="' . $data1 ['target'] . '"' . $dataHover . '>' . $aContent . '</a>' . $plus;
													if ($data1 ['isAutoShowSub'] == true) {
														$DHTML .= "<div class='grid-container3'>";
														$DHTML .= $this->autoSub ( $menu2->getUrlValue (), $menu2->getAutosub (), false, 3, $arrowForSub );
														$DHTML .= "</div>";
													}
												}
												$DHTML .= "</li>";
											}
										}
									}
									$DHTML .= "</ul>";
								} else {
									if ($headerData['isAutoShowSub']) {
										$showMenuInLevel2 = true;
										$DHTML .= "<div class='grid-container3'>";
										$DHTML .= $this->autoSub ( $menu1->getUrlValue (), $menu1->getAutosub (), $showMenuInLevel2, 3, $arrowForSub );
										$DHTML .= "</div>";
									}
								}
							} else {
								$DHTML .= "<div class='static-block ". $headerData['liClasses'] ."'>" . $headerData ['block'] . "</div>";
							}
							$DHTML .= "</div>";
						}
						$DHTML .= "</div>";
					}
					$DHTML .= "</li>";
				}
			}
			$DHTML .= "</ul>";
			$DHTML .= "</div>";
			if(str_replace(' ','',$groupInfo['responsive']) == "menu-creator-pro-rp-switcherside-panel"){	
		$DHTML .= "<script>jQuery('#responsive-menu-button-".$groupId."').sidr({name:'sidr-main',source:'#menu-group-".$groupId."'});jQuery('#sidr-id-responsive-menu-button-".$groupId."').sidr('close', 'sidr-main');</script>";
			}
		}
		return $DHTML;
	}
	public function getMenuData($menu, $permission) {
		$isDevelopMode = Mage::helper('menupro')->isDevelopMode();
		$this->_menuLink = $this->getMenuLink ( $menu->getUrlValue (), $menu->getType () );
		// Reset all val
		$this->resetMenuItemVar ();
		// Check has child or not
		$childCollection = $this->getChildMenu ( $menu->getGroupId (), $menu->getMenuId (), $permission );
		if (count ( $childCollection ) > 0) {
			$this->_parent = true;
		}
		// Prepare classes for <li> tag. Level = 0
		$this->_liClasses .= $menu->getClassSubfix() . " ";
		// Text align class
		if ($menu->getTextAlign() == "right") {
			$this->_liClasses .= "right ";
		}
		// Hide on phone or tablet
		if ($menu->getHidePhone() == 1) {
			$this->_liClasses .= "hidden-phone ";
		}
		if ($menu->getHideTablet() == 1) {
			$this->_liClasses .= "hidden-pad ";
		}
		if ($this->_menuLink == Mage::helper ( 'menupro' )->getCurrentUrl ()) {
			$this->_liClasses .= $this->_liActiveClass;
		}
		$isAutoShowSub = false;
		if ($menu->getAutosub() == 1) {
			$categoryHasChild = $this->getChildIds($this->_tree, $menu->getUrlValue());
			if (count($categoryHasChild) > 0) {
				$isAutoShowSub = true;
			}
		}
		if ($this->_parent == true || $isAutoShowSub) {
			//$groupMenuType = $this->getGroupMenuType ( $menu->getGroupId () );
			//$this->_liClasses .= $this->_liHasSubClasses[$groupMenuType];
			$this->_liClasses .= $this->_liHasSubClasses;
		}
		// Prepare for <a> tag. Check type is static block or not
		if ($menu->getType() != 6) {
			if ($menu->getImageStatus () == 1 && $menu->getImage () != "") {
				$image = Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_MEDIA ) . '/' . $menu->getImage ();
				//$this->_aImage = "style='background-image:url(" . $image . ")'";
				$this->_aImage = "<img src='" . $image ."'  />";
				// $this->_aImage = "<img src='" . $image ."' width='22px'
				// height='22px' />";
			}
			$menu->getTarget () == 1 ? $this->_aTarget = "_self" : $this->_aTarget = "_blank";
			$this->_aHref = $this->_menuLink;
			
			//Use category title as default
			$menuTitle = $menu->getTitle();
			if ($menu->getType() == 4 && $menu->getUseCategoryTitle() == 2) {
				$menuTitle = Mage::helper('menupro')->getCategoryTitle($menu->getUrlValue());
			}
			
			if ($this->_aHref == "#") {
				if (!$isDevelopMode) {
					$this->_aText = '<span class="title"><?php echo $this->__("'. $menuTitle .'") ?></span>';
				} else {
					$this->_aText = "<span class='title'>" . $this->__($menuTitle) . "</span>";
				}
			} else {
				if (!$isDevelopMode) {
					$this->_aText = '<span><?php echo $this->__("'. $this->__($menuTitle) .'") ?></span>';
					
					$baseLink = $this->getMCPBaseUrl();
					$baseWeb = $this->getMCPUrl();
					if (strpos($this->_urlValue, $baseLink) !== false) {
						$urlPath = str_replace($baseLink, '', $this->_aHref);
						$this->_aHref = '<?php echo $this->getMCPBaseUrl() ?>' . $urlPath ;
					} else if (strpos($this->_urlValue, $baseWeb) !== false) {
						$urlPath = str_replace($baseWeb, '', $this->_aHref);
						$this->_aHref = '<?php echo $this->getMCPUrl() ?>' . $urlPath ;
					}	
					
				} else {
					$this->_aText = "<span>" . $menuTitle . "</span>";
				}
			}
            //Get SEO title if exists
            if ($menu->getDescription() != "") {
                $this->_aTitle = $menu->getDescription();
            } else {
                $this->_aTitle = $this->__($menu->getTitle());
            }
			if (!$isDevelopMode) {
				$tempTitle = $this->_aTitle;
				$this->_aTitle = '<?php echo $this->__("'. $tempTitle .'") ?>';
			}
			if($menu->getImageStatus() == 1){
				//$this->_aIcon = "<i class='" . $menu->getIconClass () . "' ". (($this->_aImage != '') ? $this->_aImage : '') ."></i>";
				$this->_aIcon = "<i class='" . $menu->getIconClass () . "'  >" . $this->_aImage . "</i>";
			}
		} else {
			$this->_block = $this->_menuLink->toHtml ();
		}
		return array (
			// 'menuLink' => $this->_menuLink,
			'liClasses' => $this->_liClasses,
			'aText' => $this->_aText,
            'aTitle' => $this->_aTitle,
			'aHref' => $this->_aHref,
			'target' => $this->_aTarget,
			'aImage' => '',//Just pass notice error
			'aIcon' => $this->_aIcon,
			'dropdown_columns' => $menu->getDropdownColumns (),
			'hide_sub_header' => $menu->getHideSubHeader (),
			'block' => $this->_block,
			'childcollection' => $childCollection,
			'autosub' => $menu->getAutosub(),
			'isAutoShowSub' => $isAutoShowSub
		);
	}
	public function getStaticHtml($groupId) {
		$filename = $this->getMenuFilename($groupId);
		$path = $this->menuDesignDir();
		if (file_exists($path . $filename)) {
			$block = $this->getLayout()->createBlock('menupro/menu')->setTemplate("menupro/static/" . $filename)->toHtml();
		} else {
			//Create new static html file
			$menuHtml = $this->getMenuHtml($groupId);
			$response = $this->exportMenupro($menuHtml, $groupId);
			if ($response['success']) {
				$block = $this->getLayout()->createBlock('menupro/menu')->setTemplate("menupro/static/" . $response['filename'])->toHtml();
			}
		}
		return $block;
	}
	public function removeAllStaticHtml() {
		$refresh = false;
		try {
			$files = glob($this->menuDesignDir() . "*" ); // get all file names
			// If there is no file
			if (!$files) {
				return true;
			}
			foreach($files as $file){ // iterate files
				if(is_file($file)) {
					$result = unlink($file); // delete file
					if ($result) {
						$refresh = true;
					}
				}
			}
		} catch (Exception $e) {
			Mage::log($e, null, 'menupro.log');
		}
		return $refresh;
	}
}