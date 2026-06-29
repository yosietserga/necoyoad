/*
 * Title: jQuery Etalage plugin
 * Author: Berend de Jong, Frique
 * Author URI: https://www.frique.me/
 * Version: 1.01 (20110325.1)
 */

(function($){
	jQuery.fn.etalage = function(options){

		// OPTION DEFAULTS
		var o = $.extend({
			thumb_image_width: 300,				// The large thumbnail width (excluding borders / padding) (value in pixels)
			thumb_image_height: 400,			// The large thumbnail height (excluding borders / padding) (value in pixels)
			source_image_width: 900,			// The source/zoomed image width (not the frame around it) (value in pixels)
			source_image_height: 1200,			// The source/zoomed image height (not the frame around it) (value in pixels)
			zoom_area_width: 600,				// Width of the zoomed image frame (including borders, padding) (value in pixels)
			zoom_area_height: 'justify',		// Height of the zoomed image frame (including borders, padding) (value in pixels / 'justify' = height of large thumb + small thumbs)
			zoom_easing: true,					// Ease the animation while moving the zoomed image (true/false)
			small_thumbs: 3,					// How many small thumbnails will be visible underneath the large thumbnail (minimum of 3) (number)
			smallthumb_inactive_opacity: 0.4,	// Opacity of the inactive small thumbnails (number between or equal to 0-1)
			smallthumb_hide_single: true,		// Don't show the small thumb when there is only 1 image (true/false)
			magnifier_opacity: 0.5,				// Opacity of the magnifier area (does not apply if magnifier_invert is true) (number between or equal to 0-1)
			magnifier_invert: true,				// Make the large thumbnail clear through the magnifier, opaque around it (opacity is the value of magnifier_opacity) (true/false)
			show_icon: true,					// Shows the icon image in the middle of the magnifier (only if magnifier_invert is false) and left-bottom of large thumb (true/false)
			hide_cursor: false,					// Hides the cursor when hovering the large thumbnail (only works in some browsers) (true/false)
			show_hint: true,					// Show "hint" until image is zoomed for the first time (true/false)
			speed: 600,							// All animation speeds are based on this setting (value in ms)
			autoplay: true,						// Makes the thumbs switch automatically when not interacting (at each autoplay_interval below) (true/false)
			autoplay_interval: 6000				// Time showing each image if autoplay is on (value in ms)
		},options);

		$(this).each(function(){
			var $container = $(this);

			// Verify if this is a UL, has atleast 1 LI and 1 source image
			if($container.is('ul') && $container.find('> li').length > 0 && $container.find('img.etalage_source_image').length > 0){

				var i, j, src, thumb_id, magnifier_opacity, autotimer,
					faster = Math.floor(o.speed*0.7),
					zoom_follow_speed = Math.round(o.speed/100),
					st_moving = false,
					ie6_iframe_fix = false,
					preview = true,
					zoom_move_timer = 0,
					cur_zoomx = 0,
					cur_zoomy = 0,
					new_zoomx = 0,
					new_zoomy = 0;

				// IE specifics
				if(navigator.userAgent.indexOf('MSIE') > -1){
					preview = false;
					if(/6.0/.test(navigator.userAgent)){
						ie6_iframe_fix = true;
					}
				}

// NEW ELEMENTS & CACHE
				$container.addClass('etalage').show();
				var $thumbs = $container.find('li').addClass('etalage_thumb');
				$thumbs.first().show().addClass('etalage_thumb_active');

				var images = $thumbs.length;
				if(images < 2){
					o.autoplay = false;
				}

				// Add/generate large thumbs (& check for existing thumb/source images)
				$thumbs.each(function(i){
					i++;
					var $t = $(this);
					var $t_thumb = $t.find('.etalage_thumb_image').removeAttr('alt').show();
					var $t_source = $t.find('.etalage_source_image');
					$t.data('id', i).addClass('thumb_'+i);
					// No thumb, but source
					if($t_thumb.length < 1 && $t_source.length > 0){
						$t.prepend('<img class="etalage_thumb_image" src="'+$t_source.attr('src')+'" />');
					}
					// No thumb and no source
					else if($t_thumb.length < 1 && $t_source.length < 0){
						$t.remove();
					}
				});
				var $thumb_images = $thumbs.find('.etalage_thumb_image').css({ width:o.thumb_image_width, height:o.thumb_image_height }).show();
				$thumb_images.each(function(){
					$(this).data('src', this.src);
				});

				// Add magnifier
				var $magnifier = $('<li class="etalage_magnifier"><div><img /></div></li>').appendTo($container);
				var $magnifier_img_area = $magnifier.find('div');
				var $magnifier_img = $magnifier_img_area.find('img');

				// Add zoom icon
				var $icon = $('<li class="etalage_icon">&nbsp;</li>').appendTo($container);
				if(o.show_icon){
					$icon.show();
				}

				// Add hint
				var $hint = $('<li class="etalage_hint">&nbsp;</li>').appendTo($container);
				if(o.show_hint){
					$hint.show();
				}

				// Add zoom area
				var $zoom = $('<li class="etalage_zoom_area"><div><img class="etalage_zoom_img" /></div></li>').appendTo($container);
				var $zoom_img_area = $zoom.find('div');
				if(preview){
					var $zoom_preview = $('<img class="etalage_zoom_preview" />').css({ width:o.source_image_width, height:o.source_image_height, opacity:0.3 }).prependTo($zoom_img_area).show();
				}
				var $zoom_img = $zoom_img_area.find('.etalage_zoom_img').css({ width:o.source_image_width, height:o.source_image_height });

				// Add/generate smallthumbs
				if(images > 1 || !o.smallthumb_hide_single){
					var $smallthumbs = $('<li class="etalage_small_thumbs"><ul></ul></li>').appendTo($container);
					var $smallthumbs_ul = $smallthumbs.find('ul');
					$thumb_images.each(function(){
						var $t = $(this);
						src = $t.data('src');
						thumb_id = $t.parents('.etalage_thumb').data('id');
						$('<li><img class="etalage_small_thumb" src="'+src+'" /></li>').data('thumb_id', thumb_id).appendTo($smallthumbs_ul);
					});
					var $smallthumb = $smallthumbs.find('li').animate({ opacity:o.smallthumb_inactive_opacity }, 0);
					if(o.small_thumbs < 3){
						o.small_thumbs = 3;
					}
					// If more smallthumbs than visible
					if(images > o.small_thumbs){
						// Add extra thumb on each side
						src = $thumb_images.eq(images-1).data('src');
						thumb_id = $thumbs.eq(images-1).data('id');
						$('<li class="etalage_smallthumb_first"><img class="etalage_small_thumb" src="'+src+'" /></li>')
							.data('src', src)
							.data('thumb_id', thumb_id)
							.prependTo($smallthumbs_ul)
							.animate({ opacity:o.smallthumb_inactive_opacity }, 0);
						src = $thumb_images.eq(0).data('src');
						thumb_id = $thumbs.eq(0).data('id');
						$('<li><img class="etalage_small_thumb" src="'+src+'" /></li>')
							.data('src', src)
							.data('thumb_id', thumb_id)
							.appendTo($smallthumbs_ul)
							.animate({ opacity:o.smallthumb_inactive_opacity }, 0);
						$smallthumb = $smallthumbs.find('li');

						// Prepare for moving them left/right
						$smallthumb.eq(o.small_thumbs-1).addClass('etalage_smallthumb_last');
						// Set first active smallthumb
						$smallthumb.eq(1).addClass('etalage_smallthumb_active').animate({ opacity:1 }, 0);
					}
					// Smallthumbs are within boundries
					else{
						$smallthumb.eq(0).addClass('etalage_smallthumb_active').animate({ opacity:1 }, 0);
					}
					// Apply id
					i=1;
					$smallthumb.each(function(){
						$(this).data('id', i);
						i++;
					});
					var $smallthumb_images = $smallthumb.find('img');
					var smallthumbs = $smallthumb.length;
				}

// PREPARE
				// Magnifier invert option
				if(o.magnifier_invert){
					magnifier_opacity = 1;
				}else{
					magnifier_opacity = o.magnifier_opacity;
				}

				// Determine (generated) dimensions
				var thumb_border = parseInt($thumbs.css('borderLeftWidth'), 10) + parseInt($thumbs.css('borderRightWidth'), 10)
						+ parseInt($thumb_images.css('borderLeftWidth'), 10) + parseInt($thumb_images.css('borderRightWidth'), 10);
				var thumb_margin = parseInt($thumbs.css('marginLeft'), 10) + parseInt($thumbs.css('marginRight'), 10);
				var thumb_padding = parseInt($thumbs.css('paddingLeft'), 10) + parseInt($thumbs.css('paddingRight'), 10)
						+ parseInt($thumb_images.css('marginLeft'), 10) + parseInt($thumb_images.css('marginRight'), 10)
						+ parseInt($thumb_images.css('paddingLeft'), 10) + parseInt($thumb_images.css('paddingRight'), 10);
				var thumb_outerwidth = o.thumb_image_width+thumb_border+thumb_margin+thumb_padding,
					thumb_outerheight = o.thumb_image_height+thumb_border+thumb_margin+thumb_padding;

				var smallthumb_border = 0,
					smallthumb_margin = 0,
					smallthumb_padding = 0,
					smallthumb_width = 0,
					smallthumb_height = 0,
					smallthumb_outerwidth = 0,
					smallthumb_outerheight = 0;
				if(images > 1 || !o.smallthumb_hide_single){
					smallthumb_border = parseInt($smallthumb.css('borderLeftWidth'), 10) + parseInt($smallthumb.css('borderRightWidth'), 10)
						+ parseInt($smallthumb_images.css('borderLeftWidth'), 10) + parseInt($smallthumb_images.css('borderRightWidth'), 10);
					smallthumb_margin = parseInt($smallthumb.css('marginTop'), 10);
					smallthumb_padding = parseInt($smallthumb.css('paddingLeft'), 10) + parseInt($smallthumb.css('paddingRight'), 10)
						+ parseInt($smallthumb_images.css('marginLeft'), 10) + parseInt($smallthumb_images.css('marginRight'), 10)
						+ parseInt($smallthumb_images.css('paddingLeft'), 10) + parseInt($smallthumb_images.css('paddingRight'), 10);
					smallthumb_width = Math.round((thumb_outerwidth-((o.small_thumbs-1)*smallthumb_margin))/o.small_thumbs) - (smallthumb_border+smallthumb_padding);
					smallthumb_height = Math.round((o.thumb_image_height * smallthumb_width) / o.thumb_image_width);
					smallthumb_outerwidth = smallthumb_width+smallthumb_border+smallthumb_padding;
					smallthumb_outerheight = smallthumb_height+smallthumb_border+smallthumb_padding;
				}

				var zoom_border = parseInt($zoom.css('borderTopWidth'), 10);
				var zoom_margin = parseInt($zoom.css('marginTop'), 10);
				var zoom_padding = parseInt($zoom.css('paddingTop'), 10);
				// If source image width is smaller than zoom area
				if((o.zoom_area_width - (zoom_border*2) - (zoom_padding*2)) > o.source_image_width){
					o.zoom_area_width = o.source_image_width;
				}else{
					o.zoom_area_width = o.zoom_area_width - (zoom_border*2) - (zoom_padding*2);
				}
				if(o.zoom_area_height == 'justify'){
					o.zoom_area_height = (thumb_outerheight+smallthumb_margin+smallthumb_outerheight) - (zoom_border*2) - (zoom_padding*2);
				}else{
					o.zoom_area_height = o.zoom_area_height - (zoom_border*2) - (zoom_padding*2);
				}
				// If source image height is smaller than zoom area
				if(o.zoom_area_height > o.source_image_height){
					o.zoom_area_height = o.source_image_height;
				}
				// Add iframe underlay to resolve IE6 <select> tag bug
				if(ie6_iframe_fix){
					var $ie6_iframe_fix = $('<iframe marginwidth="0" marginheight="0" scrolling="no" frameborder="0" src="javascript:\'<html></html>\'"></iframe>').css({ position:'absolute', zIndex:1 }).prependTo($zoom);
				}

				var magnifier_border = parseInt($magnifier.css('borderTopWidth'), 10);
				var magnifier_top = parseInt($thumbs.css('borderTopWidth'), 10)
					+ parseInt($thumbs.css('marginTop'), 10)
					+ parseInt($thumbs.css('paddingTop'), 10)
					+ parseInt($thumb_images.css('borderTopWidth'), 10)
					+ parseInt($thumb_images.css('marginTop'), 10)
					- magnifier_border;
				var magnifier_left = $thumb_images.offset().left - $container.offset().left - magnifier_border;
				var magnifier_width = Math.round(o.zoom_area_width*(o.thumb_image_width / o.source_image_width));
				var magnifier_height = Math.round(o.zoom_area_height*(o.thumb_image_height / o.source_image_height));
				var magnifier_bottom = magnifier_top + o.thumb_image_height - magnifier_height;
				var magnifier_right = magnifier_left + o.thumb_image_width - magnifier_width;
				var magnifier_center_x = Math.round( (magnifier_width/2) );
				var magnifier_center_y = Math.round( (magnifier_height/2) );

				if(o.show_hint){
					var hint_top = parseInt($hint.css('marginTop'), 10);
					var hint_right = parseInt($hint.css('marginRight'), 10);
				}

// RESIZE AND REPOSITION ELEMENTS
				$container.css({ width:thumb_outerwidth, height:thumb_outerheight+smallthumb_margin+smallthumb_outerheight });
				if(o.show_icon){
					$icon.css({ top:thumb_outerheight-$icon.outerHeight(true) });
				}
				if(o.show_hint){
					$hint.css({ margin:0, top:-hint_top, right:-hint_right });
				}
				$magnifier_img.css({ margin:0, padding:0, width:o.thumb_image_width, height:o.thumb_image_height });
				$magnifier_img_area.css({ margin:0, padding:0, width:magnifier_width, height:magnifier_height });
				$magnifier.css({ margin:0, padding:0, left:(magnifier_right-magnifier_left)/2, top:(magnifier_bottom-magnifier_top)/2 }).hide();
				$zoom_img_area.css({ width:o.zoom_area_width, height:o.zoom_area_height });
				$zoom.css({ margin:0, left:thumb_outerwidth+zoom_margin }).animate({ opacity:0 }, 0).hide();
				if(images > 1 || !o.smallthumb_hide_single){
					$smallthumbs.css({ top:thumb_outerheight+smallthumb_margin, width:thumb_outerwidth });
					$smallthumbs_ul.css({ width:(smallthumb_outerwidth*smallthumbs) + (smallthumbs*smallthumb_margin) });
					$smallthumb_images.css({ width:smallthumb_width, height:smallthumb_height }).attr('width', smallthumb_width);
					$smallthumb.css({ margin:0, marginRight:smallthumb_margin });

					// Smallthumbs overflow fix for unmatching space (width of visible smallthumbs + their margins is more than the container width):
					var smallthumbs_overflow = ((smallthumb_outerwidth*o.small_thumbs) + ((o.small_thumbs-1)*smallthumb_margin)) - thumb_outerwidth;
					// Overflow*1px decrease
					if(smallthumbs_overflow > 0){
						// Each set
						for(i=1; i<=(smallthumbs-1); i=i+(o.small_thumbs-1)){
							j=1;
							// Each smallthumb
							for(j; j<=smallthumbs_overflow; j++){
								$smallthumb.eq(i+j-1).css({ marginRight:(smallthumb_margin-1) });
							}
						}
					}
					// Overflow*1px increase
					else if(smallthumbs_overflow < 0){
						for(i=1; i<=(smallthumbs-1); i=i+(o.small_thumbs-1)){
							j=1;
							// Each smallthumb
							for(j; j<=(-smallthumbs_overflow); j++){
								$smallthumb.eq(i+j-1).css({ marginRight:(smallthumb_margin+1) });
								$smallthumbs_ul.css({ width:parseInt($smallthumbs_ul.css('width'), 10)+1 });
							}
						}
					}
				}

				if(o.show_icon && !o.magnifier_invert){
					$magnifier.css({ background:$magnifier.css('background-color')+' '+$icon.css('background-image')+' center no-repeat' });
				}
				if(o.hide_cursor){
					$magnifier.add($icon).css({ cursor:'none' });
				}
				if(ie6_iframe_fix){
					$ie6_iframe_fix.css({ width:$zoom_img_area.css('width'), height:$zoom_img_area.css('height') });
				}

// INITIATE FIRST RUN

				var $current_thumb = $thumbs.first().find('.etalage_thumb_image');
				var $current_source = $thumbs.first().find('.etalage_source_image');
				if(o.magnifier_invert){
					$magnifier_img.attr('src', $current_thumb.data('src')).show();
				}
				if(preview){
					$zoom_preview.attr('src', $current_thumb.data('src'));
				}
				$zoom_img.attr('src', $current_source.attr('src'));

// FUNCTIONS

				// Autoplay
				function stopAutoplay(){
					if(autotimer){
						clearInterval(autotimer);
						autotimer = false;
					}
				}
				function startAutoplay(){
					if(autotimer){
						stopAutoplay();
					}
					autotimer = setInterval(function(){
						var $active = $container.find('.etalage_smallthumb_active');
						if($active.next().length > 0){
							$active.next().click();
						}else{
							$smallthumb.first().click();
						}
					}, o.autoplay_interval);
				}

				// Switch active thumb
				function st_click($next_active, moved){
					var $active = $container.find('.etalage_smallthumb_active').removeClass('etalage_smallthumb_active');
					$next_active.addClass('etalage_smallthumb_active');
					// Make sure the magnifier is hidden
					$magnifier.stop().hide();
					// Make sure the zoom area is hidden
					$zoom.stop().hide();
					// Switch small thumb
					if(!moved){
						$active.stop(true,true).animate({ opacity:o.smallthumb_inactive_opacity });
						$next_active.stop(true,true).animate({ opacity:1 });
					}
					// Switch large thumb
					$container.find('.etalage_thumb_active').removeClass('etalage_thumb_active').stop().animate({ opacity:0 }, o.speed, function(){
						$(this).hide();
					});
					var $next_thumb = $thumbs.filter('.thumb_'+$next_active.data('thumb_id')).addClass('etalage_thumb_active').show().stop().animate({ opacity:0 }, 0).animate({ opacity:1}, o.speed);
					$current_thumb = $next_thumb.find('.etalage_thumb_image');
					$current_source = $next_thumb.find('.etalage_source_image');
					// Switch magnifier
					if(o.magnifier_invert){
						$magnifier_img.attr('src', $current_thumb.data('src'));
					}
					// Switch zoom image
					if(preview){
						$zoom_preview.attr('src', $current_thumb.data('src'));
					}
					$zoom_img.attr('src', $current_source.attr('src'));
					// Reset autoplay
					if(o.autoplay){
						stopAutoplay();
						startAutoplay();
					}
				};

				// Smallthumbs sliding
				function st_move(distance, $next_first, $next_last, $next_active){
					$smallthumb.each(function(){
						var animation = {
							left: '-='+distance,
							opacity: o.smallthumb_inactive_opacity
						};
						if($(this).data('id') == $next_active.data('id')){
							animation.opacity = 1;
						}
						$(this).animate(animation, faster, 'swing', function(){
							if(st_moving){
								$next_first.addClass('etalage_smallthumb_first');
								$next_last.addClass('etalage_smallthumb_last');
								$next_active.addClass('etalage_smallthumb_active');
								st_moving = false;
							}
						});
					});

					// Switch thumb
					st_click($next_active, true);
				};

				// Moving the zoomed image
				function zoom_move(){
					var diff_x = new_zoomx - cur_zoomx;
					var diff_y = new_zoomy - cur_zoomy;
					var movethismuchnow_x = -diff_x / zoom_follow_speed;
					var movethismuchnow_y = -diff_y / zoom_follow_speed;
					cur_zoomx = cur_zoomx - movethismuchnow_x;
					cur_zoomy = cur_zoomy - movethismuchnow_y;
					if(diff_x < 1 && diff_x > -1){
						cur_zoomx = new_zoomx;
					}
					if(diff_y < 1 && diff_y > -1){
						cur_zoomy = new_zoomy;
					}
					// Move a bit
					$zoom_img.css({ left:cur_zoomx, top:cur_zoomy });
					if(preview){
						$zoom_preview.css({ left:cur_zoomx, top:cur_zoomy });
					}
					// Repeat
					if(diff_x > 1 || diff_y > 1 || diff_x < 1 || diff_y < 1){
						zoom_move_timer = setTimeout(function(){
							zoom_move();
						}, 25);
					}
				};

// ACTIONS

				// Hide hint
				if(o.show_hint){
					$container.hover(function(){
						$hint.hide();
					});
				}

				// Thumb hover
				$thumbs.add($magnifier).add($icon).mouseenter(function(){
					$magnifier.stop().fadeTo(faster, magnifier_opacity);
					$icon.stop().animate({ opacity:0 }, faster);
					$zoom.stop().show().animate({ opacity:1 }, faster);
					// Magnifier invert option
					if(o.magnifier_invert){
						$current_thumb.stop().animate({ opacity:o.magnifier_opacity }, faster);
					}
					// Pause autoplay
					if(o.autoplay){
						stopAutoplay();
					}
				}).mouseleave(function(){
					$magnifier.stop().fadeOut(o.speed);
					$icon.stop().animate({ opacity:1 }, o.speed);
					$zoom.stop().animate({ opacity:0 }, o.speed, function(){
						$(this).hide();
					});
					// Magnifier invert option
					if(o.magnifier_invert){
						$current_thumb.stop().animate({ opacity:1 }, o.speed);
					}
					clearTimeout(zoom_move_timer);
					// Restart autoplay
					if(o.autoplay){
						startAutoplay();
					}
				});

				// Magnifier movement
				$thumbs.add($magnifier).add($icon).mousemove(function(e){
					var mouse_x = Math.round( e.pageX - $current_thumb.offset().left + magnifier_left );
					var mouse_y = Math.round( e.pageY - $current_thumb.offset().top + magnifier_top );

					// Magnifier location
					var new_x = (mouse_x-magnifier_center_x);
					var new_y = (mouse_y-magnifier_center_y);
					if( new_x < magnifier_left ){ new_x = magnifier_left; }
					if( new_x > magnifier_right ){ new_x = magnifier_right; }
					if( new_y < magnifier_top ){ new_y = magnifier_top; }
					if( new_y > magnifier_bottom ){ new_y = magnifier_bottom; }
					$magnifier.css({ left:new_x, top:new_y });

					// Magnifier invert option
					if(o.magnifier_invert){
						var invert_x = new_x-magnifier_left;
						var invert_y = new_y-magnifier_top;
						$magnifier_img.css({ left:-invert_x, top:-invert_y });
					}

					// Zoomed area scrolling
					new_zoomx = -( (new_x-magnifier_left) * (1/(o.thumb_image_width/o.source_image_width)) );
					new_zoomy = -( (new_y-magnifier_top) * (1/(o.thumb_image_height/o.source_image_height)) );
					if(o.zoom_easing){
						clearTimeout(zoom_move_timer);
						zoom_move();
					}
					else{
						if(preview){
							$zoom_preview.css({ left:-new_x, top:-new_y });
						}
						$zoom_img.css({ left:-new_x, top:-new_y });
					}
				});

				if(images > 1 || !o.smallthumb_hide_single){
					// Smallthumbs slide left
					$container.find('.etalage_smallthumb_first').on('click', function(){
						// If not already moving
						if(!st_moving){
							st_moving = true;
							var $first = $(this).removeClass('etalage_smallthumb_first');
							var $last = $container.find('.etalage_smallthumb_last').removeClass('etalage_smallthumb_last');
							var $next_first, $next_last, $next_active;

							// If this isnt the first
							if($(this).prev().length > 0){
								$next_first = $first.prev();
								$next_last = $last.prev();
								$next_active = $first;
							}
							// Shift to the end
							else{
								$next_first = $smallthumb.eq(smallthumbs-o.small_thumbs);
								$next_last = $smallthumb.eq(smallthumbs-1);
								$next_active = $next_last.prev();
							}
							var distance = $next_first.position().left;

							// Animate and switch thumb
							st_move(distance, $next_first, $next_last, $next_active);
						}
					});

					// Smallthumbs slide right
					$container.find('.etalage_smallthumb_last').on('click', function(){
						// If not already moving
						if(!st_moving){
							st_moving = true;
							var $first = $container.find('.etalage_smallthumb_first').removeClass('etalage_smallthumb_first');
							var $last = $(this).removeClass('etalage_smallthumb_last');
							var $next_first, $next_last, $next_active;

							// If this isnt the last
							if($(this).next().length > 0){
								$next_first = $first.next();
								$next_last = $last.next();
								$next_active = $last;
							}
							// Shift back to beginning
							else{
								$next_first = $smallthumb.eq(0);
								$next_last = $smallthumb.eq(o.small_thumbs-1);
								$next_active = $next_first.next();
							}
							var distance = $next_first.position().left;

							// Animate and switch thumb
							st_move(distance, $next_first, $next_last, $next_active);
						}
					});

					// Smallthumb click
					$smallthumb.click(function(){
						if(!$(this).hasClass('etalage_smallthumb_first') && !$(this).hasClass('etalage_smallthumb_last') && !$(this).hasClass('etalage_smallthumb_active') && !st_moving){
							st_click($(this), false);
						}
					});

					// Remove loading gifs when full page is loaded
					$(window).bind('load', function(){
						// Large thumbnail background image
						$thumbs.css({ 'background-image':'none' });
						// Zoom background image
						$zoom.css({ 'background-image':'none' });
						// Remove zoom preview
						if(preview){
							preview = false;
							$zoom_preview.remove();
						}
					});

				}

				// Initiate first autoplay
				if(o.autoplay){
					startAutoplay();
				}

			}
		});
	};
})(jQuery);