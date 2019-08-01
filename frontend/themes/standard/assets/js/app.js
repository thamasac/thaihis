/**
Core script to handle the entire layout and base functions
**/
var App = function () {

	// IE mode
	var isRTL = false;
	var isIE8 = false;
	var isIE9 = false;
	var isIE10 = false;

	var responsiveHandlers = [];

	var handleInit = function() {

		if ($('body').css('direction') === 'rtl') {
			isRTL = true;
		}

		isIE8 = !! navigator.userAgent.match(/MSIE 8.0/);
		isIE9 = !! navigator.userAgent.match(/MSIE 9.0/);
		isIE10 = !! navigator.userAgent.match(/MSIE 10/);
        
		if (isIE10) {
			jQuery('html').addClass('ie10'); // detect IE10 version
		}
	}

	var handleDesktopTabletContents = function () {
		// loops all page elements with "responsive" class and applies classes for tablet mode
		// For metornic  1280px or less set as tablet mode to display the content properly
		if ($(window).width() <= 1280 || $('body').hasClass('page-boxed')) {
			$(".responsive").each(function () {
				var forTablet = $(this).attr('data-tablet');
				var forDesktop = $(this).attr('data-desktop');
				if (forTablet) {
					$(this).removeClass(forDesktop);
					$(this).addClass(forTablet);
				}
			});
		}

		// loops all page elements with "responsive" class and applied classes for desktop mode
		// For metornic  higher 1280px set as desktop mode to display the content properly
		if ($(window).width() > 1280 && $('body').hasClass('page-boxed') === false) {
			$(".responsive").each(function () {
				var forTablet = $(this).attr('data-tablet');
				var forDesktop = $(this).attr('data-desktop');
				if (forTablet) {
					$(this).removeClass(forTablet);
					$(this).addClass(forDesktop);
				}
			});
		}
	}

	var handleSidebarState = function () {
		// remove sidebar toggler if window width smaller than 900(for table and phone mode)
		if ($(window).width() < 768) {
			$('body').removeClass("page-sidebar-closed");
		} else if($(window).width() >= 768 && $(window).width() < 1200){
			var body = $('body');
			
			body.addClass("page-sidebar-closed");
			
			if ((body.hasClass('page-sidebar-closed') === false || body.hasClass('page-sidebar-fixed') === false)) {
				return;
			}

			$('.page-sidebar-menu li.open .sub-menu').hide();
		} else {
			
		}
	}

	var runResponsiveHandlers = function () {
		// reinitialize other subscribed elements
		for (var i in responsiveHandlers) {
			var each = responsiveHandlers[i];
			each.call();
		}
	}

	var handleResponsive = function () {
		//handleTooltips();
		handleSidebarState();
		handleDesktopTabletContents();
		handleFixedSidebar();
		handleFixedRightSidebar();
		runResponsiveHandlers();
	}

	var handleResponsiveOnInit = function () {
		handleSidebarState();
		handleDesktopTabletContents();
	}

	var handleResponsiveOnResize = function () {
		var resize;
		if (isIE8) {
			var currheight;
			$(window).resize(function() {
				if(currheight == document.documentElement.clientHeight) {
					return; //quite event since only body resized not window.
				}                
				if (resize) {
					clearTimeout(resize);
				}   
				resize = setTimeout(function() {
					handleResponsive();    
				}, 50); // wait 50ms until window resize finishes.                
				currheight = document.documentElement.clientHeight; // store last body client height
			});
		} else {
			$(window).resize(function() {
				if (resize) {
					clearTimeout(resize);
				}   
				resize = setTimeout(function() {
					
					handleResponsive();    
				}, 50); // wait 50ms until window resize finishes.
			});
		}   
	}

	//* BEGIN:CORE HANDLERS *//
	// this function handles responsive layout on screen size resize or mobile device rotate.

	var handleSidebarMenu = function () {
		jQuery('.page-sidebar').on('click', 'li > a', function (e) {
			var parent = $(this).parent().parent();
		
			if(parent.hasClass('sub-menu') == false && $('body').hasClass('page-sidebar-nofixed') == true && $('body').hasClass('page-sidebar-closed') == true){
				return;
			}
		
			if ($(this).next().hasClass('sub-menu') == false) {
				if ($('.btn-navbar').hasClass('collapsed') == false) {
					$('.btn-navbar').click();
				}
				return;
			}
			//Reset show submenu
			parent.children('li.open').children('a').children('.arrow').removeClass('open');
			parent.children('li.open').children('.sub-menu').slideUp(200);
			parent.children('li.open').removeClass('open');
			
			var sub = jQuery(this).next();
			if (sub.is(":visible")) {
				jQuery('.arrow', jQuery(this)).removeClass("open");
				jQuery(this).parent().removeClass("open");
				sub.slideUp(200, function () {
					
				});
			} else {
				jQuery('.arrow', jQuery(this)).addClass("open");
				jQuery(this).parent().addClass("open");
				sub.slideDown(200, function () {
					
				});
			}
			
			e.preventDefault();
		});

	}

	var _calculateFixedSidebarViewportHeight = function () {
		var sidebarHeight = $(window).height() - 98; //- $('.header').height()
		if ($('body').hasClass("page-footer-fixed")) {
			sidebarHeight = sidebarHeight- $('.footer').height();
		}

		return sidebarHeight; 
	}
	
	var _calculateFixedRightsSidebarViewportHeight = function () {
		var sidebarHeight = $(window).height() - 51; //- $('.header').height()
		if ($('body').hasClass("page-footer-fixed")) {
			sidebarHeight = sidebarHeight- $('.footer').height();
		}
		return sidebarHeight; 
	}

	var handleFixedSidebar = function () {
		var menu = $('.page-sidebar-menu');

		if (menu.parent('.slimScrollDiv').length === 1) { // destroy existing instance before updating the height
			
			menu.slimScroll({
				destroy: true
			});
			menu.removeAttr('style');
			$('.page-sidebar').removeAttr('style');
			
		}

		if ($('.page-sidebar-fixed').length === 0) {
			return;
		}

		if ($(window).width() >= 768) {
			var sidebarHeight = _calculateFixedSidebarViewportHeight();

			menu.slimScroll({
				size: '7px',
				color: '#a1b2bd',
				opacity: .3,
				position: isRTL ? 'left' : ($('.page-sidebar-on-right').length === 1 ? 'left' : 'right'),
				height: sidebarHeight,
				allowPageScroll: false,
				disableFadeOut: false
			});
		}
	}
	
	var handleFixedRightSidebar = function () {
		var rside = $('#right-side-scroll');

		if ($('.page-sidebar-fixed').length === 0) {
			return;
		}

		if ($(window).width() >= 992) {
			var sidebarHeight = _calculateFixedRightsSidebarViewportHeight();

			rside.slimScroll({
				size: '7px',
				color: '#a1b2bd',
				opacity: .8,
				position: isRTL ? 'left' : ($('.page-sidebar-on-right').length === 1 ? 'left' : 'right'),
				height: sidebarHeight,
				allowPageScroll: false,
				disableFadeOut: false
			});
		} else {
			if (rside.parent('.slimScrollDiv').length === 1) {
				rside.slimScroll({
					destroy: true
				});
				rside.removeAttr('style');
				$('.right-sidebar').removeAttr('style');
			}
		}
	}

	var handleFixedSidebarHoverable = function () {
		if ($('body').hasClass('page-sidebar-fixed') === false) {
			return;
		}
		
		$('.page-sidebar').mouseenter(function () {
			var body = $('body');
			
			if ((body.hasClass('page-sidebar-closed') === false || body.hasClass('page-sidebar-fixed') === false)) {
				return;
			}

			$('.page-sidebar-menu li.open > .sub-menu').show();
		});

		$('.page-sidebar').mouseleave(function () {
			var body = $('body');

			if ((body.hasClass('page-sidebar-closed') === false || body.hasClass('page-sidebar-fixed') === false)) {
				return;
			}

			$('.page-sidebar-menu li.open>.sub-menu').hide();
			
		});
	}

	var handleSidebarToggler = function () {
		// handle sidebar show/hide
		$('.page-sidebar').on('click', '.sidebar-toggler', function (e) {            
			var body = $('body');
			var sidebar = $('.page-sidebar');

			//$(".sidebar-search", sidebar).removeClass("open");

			if (body.hasClass("page-sidebar-closed")) {
				$.cookie('sidebar_toggler', 1, { path: '/' });
				body.removeClass("page-sidebar-closed");
				if (body.hasClass('page-sidebar-fixed')) {
					sidebar.css('width', '');
				}
			} else {
				$.cookie('sidebar_toggler', 0, { path: '/' });
				body.addClass("page-sidebar-closed");
			}
			runResponsiveHandlers();
		});

	}

	var handleGoTop = function () {
		/* set variables locally for increased performance */
		jQuery('.footer').on('click', '.go-top', function (e) {
			App.scrollTo();
			e.preventDefault();
		});
	}

	var handleScrollers = function () {
		$('.scroller').each(function () {
			$(this).slimScroll({
				size: '7px',
				color: '#a1b2bd',
				position: isRTL ? 'left' : 'right',
				height: $(this).attr("data-height"),
				alwaysVisible: ($(this).attr("data-always-visible") == "1" ? true : false),
				railVisible: ($(this).attr("data-rail-visible") == "1" ? true : false),
				disableFadeOut: true
			});
		});
	}

	var handleTooltips = function () {
		if (App.isTouchDevice()) { // if touch device, some tooltips can be skipped in order to not conflict with click events
			$('body').tooltip('destroy');
		}
	}

	var handleFixInputPlaceholderForIE = function () {
		//fix html5 placeholder attribute for ie7 & ie8
		if (isIE8 || isIE9) { // ie7&ie8
			// this is html5 placeholder fix for inputs, inputs with placeholder-no-fix class will be skipped(e.g: we need this for password fields)
			jQuery('input[placeholder]:not(.placeholder-no-fix), textarea[placeholder]:not(.placeholder-no-fix)').each(function () {

				var input = jQuery(this);

				if(input.val()=='' && input.attr("placeholder") != '') {
					input.addClass("placeholder").val(input.attr('placeholder'));
				}

				input.focus(function () {
					if (input.val() == input.attr('placeholder')) {
						input.val('');
					}
				});

				input.blur(function () {                         
					if (input.val() == '' || input.val() == input.attr('placeholder')) {
						input.val(input.attr('placeholder'));
					}
				});
			});
		}
	}

	var renderToggleButton = function () {
	    $('#main-nav-app .navbar-header').append('<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#slide-collapse">'+
							    '<span class="sr-only">Toggle navigation</span>'+
							    '<span class="icon-bar"></span>'+
							    '<span class="icon-bar"></span>'+
							    '<span class="icon-bar"></span>'+
						    '</button>');
	}

	//* END:CORE HANDLERS *//

	return {

		//main function to initiate template pages
		init: function () {

			//IMPORTANT!!!: Do not modify the core handlers call order.

			//core handlers
			handleInit();
			handleResponsiveOnResize(); // set and handle responsive       
			handleScrollers(); // handles slim scrolling contents 
			handleResponsiveOnInit(); // handler responsive elements on page load

			//layout handlers
			handleFixedSidebar(); // handles fixed sidebar menu
			handleFixedRightSidebar();
			handleFixedSidebarHoverable(); // handles fixed sidebar on hover effect 
			handleSidebarMenu(); // handles main menu
			handleSidebarToggler(); // handles sidebar hide/show            
			handleFixInputPlaceholderForIE(); // fixes/enables html5 placeholder attribute for IE9, IE8
			handleGoTop(); //handles scroll to top functionality in the footer
			renderToggleButton();
			
			//ui component handlers
			//handleTooltips(); // handle bootstrap tooltips

		},

		addResponsiveHandler: function (func) {
			responsiveHandlers.push(func);
		},

		// useful function to make equal height for contacts stand side by side
		setEqualHeight: function (els) {
			var tallestEl = 0;
			els = jQuery(els);
			els.each(function () {
				var currentHeight = $(this).height();
				if (currentHeight > tallestEl) {
					tallestColumn = currentHeight;
				}
			});
			els.height(tallestEl);
		},

		// wrapper function to scroll to an element
		scrollTo: function (el, offeset) {
			pos = el ? el.offset().top : 0;
			jQuery('html,body').animate({
				scrollTop: pos + (offeset ? offeset : 0)
			}, 'slow');
		},

		scrollTop: function () {
			App.scrollTo();
		},

		// check for device touch support
		isTouchDevice: function () {
			try {
				document.createEvent("TouchEvent");
				return true;
			} catch (e) {
				return false;
			}
		},

		isIE8: function () {
			return isIE8;
		},

		isRTL: function () {
			return isRTL;
		}

	};

}();