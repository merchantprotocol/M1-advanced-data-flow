var mst = jQuery;
mst(document).ready(function($) {
    //$('ul.menu-creator-pro').clone(false).removeAttr("class").addClass("menu-creator-pro-responsive").insertAfter($('ul.menu-creator-pro'));
    $('.menu-creator-pro-responsive .fa-angle-right').removeClass("fa-angle-right").addClass("fa-angle-down");
    var current_height = $('.menu-creator-pro-responsive').height();
    $('.menu-creator-pro-responsive').height(0);
    $('.menu-creator-pro-responsive > .switcher').click(function(){
        current_height = $('.menu-creator-pro-responsive').height();
        var max_height = $('.menu-creator-pro-responsive').css("height","auto").height();
        $('.menu-creator-pro-responsive').height(current_height);
        if($(this).parent().hasClass("active")){
            $(this).parent().removeClass("active").animate({height:"0px"});
        }else{
            $(this).parent().addClass("active").animate({height:max_height});
        }
        //$('.menu-creator-pro-responsive > li:not(.switcher').slideToggle(); 
        return false;
    });
    $('.menu-creator-pro-responsive > li:not(.parent) .fa-angle-down').remove();
    $('.menu-creator-pro-responsive > li .fa-angle-down').addClass("icon_toggle")
    $('.menu-creator-pro-responsive > li .icon_toggle').click(function(){
        var max_height = $('.menu-creator-pro-responsive').css("height","auto");
        if($(this).hasClass("fa-angle-down")){
            $(this).removeClass('fa-angle-down').addClass('fa-angle-up').next().slideDown(500);
        }else{
            $(this).removeClass('fa-angle-up').addClass('fa-angle-down').next().slideUp(500);
        };
    });
    $('.menu-creator-pro .fa-angle-down').click(function(){
       if(!$(this).parent().hasClass("item_cr")){
           $(this).parent().nextAll().children(".grid-container3").css('opacity',"0"); 
           $(this).next().css({'transform':'scale(1, 1)',"opacity":'1',"display":'block'});
           $(this).parent().addClass("item_cr");
       }else{
           $(this).parent().removeClass("item_cr");
           $(this).next().css({'transform':'scale(1, 0)',"opacity":'0',"display":'none'});
       }
    });
    
	/* Reponsive side menu */
	$(window).touchwipe({
		wipeLeft: function() {
		  // Close
		  $.sidr('close', 'sidr-main');
		},
		wipeRight: function() {
		  // Open
		  $.sidr('open', 'sidr-main');
		},
		preventDefaultEvents: false
	  });
	//------------------Active State--------------------
	MCP = {
		activeClass : 'current',
		addActiveClass : function(selector) {
			/* Add active class to parent when a child active */
			var current_url = window.location.href;
			var link = null;
			var li_class = null;
			$(selector + ' li a').each(function() {
				link = $(this).attr('href');
				if(link == current_url){
					$(this).addClass(MCP.activeClass);
					$(this).parents('li').addClass(MCP.activeClass);
				}
			});
			/****NOTICE: If you just want active class visiable in li level0, and remove in all another level then uncomment below code */
			/* $(selector + ' li').each(function() {
				try{
					li_class = $(this).attr('class');
					if (li_class != "" && li_class != undefined) {
						if(li_class.indexOf('level0') == -1) {
							$(this).removeClass(MCP.activeClass);
						}
					}

				}catch(error){
					//Do nothing in here
				}
			});*/
		}
	}
	MCP.addActiveClass('.mst .mcp-version-4x');
});