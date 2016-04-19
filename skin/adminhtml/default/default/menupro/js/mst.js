/**
* Magento Support Team- http://magebay.com
* Menu Creator Pro
* Version 2.0
* http://menucreatorpro.com
*/
var mstMCP = jQuery.noConflict();
mstMCP(document).ready(function($) {
	/**
	* Menu type :
	* 	1 => CMS Page
	* 	4 => Category Page
	* 	3 => Weblinks
	* 	5 => Product Page
	* 	6 => Static Block
	* 	7 => href=#
	*	8 => Seperator line
	*/
	var parentOptions = new Array();
	MCP = {
		/** Show and hide some field when [id=#type] change*/
		showAndHideInput : function( par ) {
			for(var i = 0; i < par.length - 1; i+=2){
				// If true->show, else hide
				if(par[i+1] == true){
					$("#menupro_form #" + par[i]).removeAttr('disabled');
					$("#menupro_form #" + par[i]).val('');
					$("#menupro_form #" + par[i]).parent().parent().show();
				}else{
					$("#menupro_form #" + par[i]).attr('disabled',true);
					$("#menupro_form #" + par[i]).parent().parent().hide();
				}
			}
		},
		/** Fire action when #type dropdown changed */
		menuTypeChangeEvent : function() {
			/** When type dropdown change */
			$("#menupro_form #type").change(function() {
				var type = $(this).val();
				var optionText = $('#type option:selected').text();
				//If title hide by seperator before, show again
				$("#menupro_form #title").val('');
				$("#menupro_form #title").parent().parent().show();
				if ( type == 4 ) {
					MCP.hideFormField( type );
				} else if ( type == 1 || type == 6 || type == 3 || type == 5 ) {
					//Remove disable attr if category has selected before
					$("#menupro_form #title").removeAttr('readonly');
					MCP.hideFormField( type );
				} else if (type == 7) {
					//Remove disable attr if category has selected before
					$("#menupro_form #title").removeAttr('readonly');
					MCP.hideFormField( type );
					$("#menupro_form #url_value").val( "#" );
				} else if (type == 8) {
					var separatorLine = $("#separator_line").val();
					MCP.hideFormField( type );
					$("#menupro_form #url_value").val( separatorLine );
					$("#menupro_form #title").val( separatorLine );
					$("#menupro_form #title").parent().parent().hide();
				} else {
					// Most used links
					//Remove disable attr if category has selected before
					$("#menupro_form #title").removeAttr('readonly');
					MCP.hideFormField( type );
					// Type is value too
					$("#menupro_form #url_value").val( type );
					$("#menupro_form #title").val( optionText );
					$("#title").focus();
				}
			});
		},
		/** Fill selected value to url_value when category_id or cms_page ... dropdown changed */
		selecedtUrlValue : function( id ) {
			$("#menupro_form #" + id).bind("change",function(){
				var value = $("#menupro_form #" + id + " option:selected").val();
				$("#menupro_form #url_value").val( value );
				if (id == "category_id" && value != "") {
					var categoryTitle = $.trim($("#menupro_form #" + id + " option:selected").text());
					//Auto fill data to title and make it read only
					$('#title').val(categoryTitle);
					$('#title').attr('readonly', 'readonly');
					//Unchecked checkbox field if it checked
					$("#use_category_title").attr('checked', false);
					$()
				} else {
					$('#title').val('');
					$('#title').removeAttr('readonly');
				}
			});
		},
		/** Auto fill data to url_value when typing to custom_urlvalue field */
		autoFillUrlValue : function( id ) {
			$("#menupro_form #" + id).bind("keyup keydown",function() {
				var value = $(this).val();
				$("#menupro_form #url_value").val( value );
			});
		},
		/**
		* When click edit button form grid page, selected option or textbox
		* have to fill auto when data match with url_value
		*/
		activeSelectedOption : function(selectorId, urlValue) {
			var optionValue;
			$(selectorId).val(urlValue);
			$('#url_value').val(urlValue);
			//Disabled #custom_urlvalue for pass validation of edit form before save
			$("#menupro_form #custom_urlvalue").attr('disabled', true);
		},
		formatTextarea : function() {
			var installScript = $('#description').text();
			if(installScript != ""){
				$('.help-install').hide();
				$('.help-note').show();
			}
			var fomarted = installScript.replace(/<br\/>/gi, "\n");
			$('#description').html(fomarted);
		},
		hideStoreId : function() {
			/**
			 * When you allow menu visiable in all store view, look for
			 * menucontroller(saveAction->save storeid)
			 */
			$("#menupro_form #storeids option").each(function() {
				if($(this).val() == 0){
					if( $(this).attr("selected") == "selected" ){
						$("#menupro_form #storeids option").each(function() {
							if($(this).val() !=0 ){
								$(this).removeAttr( "selected" );
							}
						});
					}
				}
			});
		},
		hideFormField : function( type ) {
			switch( parseInt(type) ) {
				case 1 :
					MCP.showAndHideInput( myarray = new Array(
											"category_id", false,
											"cms_page", true,
											"static_block", false,
											"custom_urlvalue", false,
											"autosub", false,
											"use_category_title", false,
											"product_id", false
										));
					break;
				case 3 :
					MCP.showAndHideInput( myarray = new Array(
											"category_id", false,
											"cms_page", false,
											"static_block", false,
											"custom_urlvalue", true,
											"autosub", false,
											"use_category_title", false,
											"product_id", false
										));
					break;
				case 4 :
					MCP.showAndHideInput( myarray = new Array(
											"category_id", true,
											"cms_page", false,
											"static_block", false,
											"custom_urlvalue", false,
											"autosub", true,
											"use_category_title", true,
											"product_id", false
										));
					break;
				case 5 :
					MCP.showAndHideInput( myarray = new Array(
											"category_id", false,
											"cms_page", false,
											"static_block", false,
											"custom_urlvalue", false,
											"autosub", false,
											"use_category_title", false,
											"product_id", true
										));
					break;	
				case 6 :
					MCP.showAndHideInput( myarray = new Array(
											"category_id", false,
											"cms_page", false,
											"static_block", true,
											"custom_urlvalue", false,
											"autosub", false,
											"use_category_title", false,
											"product_id", false
										));
					break;
				case 7 :
					MCP.showAndHideInput( myarray = new Array(
											"category_id", false,
											"cms_page", false,
											"static_block", false,
											"custom_urlvalue", false,
											"autosub", false,
											"use_category_title", false,
											"product_id", false
										));
					break;
				default :
					MCP.showAndHideInput( myarray = new Array(
											"category_id", false,
											"cms_page", false,
											"static_block", false,
											"custom_urlvalue", false,
											"autosub", false,
											"use_category_title", false,
											"product_id", false
										));
					break;
			}
		},
		/** Add depth attribute to li tag just for style purpose, and for add childmenu button */
		addDepthToLi : function( ul_id ) {
			$("#" + ul_id + " li").each(function() {
				var id = $(this).attr("id");
				var depth = $("#" + id,"#" + ul_id).parents("ul").size();
				$("#" + id).addClass("level" + (depth - 1));
			});
		},
		/** Update menu parent id, position to all menu when menu tree change */
		updateMenu : function() {
			var url = $("#baseurl").val() + "menupro/index/updatemenu";
			var groupId, parentId, pLiId,
				liId, tempId, depth, isGroupId;
			var data = "";
			$("#sitemap li").each(function() {
				tempId = $(this).attr("id");
				if(tempId != 0){
					liId = tempId.replace(/[^0-9]/gi,"");
					depth = $("#" + tempId, "#sitemap").parents("ul").size();
					/** depth - 1== value of dept attr of li */
					if((depth - 1) > 0){
						// go up to ul if depth >0 to get id of li tag
						pLiId = $(this).parent().parent().attr( "id" );
					}else{
						pLiId = '0';//$(this).parent().attr("id");
					}	
					parentId = pLiId.replace(/[^0-9]/gi,"");
					isGroupId = $(this).prev().attr( "id" );
					if(isGroupId){
						if( isGroupId.match( "group" ) ){
							groupId = isGroupId.replace(/[^0-9]/gi,"");	
						}
					}
					data += liId + "-" + groupId + "-" + parentId + ",";
				}
			});
			/** Pass data via ajax function to save to database */
			$.ajax({
				type : "POST",
				url : url,
				data : "saveString=" + data,
				beforeSend : function() {
					$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px;");
					$("#loading-mask").show();
				},
				success : function( data ) {
					if(data == "Ok"){
						$("#loading-mask").hide();
					}else{
						alert(data);
						window.location.reload();
					}
				}
			});
		},
		activeDeleteEvent : function() {
			var liId;
			$('#sitemap .sm_delete').click(function() {
				liId = $(this).parents('li').attr( 'id' );
				var child_ids = MCP.getChildMenuIds(liId);
				MCP.deleteMenu(child_ids);
			});
		},
		activeAddChildEvent : function() {
			var liId, liClass, liTopId;
			var groupId;
			$('#sitemap .sm_addChild').click(function() {
				liId = $(this).parents('li').attr( 'id' );
				liClass = $('#' + liId).attr( 'class' );
				/**
				* If level of current menu not = 0, then loop back to get parent id that has level = 0
				* Get id of li with level = 0, then use prevAll loop back to find group id of current menu
				* Menu Id format: m-id => use split to get second position
				* Group Id format: group-id => same as menu id
				*/
				if( liClass.match( 'level0' ) ) {
					groupId = $('#' + liId).prevAll('.group-menu').attr('id');
				} else {
					liTopId = $('#' + liId).parents('li.level0').attr('id');
					groupId = $('#' + liTopId).prevAll('.group-menu').attr('id');
				}
				MCP.addChild( liId.split('-')[1], groupId.split('-')[1] );
			});
		},
		addChild : function( id, groupId ) {
			var formAction;
			var baseurl = $('#baseurl').val();
			//Filter group id, then parent option will ready to use
			MCP.filterByGroupId(groupId);
			$('#parent_id option').each(function() {
				if( $(this).val() == id ){
					$(':input','#edit_form')
						.not(':button, :submit, :reset, :hidden')
						.val('')
						.removeAttr('checked')
						.removeAttr('selected');
					MCP.hideFormField( $("#type").val() );
					//Rest 2 hidden field
					$('#custom_urlvalue, #url_value').val('');
					//Active selected option 
					$('#group_id').val( groupId );
					$('#parent_id').val(id);
					$('#storeids').val( 0 );
					//Hide parent_id and groupid
					$("#group_id, #parent_id").parent().parent().hide();
					//SrollTo top
					$('html, body').animate({
						 scrollTop: $("#menupro_form").offset().top
					}, 500);
					//Swich layout
					MCP.switchColumnLayout();
					//Change action form url if exists param id
					formAction = $('#edit_form').attr('action');
					var index = formAction.indexOf("key");
					var newAction = formAction.substr(index, formAction.length - index + 1);
					//Update action of form
					$('#edit_form').attr('action', baseurl + 'menupro/adminhtml_menupro/save/' + newAction);
					$('#type').focus();
				}
			});
			//Set menu_id field to be empty to add new menu
			$("#menu_id").val('');
		},
		deleteMenu : function(ids) {
			var baseurl = $( '#baseurl' ).val();
			if( ids.length > 1 ) {
				if(!confirm( "Do you want to delete this menu and it's childs?" )) {
					return false;
				}
			}else{
				if( !confirm( 'Do you want to delete this menu?' ) ) {
					return false;
				}
			}
 			$.ajax({
				type : "POST",
				url : baseurl + "menupro/index/deletemenu",
				data : 'ids=' + ids.join(','),
				beforeSend : function(){
					// Use default magento class
					$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px;");
					$("#loading-mask").show();
				},
				success : function( data ) {
					if(data == "Ok"){
						$("#sitemap li").each(function() { 
							// Delete parent li if it has child
							if($(this).attr("id") == ids[0]){
								$(this).remove();
								return;
							}
						});
						$("#loading-mask").hide();
						// If in edit status, after delete menu, we need reload page
						if($('#title').val() != ""){
							window.location.reload();
						}
					}else{
						alert(data);
						window.location.reload();
					}
				}
			});
		},
		/**
		* Get all sub menu item id of parent id
		* @param parentId
		*/
		getChildMenuIds : function( parentId ) {
			var ids = new Array();
			ids.push(parentId);
			$('#' + parentId +' li').each(function() {
				ids.push( $(this).attr('id') );
			});
			return ids;
		},
		editMenu : function( url ) {
			$.ajax({
				type : "GET",
				url : url + "isAjax=true",
				beforeSend : function(){
					$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px;");
					$("#loading-mask").show();
				},
				success : function( data ) {
					if(data != ""){
						var menuObject = $.parseJSON(data);
						var menuId, groupId, title, image,
							type, parentId, urlValue, imageStatus,
							storeids, permission, status, target,
							dropdownColumns, iconClass, hideSubHeader, 
							autosub, classSubfix, useCategoryTitle,
							textAlign, hidePhone, hideTablet, description;
						var index, str1, str2, action;
						//Assign menu form var
						menuId = menuObject.menu_id;
						groupId = menuObject.group_id;
						title = menuObject.title;
						image = menuObject.image;
						type = menuObject.type;
						parentId = menuObject.parent_id;
						urlValue = menuObject.url_value;
						imageStatus = menuObject.image_status;
						permission = menuObject.permission;
						status = menuObject.status;
						target = menuObject.target;
						storeids = menuObject.storeids;
						dropdownColumns = menuObject.dropdown_columns;
						iconClass = menuObject.icon_class;
						hideSubHeader = menuObject.hide_sub_header;
						autosub = menuObject.autosub;
						classSubfix = menuObject.class_subfix;
						useCategoryTitle = menuObject.use_category_title;
						textAlign = menuObject.text_align;
						hidePhone = menuObject.hide_phone;
						hideTablet = menuObject.hide_tablet;
                        description = menuObject.description;
						
						//Fill data to form
						MCP.fillDataToEditForm(	menuId, groupId, title, image,
												type, parentId, urlValue, imageStatus,
												permission, status, target, storeids,
												dropdownColumns, iconClass, hideSubHeader, 
												autosub, classSubfix, useCategoryTitle, 
												textAlign, hidePhone, hideTablet, description);
						$("#menu_id").val(menuId);
						$("#loading-mask").hide();
						MCP.switchColumnLayout();
					}
				}
			});
			return false;
		},
		/** Fill menu data from database to edit form */
		fillDataToEditForm : function(	menuId, groupId, title, image,
										type, parentId, urlValue, imageStatus,
										permission, status, target, storeids,
										dropdownColumns, iconClass, hideSubHeader, 
										autosub, classSubfix, useCategoryTitle, 
										textAlign, hidePhone, hideTablet, description)
		{
			// Filter parent dropdown by group_id after page load
			MCP.filterByGroupId( groupId );
			// Change header text from Add Menu to Edit Menu
			$("#menupto-title").text("Menu Manager: Edit '" + title + "'");
			// Text field
			$("#menupro_form #title").val(title);
			//If select category before, make sure you have to remove readonly when edit the other menu
			$('#title').removeAttr('readonly');
			$("#menupro_form #description").val(description);
            $("#menupro_form #icon_class").val(iconClass);
			$("#menupro_form #class_subfix").val(classSubfix);
			//Checkbox field
			/*if (autosub == 1) {
				$('#menupro_form #autosub').val(autosub);
				$('#menupro_form #autosub').attr('checked', 'checked');
			} else {
				$('#menupro_form #autosub').removeAttr('checked');
			}*/
			//Dropdown field
			MCP.activeSelectedOption('#group_id', groupId);
			MCP.activeSelectedOption('#status', status);
			MCP.activeSelectedOption('#image_status', imageStatus);
			MCP.activeSelectedOption('#parent_id', parentId);
			MCP.activeSelectedOption('#target', target);
			MCP.activeSelectedOption('#type', type);
			MCP.activeSelectedOption('#dropdown_columns', dropdownColumns);
			MCP.activeSelectedOption('#hide_sub_header', hideSubHeader);
			MCP.activeSelectedOption('#permission', permission);
			MCP.activeSelectedOption('#text_align', textAlign);
			MCP.activeSelectedOption('#hide_phone', hidePhone);
			MCP.activeSelectedOption('#hide_tablet', hideTablet);
			// Image field
			if(image != ""){
				var baseurl = $("#skin_baseurl").val();
				$('#image').attr('style','margin-left:25px');
				var link='<a class="image-preview" onclick="imagePreview(\'image_image\'); return false;" href="' +
				baseurl + 'media/' + image + '"><img width="22" height="22" class="small-image-preview v-middle" alt="' +
				image + '" title="' + image + '" id="image_image" src="' + baseurl + 'media/' + image + '"></a>';
				var deleteCheckbox = '<span class="delete-image">' + 
				'<input type="checkbox" id="image_delete" class="checkbox" value="1" name="image[delete]">' + 
				'<label for="image_delete"> Delete Image</label>' +
				'<input type="hidden" value="' + image + '" name="image[value]"></span>';
				// Remove exists item before append
				$("#menupro_form .delete-image").remove();
				$("#menupro_form .image-preview").remove();
				$("#menupro_form #image").parent().prepend( link );
				$("#menupro_form #image").parent().append( deleteCheckbox );
			}else{
				$('#image').attr('style','margin-left:0px');
				$("#image_image").remove();
				$("#menupro_form .delete-image").remove();
				$("#menupro_form .image-preview").remove();
			}
			/**
			* Show and hide some field of current menu
			*/
			switch ( type ) {
				case "1" :
					MCP.hideFormField( type );
					MCP.activeSelectedOption( '#cms_page', urlValue );
					break;
				case "4" :
					MCP.hideFormField( type );
					MCP.activeSelectedOption( '#category_id', urlValue );
					break;
				case "5" :
					MCP.hideFormField( type );
					MCP.activeSelectedOption( '#product_id', urlValue );
					break;	
				case "6" :
					MCP.hideFormField( type );
					MCP.activeSelectedOption( '#static_block', urlValue );
					break;
				case "8" :
					var separatorLine = $("#separator_line").val();
					MCP.hideFormField( type );
					$("#url_value").val( urlValue );
					$("#title").val( separatorLine );
					$("#title").parent().parent().hide();
					break;
				default :
					//Most used links
					MCP.hideFormField( type );
					$("#menupro_form #custom_urlvalue").val( urlValue );
					$("#menupro_form #url_value").val( urlValue );
					break;
			}
			// Multi select field
			// Delete all selected option first
			$("#menupro_form #storeids option").each(function() {
				$(this).removeAttr("selected");
			});
			var storeArray=storeids.split(",");
			$("#menupro_form #storeids option").each(function() {
				var storeid = $(this).val();
				if(storeArray[0] == 0){
					$(this).attr("selected","selected");
					return false;
				}else{
					if($.inArray(storeid,storeArray) != -1 && $.inArray(0,storeArray) == -1){
						$(this).attr("selected","selected");
					}
				}
			});
			//Checkbox field, category type
			if (type == 4) {
				if (autosub == 1) {
					$('#menupro_form #autosub').val(1);
					$('#menupro_form #autosub').attr('checked', 'checked');
				} else {
					$('#menupro_form #autosub').val(2);
					$('#menupro_form #autosub').removeAttr('checked');
				}
				if (useCategoryTitle == 1) {
					$('#menupro_form #use_category_title').val(1);
					$('#menupro_form #use_category_title').attr('checked', 'checked');
					$('#title').removeAttr('readonly');
				} else {
					$('#menupro_form #use_category_title').val(2);
					$('#menupro_form #use_category_title').removeAttr('checked');
					$("#title").attr('readonly', 'readonly');
				}
			}
		},
		/** Show and hide li tag as filter */
		storeFilter:function( storeids ) {
			/* If select specific store */
			if(storeids != 0){
				$("#sitemap li").each(function(){
					var id = $(this).attr("id");
					/* If it is not a first li, root node */
					if(id != 0){
						var menu_store = $(this).attr("store");
						var menuStoreArr = new Array();
						menuStoreArr = menu_store.split(',');
						if($.inArray(storeids, menuStoreArr) != -1 || $.inArray(0, menuStoreArr) != -1){
							$(this).show();
						}else{
							$(this).hide();
						}
					}	
				});
			}else{
				/* When all store view selected */
				$("#sitemap li").each(function(){
					$(this).show();
				});
			}
		},
		filterByGroupId : function( id ) {
			//Empty option
			$('#parent_id').html('');
			$('#parent_id').append('<option value="">Please select ---</option>');
			if( id != ""){
				$('#parent_id').append('<option level="0" group="" value="0">Root</option>');
			}
			var option;
			for (var i = 0 ; i < parentOptions.length; i++){
				if(parentOptions[i].group == id){
					option ='<option group="' + parentOptions[i].group + '" value="' + parentOptions[i].value + '" level="' + parentOptions[i].level +'">' + 
					parentOptions[i].title + "</option>";
					$('#parent_id').append(option);
				}
			}
		},
		getParentOptions : function() {
			$('#parent_id option').each(function() {
				var optionObj = {};
				optionObj.group = $(this).attr('group');
				optionObj.level = $(this).attr('level');
				optionObj.value = $(this).val();
				optionObj.title = $(this).text();
				parentOptions.push(optionObj);
			});
		},
		getGroupId : function() {
			return $("#group_id option:selected").val();
		},
		getCurentMenuType : function() {
			var currentType = $('#menupro_form #group_id option:selected').attr('menu_type');
			return currentType;
		},
		switchColumnLayout : function() {
			/**
			* Dropdown, Sidebar, Accordion: has 3 level. => Show drop column
			* when current parent_id level = 0 to set column layout => Show
			* drop column when current parent_id level = 1 to set column unnit
			* 
			* Dropline is special: has 4 level => Show drop column when current
			* parent_id level = 1 to set column layout => Show drop column when
			* current parent_id level = 2 to set column unnit
			*/
			//var currentType = MCP.getCurentMenuType();
			var currentLevel = MCP.getCurrentParentLevel();
			
			switch ( currentLevel ) {
				case '0':
					MCP.hideSubHeader();
					MCP.showColumnLayout();
					break;
				case '1':
					MCP.hideSubHeader();
					MCP.showColumnUnit();
					MCP.showSubHeader();
					break;
				default:
					MCP.hideSubHeader();
					MCP.hideColumnLayout();
					break;
			}
		},
		getCurrentParentLevel : function() {
			var currentLevel = $('#menupro_form #parent_id option:selected').attr('level');
			return currentLevel;
		},
		showColumnLayout : function() {
			//Show option 6 if it is hiding
			$('#column_label').text('Sub Column Layout: ');
			/*$('#dropdown_columns option').each(function() {
				if($(this).val() == 6){
					$(this).show();
				}
			});*/
			$('#dropdown_columns').removeAttr('disabled');
			$('#dropdown_columns').parent().parent().show();
		},
		hideColumnLayout : function() {
			$('#dropdown_columns').attr('disabled',true);
			$('#dropdown_columns').parent().parent().hide();
		},
		showColumnUnit : function() {
			//Remove option 6, because column unit have only 5 option exclude full option
			$('#column_label').text('Sub Column Unit: ');
			/*$('#dropdown_columns option').each(function() {
				if($(this).val() == 6){
					$(this).hide();
				}
			});*/
			$('#dropdown_columns').removeAttr('disabled');
			$('#dropdown_columns').parent().parent().show();
		},
		showSubHeader : function() {
			$('#hide_sub_header').removeAttr('disabled');
			$('#hide_sub_header').parent().parent().show();
		},
		hideSubHeader : function() {
			$('#hide_sub_header').attr('disabled',true);
			$('#hide_sub_header').parent().parent().hide();
		},
		collapseAll : function() {
			var liClass;
			var liOpen = "sm_liOpen";
			var liClosed = "sm_liClosed";
			$('#sitemap li').each(function() {
				liClass = $(this).attr('class');
				
				if(liClass.match(liOpen)) {
					$(this).removeClass(liOpen);
					$(this).addClass(liClosed);
				}
			});
		},
		expandCurrentActiveMenu : function () {
			var currentMenuId = $('#current_active_menu').val();
			if (typeof currentMenuId != "undefined" && currentMenuId !="") {
				var parentId = $("#" + currentMenuId).parents().filter('.level0').attr('id');
				var currentMenu = $('#' + currentMenuId);
				var liClass;
				var liOpen = "sm_liOpen";
				var liClosed = "sm_liClosed";
				
				currentMenu.parents('li').removeClass('sm_liClosed');
				currentMenu.parents('li').addClass('sm_liOpen');
				$("html, body").animate({scrollTop: currentMenu.offset().top }, 1000);
			}
			
		},
		expandAll : function() {
			var liClass;
			var liOpen = "sm_liOpen";
			var liClosed = "sm_liClosed";
			$('#sitemap li').each(function() {
				liClass = $(this).attr('class');
				if(liClass.match(liClosed)) {
					$(this).removeClass(liClosed);
					$(this).addClass(liOpen);
				}
			});
		},
		useCategoryTitle : function() {
			var categoryTitle = $('#use_category_title');
			if (categoryTitle.is(':checked')) {
				categoryTitle.val(1);
				$('#title').removeAttr('readonly');
				$('#title').focus();
			} else {
				categoryTitle.val(2);
				if ($('#title').val() != "") {
					$('#title').attr('readonly', 'readonly');
				}
			}
		}
	}
	//Push option object to parentOptions array
	MCP.getParentOptions();
	
	MCP.menuTypeChangeEvent();
	MCP.selecedtUrlValue("category_id");
	MCP.selecedtUrlValue("cms_page");
	MCP.selecedtUrlValue("static_block");
	MCP.autoFillUrlValue("custom_urlvalue");
	MCP.autoFillUrlValue("product_id");
	//MCP.autoFillWhenEdit();
	MCP.hideStoreId();
	MCP.addDepthToLi("sitemap");
	MCP.collapseAll();
	//Expand current active menu
	MCP.expandCurrentActiveMenu();
	//If isset current_active_menu, then expand until this menu
	// MCP.showAndHideGridElement();
	// Filter parent dropdown by group_id after page load
	var id = MCP.getGroupId();
	MCP.filterByGroupId(id);
	MCP.switchColumnLayout();
	MCP.activeDeleteEvent();
	MCP.activeAddChildEvent();
	MCP.formatTextarea();
	
});