/*
Author       : Dreamstechnologies
Template Name: Doccure - Bootstrap Admin Template
Version      : 1.3
*/

(function ($) {
	"use strict";

	// Variables declarations

	var $wrapper = $('.main-wrapper');
	var $pageWrapper = $('.page-wrapper');
	var $slimScrolls = $('.slimscroll');

	// Sidebar

	var SidebarMenu = {
		init: function () {
			this.bindUI();
			this.keepActiveOpen();
		},

		bindUI: function () {
			// toggle submenu on click
			$('#sidebar-menu').on('click', 'li.submenu > a', function (e) {
				e.preventDefault();
				var $this = $(this);
				var $submenu = $this.next('ul');

				if ($submenu.is(':visible')) {
					// close current
					$this.removeClass('subdrop');
					$submenu.slideUp(300);
				} else {
					// close all others first, excluding parent submenus
					$('#sidebar-menu li.submenu a.subdrop').not($this.parents('.submenu').children('a')).removeClass('subdrop');
					$('#sidebar-menu li.submenu ul:visible').not($this.parents('ul')).slideUp(300);

					// open current
					$this.addClass('subdrop');
					$submenu.slideDown(300);
				}
			});
		},

		keepActiveOpen: function () {
			// auto-open if active link is inside
			var $active = $('#sidebar-menu li.submenu ul li a.active');
			if ($active.length) {
				$active.parents('ul').show().prev('a').addClass('subdrop');
			}
		}
	};

	// Initialize on DOM ready
	$(function () {
		SidebarMenu.init();
	});




	// Mobile menu sidebar overlay

	$('body').append('<div class="sidebar-overlay"></div>');
	$(document).on('click', '#mobile_btn', function () {
		$wrapper.toggleClass('slide-nav');
		$('.sidebar-overlay').toggleClass('opened');
		$('html').toggleClass('menu-opened');
		return false;
	});

	// Sidebar overlay

	$(".sidebar-overlay").on("click", function () {
		$wrapper.removeClass('slide-nav');
		$(".sidebar-overlay").removeClass("opened");
		$('html').removeClass('menu-opened');
	});

	// Page Content Height

	if ($('.page-wrapper').length > 0) {
		var height = $(window).height();
		$(".page-wrapper").css("min-height", height);
	}

	// Page Content Height Resize

	$(window).resize(function () {
		if ($('.page-wrapper').length > 0) {
			var height = $(window).height();
			$(".page-wrapper").css("min-height", height);
		}
	});

	// Select 2

	if ($('.select').length > 0) {
		$('.select').select2({
			minimumResultsForSearch: -1,
			width: '100%'
		});
	}

	// Datetimepicker

	if ($('.datetimepicker').length > 0) {
		$('.datetimepicker').datetimepicker({
			format: 'DD/MM/YYYY',
			icons: {
				up: "fa fa-angle-up",
				down: "fa fa-angle-down",
				next: 'fa fa-angle-right',
				previous: 'fa fa-angle-left'
			}
		});
		$('.datetimepicker').on('dp.show', function () {
			$(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
		}).on('dp.hide', function () {
			$(this).closest('.temp').addClass('table-responsive').removeClass('temp')
		});
	}

	// Tooltip

	if ($('[data-bs-toggle="tooltip"]').length > 0) {
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl)
		})
	}

	// Datatable

	if ($('.datatable').length > 0) {
		$('.datatable').DataTable({
			"bFilter": true,
			"language": {
				paginate: {
					next: 'Next',
					previous: 'Previous'
				},
			}
		});
	}
	// Email Inbox

	if ($('.clickable-row').length > 0) {
		$(document).on('click', '.clickable-row', function () {
			window.location = $(this).data("href");
		});
	}

	// Check all email

	$(document).on('click', '#check_all', function () {
		$('.checkmail').click();
		return false;
	});
	if ($('.checkmail').length > 0) {
		$('.checkmail').each(function () {
			$(this).on('click', function () {
				if ($(this).closest('tr').hasClass('checked')) {
					$(this).closest('tr').removeClass('checked');
				} else {
					$(this).closest('tr').addClass('checked');
				}
			});
		});
	}

	// Mail important

	$(document).on('click', '.mail-important', function () {
		$(this).find('i.fa').toggleClass('fa-star').toggleClass('fa-star-o');
	});

	// Summernote

	if ($('.summernote').length > 0) {
		$('.summernote').summernote({
			height: 200,                 // set editor height
			minHeight: null,             // set minimum height of editor
			maxHeight: null,             // set maximum height of editor
			focus: false                 // set focus to editable area after initializing summernote
		});
	}

	// Product thumb images

	if ($('.proimage-thumb li a').length > 0) {
		var full_image = $(this).attr("href");
		$(".proimage-thumb li a").click(function () {
			full_image = $(this).attr("href");
			$(".pro-image img").attr("src", full_image);
			$(".pro-image img").parent().attr("href", full_image);
			return false;
		});
	}

	// Lightgallery

	if ($('#pro_popup').length > 0) {
		$('#pro_popup').lightGallery({
			thumbnail: true,
			selector: 'a'
		});
	}

	// Sidebar Slimscroll

	// if($slimScrolls.length > 0) {
	// 	$slimScrolls.slimScroll({
	// 		height: 'auto',
	// 		width: '100%',
	// 		position: 'right',
	// 		size: '7px',
	// 		color: '#ccc',
	// 		allowPageScroll: false,
	// 		wheelStep: 10,
	// 		touchScrollStep: 100
	// 	});
	// 	var wHeight = $(window).height() - 60;
	// 	$slimScrolls.height(wHeight);
	// 	$('.sidebar .slimScrollDiv').height(wHeight);
	// 	$(window).resize(function() {
	// 		var rHeight = $(window).height() - 60;
	// 		$slimScrolls.height(rHeight);
	// 		$('.sidebar .slimScrollDiv').height(rHeight);
	// 	});
	// }

	// Small Sidebar

	$(document).on('click', '#toggle_btn', function () {
		if ($('body').hasClass('mini-sidebar')) {
			$('body').removeClass('mini-sidebar');
			$('.subdrop + ul').slideDown();
		} else {
			$('body').addClass('mini-sidebar');
			$('.subdrop + ul').slideUp();
		}
		setTimeout(function () {
			// mA.redraw();
			// mL.redraw();
		}, 300);
		return false;
	});
	$(document).on('mouseover', function (e) {
		e.stopPropagation();
		if ($('body').hasClass('mini-sidebar') && $('#toggle_btn').is(':visible')) {
			var targ = $(e.target).closest('.sidebar').length;
			if (targ) {
				$('body').addClass('expand-menu');
				$('.subdrop + ul').slideDown();
			} else {
				$('body').removeClass('expand-menu');
				$('.subdrop + ul').slideUp();
			}
			return false;
		}
	});


})(jQuery);
