var mstSitemap = jQuery.noConflict();

mstSitemap(document).ready(function($) {
	SM = {
		activeSitemap : function() {
			$('#sitemap li').prepend('<div class="dropzone"></div>');

			$('#sitemap dl, #sitemap .dropzone').droppable({
				accept: '#sitemap li',
				tolerance: 'pointer',
				drop: function(e, ui) {
					var li = $(this).parent();
					//Max depth level = 5
					var depth = $('#' + li.attr('id'),"#sitemap").parents("ul").size();
					if(depth >= 5){
						$(this).filter('dl').css({ backgroundColor: '' });
						$(this).filter('.dropzone').css({ borderColor: '' });
						alert("Max depth reached. Allow 5 level only!");
						return false;
					}
					var child = !$(this).hasClass('dropzone');
					if (child && li.children('ul').length == 0) {
						li.append('<ul/>');
					}
					if (child) {
						li.addClass('sm_liOpen').removeClass('sm_liClosed').children('ul').append(ui.draggable);
					}
					else {
						li.before(ui.draggable);
					}
					$('#sitemap li.sm_liOpen').not(':has(li:not(.ui-draggable-dragging))').removeClass('sm_liOpen');
					li.find('dl,.dropzone').css({ backgroundColor: '', borderColor: '' });
					sitemapHistory.commit();
				},
				over: function() {
					$(this).filter('dl').css({ backgroundColor: '#ccc' });
					$(this).filter('.dropzone').css({ borderColor: '#000' });
				},
				out: function() {
					$(this).filter('dl').css({ backgroundColor: '' });
					$(this).filter('.dropzone').css({ borderColor: '' });
				}
			});
			$('#sitemap li').draggable({
				handle: ' > dl',
				cancel: "dl.no_drag",
				opacity: .8,
				addClasses: false,
				helper: 'clone',
				zIndex: 100,
				start: function(e, ui) {
					sitemapHistory.saveState(this);
				}
			});
		},
		undo : function() {
			$(document).bind('keypress', function(e) {
				if (e.ctrlKey && (e.which == 122 || e.which == 26))
					sitemapHistory.restoreState();
			});
		},
		expand : function() {
			$('.sm_expander').live('click', function() {
				$(this).parent().parent().toggleClass('sm_liOpen').toggleClass('sm_liClosed');
				return false;
			});
		}
	}
	
	sitemapHistory = {
		stack: new Array(),
		temp: null,
		//takes an element and saves it's position in the sitemap.
		//note: doesn't commit the save until commit() is called!
		//this is because we might decide to cancel the move
		saveState: function(item) {
			sitemapHistory.temp = { item: $(item), itemParent: $(item).parent(), itemAfter: $(item).prev() };
		},
		commit: function() {
			if (sitemapHistory.temp != null) sitemapHistory.stack.push(sitemapHistory.temp);
		},
		//restores the state of the last moved item.
		restoreState: function() {
			var h = sitemapHistory.stack.pop();
			if (h == null) return;
			if (h.itemAfter.length > 0) {
				h.itemAfter.after(h.item);
			}
			else {
				h.itemParent.prepend(h.item);
			}
			//checks the classes on the lists
			$('#sitemap li.sm_liOpen').not(':has(li)').removeClass('sm_liOpen');
			$('#sitemap li:has(ul li):not(.sm_liClosed)').addClass('sm_liOpen');
		}
	}
	
	SM.activeSitemap();
	SM.undo();
	SM.expand();
});

