$(document).ready(function () {
	/* Класс анимации облаков*/

	var Clouds = function () {
		var canvas_color = "rgba(0, 0, 0, 0)";
		var canvas_width;
		var canvas_height;
		var canvas_handler = null;
		var context = null;
		var clouds = [];
		var images = ['/img/clouds1.png', '/img/clouds2.png', '/img/clouds3.png', '/img/clouds4.png'];
		var object_images = {};
		var preload_timer = null;
		var wind_timer = null;
		var clouds_timer = null;
		var wind_vector_timer = null;
		var render_timer = null
		var wind_max = 0.8;
		var is_tab_visible = true;

		this.Init = function () {
			for (var i = 0; i < images.length; i++) {
				preloadImages(images[i], i);
			}

			canvas_width = 1488;
			canvas_height = 927;

			$('div.wrapper').prepend($('<canvas style="z-index:50000;position:absolute;" class="workArea" id="cloud"></canvas>'));

			canvas_handler = document.getElementById('cloud');

			canvas_handler.width = canvas_width;
			canvas_handler.height = canvas_height;

			if (!canvas_handler || !canvas_handler.getContext)
				return false;

			context = canvas_handler.getContext('2d');

			$(window).unbind('blur');
			$(window).unbind('focus');

			$(window).bind('focusout', function () {
				is_tab_visible = false;
			});

			$(window).bind('focus', function () {
				is_tab_visible = true;
			});

			$(document).unbind('blur');
			$(document).unbind('focus');

			$(document).bind('focusout', function () {
				is_tab_visible = false;
			});

			$(document).bind('focus', function () {
				is_tab_visible = true;
			});

			preload_timer = setInterval(onLoadComplete, 300);
		}

		this.stop = function () {
			clearInterval(preload_timer);
			clearInterval(clouds_timer);
			clearInterval(wind_timer);
			clearInterval(render_timer);

			$(canvas_handler).remove();
			canvas_handler = null;
		}

		this.get_state = function () {
			if (canvas_handler != null) {
				return true;
			}

			return false;
		}

		var onLoadComplete = function () {
			if (images.length == getObjectSize(object_images)) {
				clearInterval(preload_timer);

				startAnimation();
			}
		}

		var startAnimation = function () {
			if (clouds.length == 0) {
				create_cloud();
			}

			clouds_timer = setInterval(function () {
				if (is_tab_visible && clouds.length < 15) {
					create_cloud();
				}
			}, 30000);

			wind_timer = setInterval(function () {
				var length = clouds.length;

				for (var i = 0; i < length; i++) {
					clouds[i].wind_velocity = Math.random() * wind_max;
				}
			}, 10000);

			var wind_velocity_timer = setInterval(function () {
				var length = clouds.length;

				for (var i = 0; i < length; i++) {
					if (clouds[i].wind < clouds[i].wind_velocity) {
						clouds[i].wind += 0.1;
					}

					if (clouds[i].wind > clouds[i].wind_velocity && clouds[i].wind > 0.3) {
						clouds[i].wind -= 0.1;
					}
				}
			}, 500);

			wind_vector_timer = setInterval(function () {
				var length = clouds.length;

				for (var i = 0; i < length; i++) {
					clouds[i].wind_vector = Math.floor(Math.random() * 2);
				}
			}, 5000);


			render_timer = setInterval(function () {
				if (is_tab_visible) {
					var length = clouds.length;

					context.fillStyle = canvas_color;
					context.clearRect(0, 0, canvas_width, canvas_height);

					for (var i = 0; i < length; i++) {
						if (clouds[i].wind_vector == 1)
							var new_y = clouds[i].y + (clouds[i].wind / 3);

						if (clouds[i].wind_vector == 2)
							var new_y = clouds[i].y - (clouds[i].wind / 3);

						if (clouds[i].wind_vector == 0)
							var new_y = clouds[i].y;

						var new_x = clouds[i].x + clouds[i].wind;

						clouds[i].y = new_y;
						clouds[i].x = new_x;

						context.drawImage(clouds[i].image.object, new_x, new_y);

						cloud_dead(i);
						length = clouds.length;
					}
				}
			}, 1000 / 33);
		}

		var create_cloud = function () {
			var cloud_type = Math.floor(Math.random() * 3);
			var img = object_images[cloud_type];
			var cloud_x = 0 - img.width;
			var cloud_y = Math.floor(Math.random() * window.innerHeight);

			if (cloud_y < 20) {
				cloud_y = cloud_y + Math.floor(Math.random() * 100);
			}

			if ((cloud_y + img.height) > window.innerHeight) {
				cloud_y = (cloud_y - img.height) - Math.floor(Math.random() * 30);
			}

			var cloud =
			{
				wind: 0,
				wind_velocity: 0.5,
				wind_vector: 0,
				image: img,
				index: cloud_type,
				x: cloud_x,
				y: cloud_y
			}

			clouds.push(cloud);
		}

		var cloud_dead = function (position) {
			var cloud = clouds[position];

			if (cloud.x - cloud.image.object.width > window.innerWidth) {
				clouds.splice(position, 1);
			}
		}

		var preloadImages = function (src, key) {
			var img = new Image();

			img.onload = function () {
				var img_object = {
					width: this.width,
					height: this.height,
					object: this
				}

				object_images[key] = img_object;
			}

			img.src = src;
		}

		getObjectSize = function (obj) {
			var size = 0, key;

			for (key in obj) {
				if (obj.hasOwnProperty(key)) size++;
			}

			return size;
		}
	}

	/* Обработчик перехода по ссылке /towns/horse */

	$('.cityKonik').click(function () {
		clouds_obj.stop();

		canvas_width = 2000;
		canvas_height = 890;

		$('div.allLabels').prepend($('<canvas style="z-index:50002;position:absolute;left:0px;top:0px;" id="cloud"></canvas>'));

		var canvas_handler = document.getElementById('cloud');
		var link = $(this);

		canvas_handler.width = canvas_width;
		canvas_handler.height = canvas_height;

		if (!canvas_handler || !canvas_handler.getContext) {
			return true;
		}

		var context = canvas_handler.getContext('2d');
		var scale = 1;
		var img = new Image();
		var x = 0;
		var y = 0;

		img.src = '/img/main_bg.jpg';

		var render_timer = setInterval(function () {
			if (scale < 1.2) {
				scale = scale + 0.02;
				x = x - (85 * scale);
				y = y - (20 * scale);

				context.scale(scale, scale);
				context.drawImage(img, x, y);
			}
			else {
				clearInterval(render_timer);
				location.href = $(link).attr('href');
			}
		}, 30 );

		return false;
	});

	/* Обработчик перехода по ссылке /towns/alpiyka */

	$('.cityAlpiyka').click(function () {
		clouds_obj.stop();

		canvas_width = 2000;
		canvas_height = 890;

		$('div.allLabels').prepend($('<canvas style="z-index:50002;position:absolute;left:0px;top:0px;" id="cloud"></canvas>'));

		var canvas_handler = document.getElementById('cloud');
		var link = $(this);

		canvas_handler.width = canvas_width;
		canvas_handler.height = canvas_height;

		if (!canvas_handler || !canvas_handler.getContext) {
			return true;
		}

		var context = canvas_handler.getContext('2d');
		var scale = 1.02;
		var img = new Image();
		var x = 0;
		var y = 0;

		img.src = '/img/main_bg.jpg';

		var render_timer = setInterval(function () {
			if (scale < 1.3) {
				scale = scale + 0.02;

				x = x - (35 * scale);
				y = y - (43 * scale);

				context.scale(scale, scale);
				context.drawImage(img, x, y);
			}
			else {
				clearInterval(render_timer);
				location.href = $(link).attr('href');
			}
		}, 30);

		return false;
	});

	/* Обработчик клика на кнопке "Подробно" в окне краткого описания дома */

	$('div.moreBtn').click(function () {
		var win = $('div.houseDescription');
		var link = null;

		$('div.links > a').each(function (i, obj) {
			var id_data = $(this).attr('id').split('_');

			if (id_data[1] == $(win).attr('building_id')) {
				link = $(this);
				return;
			}
		});

		$(link).trigger('click');
		win.fadeOut(100);
	})

	/* Активация галереи изображений "Типовые фотографии" */

	$('#additional_img').live('click', function () {
		if (!$(this).hasClass('active')) {
			$('#place_img').removeClass('active');
			$(this).addClass('active');

			var parent = $('div.additional_img');
			var active = $(parent).find('div.active');
			var active_index = $(active).attr('id').replace('additional_img_', '');
			var img = $('#section_description').find('div.photo >img');
			var show_img = $('#ai_' + active_index);

			$(img).animate({opacity: 0}, 100, function () {
				$(img).attr('src', $(show_img).attr('src'));
				$(img).animate({opacity: 1}, 100);
			});

			$('div.place_img').css('display', 'none');
			$('div.additional_img').css('display', 'block');
		}
	})

	/* Активация галереи изображений "Реальные фотографии" */

	$('#place_img').live('click', function () {
		if (!$(this).hasClass('active')) {
			$('#additional_img').removeClass('active');
			$(this).addClass('active');

			var parent = $('div.place_img');
			var active = $(parent).find('div.active');
			var active_index = $(active).attr('id').replace('place_img_', '');
			var img = $('#section_description').find('div.photo >img');
			var show_img = $('#pi_' + active_index);

			$(img).animate({opacity: 0}, 100, function () {
				$(img).attr('src', $(show_img).attr('src'));
				$(img).animate({opacity: 1}, 100);
			});

			$('div.additional_img').css('display', 'none');
			$('div.place_img').css('display', 'block');

		}
	})

	/* Прелистывание фотографий галереи */

	$('div.listing > div').live('click', function () {
		var active_menu_item = ($('#additional_img').hasClass('active')) ? 0 : 1;
		var parent = $(this).parent();
		var active = $(parent).find('div.active');
		var active_index = (!active_menu_item)
			? $(active).attr('id').replace('additional_img_', '')
			: $(active).attr('id').replace('place_img_', '');

		var click_index = (!active_menu_item)
			? $(this).attr('id').replace('additional_img_', '')
			: $(this).attr('id').replace('place_img_', '');

		var img = $('#section_description').find('div.photo >img');
		var show_img = (!active_menu_item)
			? $('#ai_' + click_index)
			: $('#pi_' + click_index);

		if (active_index != click_index) {
			$(active).removeClass('active');
			$(this).addClass('active');

			$(img).animate({opacity: 0}, 100, function () {
				$(img).attr('src', $(show_img).attr('src'));
				$(img).animate({opacity: 1}, 100);
			});
		}
	})

	$("div.infrastructureBlock > div.menu > div.menuItem").click(function () {
		var id = $(this).attr('id');

		if (id == 'block_content_one') {
			$('#block_content_two').removeClass('active');
			$(this).addClass('active');

			$('div.infrastructureBlock > div.two').fadeOut(300, function () {
				$('div.infrastructureBlock > div.one').fadeIn(300);
			});
		}

		if (id == 'block_content_two') {
			$('#block_content_one').removeClass('active');
			$(this).addClass('active');

			$('div.infrastructureBlock > div.one').fadeOut(300, function () {
				$('div.infrastructureBlock > div.two').fadeIn(300);
			});
		}
	});

	/* Отобразить следующую фотографию */

	$('div.photo > img').live('click', function () {
		var active_menu_item = ($('#additional_img').hasClass('active')) ? 0 : 1;
		var parent = (!active_menu_item)
			? $('div.additional_img')
			: $('div.place_img');
		var active = $(parent).find('div.active');
		var count_img = $(parent).children().length;
		var active_index = (!active_menu_item)
			? $(active).attr('id').replace('additional_img_', '')
			: $(active).attr('id').replace('place_img_', '');
		var click_index = (active_index >= (count_img - 1)) ? 0 : parseInt(active_index) + 1;
		var img = $('#section_description').find('div.photo >img');
		var show_img = (!active_menu_item)
			? $('#ai_' + click_index)
			: $('#pi_' + click_index);

		if (active_index != click_index) {
			$(active).removeClass('active');

			if (!active_menu_item) {
				$('#additional_img_' + click_index).addClass('active');
			}
			else {
				$('#place_img_' + click_index).addClass('active');
			}

			$(img).animate({opacity: 0}, 100, function () {
				$(img).attr('src', $(show_img).attr('src'));
				$(img).animate({opacity: 1}, 100);
			});
		}
	})

	/* Обработчик перехода по ссылке */

	$('a.jslink').live('click', function () {
		var state = {
			title: this.getAttribute("title"),
			url: this.getAttribute("href", 2)
		}

		history.pushState(state, state.title, state.url);

		var segment = get_url_section(state.url, 5, location);

		anchors_section_handler[segment] && anchors_section_handler[segment](segment);

		return false;
	});

	/* Обработчик кнопок навигации браузера Вперед - Назад */

	window.onpopstate = function (e) {
		var segment;

		try {
			segment = get_url_section(history.location.href, 5, location);
			anchors_section_handler[segment] && anchors_section_handler[segment](segment);
		}
		catch(e) {}

	};

	/* Обработчик ссылок основного меню */

	function main_menu_handler (segment) {
		var active_section = $('a.active');
		var active_block_type = $(active_section).attr('id').replace('link_', '');
		var window = $('div.houseCompleteDescription');
		var overlay = $('div.overlay');

		if ($(window).length) {
			$(window).css('display', 'none');
			$(overlay).css('display', 'none');
		}

		if (segment == 'city' && $(active_section).attr('id') != 'link_city') {
			if (!clouds_obj.get_state()) {
				clouds_obj.Init();
			}

			$(active_section).removeClass('active');
			$('#link_city').addClass('active');

			$('#' + active_block_type + "_block").css('display', 'none');
			$('#city_block').css('display', 'block');
		}

		if (segment == 'infrastructure' && $(active_section).attr('id') != 'link_infrastructure') {
			if (!clouds_obj.get_state()) {
				clouds_obj.Init();
			}

			$(active_section).removeClass('active');
			$('#link_infrastructure').addClass('active');

			$('#' + active_block_type + "_block").css('display', 'none');
			$('#infrastructure_block').css('display', 'block');
		}

		if (segment == 'map' && $(active_section).attr('id') != 'link_map') {
			clouds_obj.stop();

			$(active_section).removeClass('active');
			$('#link_map').addClass('active');

			$('#' + active_block_type + "_block").css('display', 'none');
			$('#map_block').css('display', 'block');

			initialize();
		}
	}

	function get_url_section (url, section, location_obj) {
		var segments = url.split('/');
		var segment = segments[section];

		if (location_obj.hash != '') {
			section = section - 2;
			segments = location_obj.hash.split('/');
			segment = segments[section];
		}

		return (section > -1) ? segment : segments;
	}

	function replace_url (value, section, location_obj) {
		var location_segments = location_obj.href.split('/');
		var url;

		if (location_obj.hash != '') {
			section = section - 2;
			hash_segments = location_obj.hash.split('/');

			if (hash_segments[section] != undefined) {
				hash_segments[section] = value;
				url = location_segments.splice(0, 3).join('/') + "/" + hash_segments.splice(1, hash_segments.length).join('/');

				$('a[href="' + url + '"]').trigger('click');
			}
		}

		if (location_segments[section] != undefined) {
			location_segments[section] = value;
			url = location_segments.join('/');

			$('a[href="' + url + '"]').trigger('click');
		}

		return true;
	}

	/* Обработчик открытого дома */

	function open_building () {
		var building_alias = get_url_section(location.href, 6, location);
		var section_active = get_url_section(location.href, 7, location);

		if (building_alias != undefined) {
			clouds_obj.stop();

			var overlay = $('div.overlay');
			var window = $('div.houseCompleteDescription');

			$(overlay).css('display', 'block');

			if ($(window).attr('open_alias') == building_alias) {
				$(window).fadeIn(500);

				$("#singleHouse").scrollable();
				house = $("#singleHouse").data("scrollable");
				singleHouseIndex = $("#section_tour3D").html() ? 1 : 0;

				section_active = (section_active != undefined) ? section_active : 'description';
				navCarousel(section_active);

				return;
			}

			$(window).remove();

			$.ajax(
				{
					url: '/towns/get_building_data',
					type: 'post',
					dataType: "json",
					async: true,
					data: {'alias': building_alias, 'section': section_active},
					success: function (data, textStatus) {
						if (data.status == '1') {
							$('#city_block').append(data.data);
							var window = $('div.houseCompleteDescription');

							$(window).attr('open_alias', building_alias);

							$(window).fadeIn(500, function () {
								$("#singleHouse").scrollable();
								house = $("#singleHouse").data("scrollable");
								singleHouseIndex = $("#section_tour3D").html() ? 1 : 0;

								/**
								 * change width of prev/next overlays for house Description
								 * This size will be set in percentage. We don't need to change
								 * it with js or expression. Also initial calc width was too big.
								 */
//								$(window).bind("resize", navResize);
//								navResize();

								house.onBeforeSeek(function (event, i) {
									if ($(house.getItems()[i]).attr("id")) {
										var new_section = $(house.getItems()[i]).attr("id").replace("section_", "");
										var section_active = get_url_section(location.href, 7, location);

										if (new_section != section_active) {
											replace_url(new_section, 7, location);
										}
									}

								});

								navCarousel(section_active);
							});
						}
					}
				});
		}
	}

	/* Слайдер новостей */

	$('div.news > div.next').click(function () {
		var container = $('div.newsContainer');
		var container_left = ($(container).css('left') != 'auto') ? parseInt($(container).css('left')) : 0;
		var count_items = $(container).children().length;
		var active_item = (container_left == 0) ? 1 : Math.abs(container_left / 375) + 1;

		if (active_item == 0) {
			$('div.news > div.prew').css('display', 'none');
		}

		if (active_item > 0) {
			$('div.news > div.prew').css('display', 'block');
		}

		if ((active_item + 1) == count_items) {
			$('div.news > div.next').css('display', 'none');
		}

		if ((active_item + 1) < count_items) {
			$('div.news > div.next').css('display', 'block');
		}

		$(container).css('left', container_left - 375);
	});

	$('div.news > div.prew').click(function () {
		var container = $('div.newsContainer');
		var container_left = parseInt($(container).css('left'));
		var count_items = $(container).children().length;
		var active_item = (container_left == 0) ? 0 : Math.abs(container_left / 375) - 1;

		if (active_item == 0) {
			$('div.news > div.prew').css('display', 'none');
		}

		if (active_item > 0) {
			$('div.news > div.prew').css('display', 'block');
		}

		if ((active_item - 1) == count_items) {
			$('div.news > div.next').css('display', 'none');
		}

		if ((active_item - 1) < count_items) {
			$('div.news > div.next').css('display', 'block');
		}

		$(container).css('left', (container_left + 375));
	});

	$('div.news_link').click(function () {
		var id = $(this).attr('id').replace('news_', '');
		var overlay = $('div.overlay_news');
		var window = $('div.newsWin');

		$(overlay).css('display', 'block');

		$.ajax(
			{
				url: '/main/get_news',
				type: 'post',
				dataType: "json",
				async: true,
				data: {'id': id},
				success: function (data, textStatus) {
					if (data.status == '1') {
						$('body').append(data.data);
						var window = $('div.newsWin');

						$(window).fadeIn(500);
					}
				}
			});
	});

	/* Закрытие окна подробного описания новости */

	$('div.overlay_news').click(function () {
		var overlay = $('div.overlay_news');
		var window = $('div.newsWin');

		$(overlay).css('display', 'none');
		$(window).fadeOut(500, function () {
			$(window).remove();
		});
	});


	$('div.newsWin > div.closeBtn').live('click', function () {
		var overlay = $('div.overlay_news');
		var window = $('div.newsWin');

		$(overlay).css('display', 'none');
		$(window).fadeOut(500, function () {
			$(window).remove();
		});
	});

	/* Закрытие окна подробного описания дома */

	$('div.overlay').click(function () {
		var link = $('#link_city').click();
	});


	$('div.closeBtn').live('click', function () {
		var link = $('#link_city').click();
		$(document).trigger('popup.close');
	});

	/* Инициализация карты города */

	function initialize () {
		if (map_coordinate == '') {
			$('#link_city').click();
		}

		var coordinate = map_coordinate.split(',');
		var map;
		var latLng = new google.maps.LatLng(coordinate[0], coordinate[1]);
		var myOptions = {
			panControl: true,
			zoomControl: true,
			mapTypeControl: true,
			scaleControl: true,
			streetViewControl: true,
			overviewMapControl: false,
			draggable: true,
			disableDoubleClickZoom: true,     //disable zooming
			scrollwheel: true,
			zoom: 15,
			center: latLng,
			mapTypeId: google.maps.MapTypeId.HYBRID

		};

		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		var image = '/img/marker.png';
		var markerlatlng = new google.maps.LatLng(coordinate[0], coordinate[1]);
		var marker = new google.maps.Marker({
			position: markerlatlng,
			icon: image
		});
		marker.setMap(map);
	}

	/* Ресайз слайдера открытого дома */
	/* @deprecated */
	function navResize () {
		$(".browse.left.prev, .browse.right.next").width((document.body.offsetWidth - 700) / 2);
	};

	/* Прокрутка слайдера открытого дома */

	function navCarousel (hash) {
		var clear_hash = hash.replace(new RegExp(/[0-9]+/), '');
		var floor_num = hash.replace('floor', '');
		$(".menu a").removeClass("active");

		if (clear_hash == 'floor') {
			hash = clear_hash;
		}

		switch (hash) {
			case "tour3D":
				house.seekTo(singleHouseIndex - 1);
				$("#nav_tour3D").addClass("active");
				break;

			case "floor":
				house.seekTo(singleHouseIndex + parseInt(floor_num) + 1);
				$("#nav_plan1, .plan" + floor_num + "menu").addClass("active");
				break;

			case "description":
			default:
				house.seekTo(singleHouseIndex);
				$("#nav_description").addClass("active");
				break;
		}

	};

	/* Инициализация */

	var clouds_obj = new Clouds();

	clouds_obj.Init();
	topMenu.init();
	mapResizer.init();
	makeVisible.init();

	var anchors_section_handler = {
		'city': main_menu_handler,
		'infrastructure': main_menu_handler,
		'map': main_menu_handler,
		'building': open_building
	};

	var house = "";
	var singleHouseIndex = 0;

	var segment = get_url_section(location.href, 5, location);

	if (anchors_section_handler[segment] != undefined && location.hash == '') {
		anchors_section_handler[segment](segment);
	}

	$('body').removeClass('preload');

	$(".menuFooter").bind("click", function () {
		$(".cityMenu a").slideToggle("fast");
	});
});

/**
 * slideDown or slideUp top menu after only once, when "duration" completes
 * @type {{element: null, timeout: null, toggleDelay: number, init: Function, toggleMenu: Function}}
 */
var topMenu = {
	element: null,
	timeout: null,
	toggleDelay: 1000,

	/**
	 * Find & cache menu element
	 */
	init: function () {
		this.element = $('.cityMenu a');
	},

	/**
	 * Slide up (hide) or slide down (show) the menu depending on boolean action flag
	 * @param action {Boolean} if true - hide the menu, if false - show
	 */
	toggleMenu: function (action) {
		if (!this.element.length) {
			return false;
		}

		var that = this;

		this.timeout && clearTimeout(this.timeout);

		this.timeout = setTimeout(function () {
			that.element[action ? 'slideUp' : 'slideDown']('fast');
		}, this.toggleDelay);

	}

};

/**
 * Zoom or Crop Map
 *
 * Visual Area (vis): Area on the map with blurbs and buttons that should be always in view
 * Crop:
 *
 * @Dependencies:
 * 		for the first instance of .workArea -> addional .js-workarea must be added
 * 		for .js-workarea data-active-zone="92,253,924,750" attr must be set
 *
 */
var mapResizer = {
	$win: null,
	$wrapper: null,
	$footer: null,
	$outerWrapper: null,
	$viewScreenWrapper: null,
	$zoomable: null,
	img: null,
	vis: null,
	crop: null,
	scale: null,
	origin: null,

	init: function() {
		this.getElementsData();
		this.events();
		this.fitToWindow();
	},

	events: function() {
		var that = this;
		$(window).bind('resize', function() {
			that.fitToWindow();
		});
	},

	/**
	 * Find layout elements for later usage
	 * Run initial calculations
	 */
	getElementsData: function() {
		var workarea = $('.js-workarea');
		var data = workarea.attr('data-active-zone').split(',');

		this.$win = $(window);
		this.$wrapper = $('.wrapper');
		this.$zoomable = $('.js-zoomable');
		this.$viewScreenWrapper = $('.viewscreen-fixed-wrapper');

		this.$footer = $('.footer');
		this.$outerWrapper = $('.outerWrapper');

		this.getCoordinates(workarea, data);
		this.getVisualAreaCropPercentage();

	},

	/**
	 * Performs main map-sizing logic
	 * If Window size is bigger than the image, zoom-in (stretch to width, avoiding crop of the height important area)
	 * If Window size is smaller than the image but bigger than VisualArea -> crop the image ( with a smart cropping offset,
	 * keeping the Visual Area in the center of the window )
	 * If Window size is smaller than VisualArea - zoom-out , until it becomes visible
	 *
	 * @returns {boolean}
	 */
	fitToWindow: function() {
		var W = this.$win.width();
		/* subtracting footer height: for correct visual area calculations */
		var H = this.$win.height() - this.$footer.outerHeight();
		var transform = 'top ';
		var scale;

		/* dynamic outer-wrapper height */
		this.$outerWrapper.height(H);

		/* get scale index */
		scale = this.getScale(W, H);

		/* for crop set transform-origin to "top center", for zooming "top left" */
		transform += (scale === 1) ? 'center' : 'left';

		this.triggerSpecificSizeEvents(W, H, scale);
		this.updateOffset(W, H, scale);
		this.setTransform(transform);
		this.setScale( scale );
	},

	/**
	 * Trigger show/hide of the top menu, depending on window size
	 * @param W
	 * @param H
	 * @param scale
	 */
	triggerSpecificSizeEvents: function(W, H, scale) {
		var smallScreen = W < 1200 || H < 644;
		topMenu.toggleMenu(smallScreen);
	},

	/**
	 * Set CSS transform property
	 * @param scale {string} scale value
	 */
	setScale: function(scale) {
		if ( scale !== this.scale ) {
			/* zooming wrapper and .js-zoomable elements */
			this.$wrapper.add(this.$zoomable).css({
				'-webkit-transform': 'scale( ' + scale + ' )',
				'-ms-transform': 'scale( ' + scale + ' )',
				'transform': 'scale( ' + scale + ' )'
			});

			this.scale = scale;
		}
	},

	/**
	 * Set CSS transform-origin property
	 * @param transform {string} origin aligning
	 */
	setTransform: function(transform) {
		if (this.origin !== transform) {
			this.$wrapper.css({
				'-webkit-transform-origin': transform,
				'-ms-transform-origin': transform,
				'transform-origin': transform
			});
			this.origin = transform;
		}
	},

	/**
	 * Updates CSS margin property of the .wrapper element, considering scale index
	 * Set Left, Top, Width, Height properties for fixed labels wrapper
	 * @param W {Number} Window width
	 * @param H {Number} Window height
	 * @param scale {Number} Scale index
	 */
	updateOffset: function(W, H, scale) {
		var overflowX = this.img.w * scale - W;
		var overflowY = this.img.h * scale - H;

		var offsetValueX = (overflowX > 0) ? overflowX * this.crop.left : 0;
		var offsetValueY = (overflowY > 0) ? overflowY * this.crop.top : 0;

		this.$wrapper.css({
			'margin-left': -offsetValueX + 'px',
			'margin-top': -offsetValueY + 'px'
		});

		/* Set Left, Top, Width, Height properties for fixed labels wrapper */
		var wrapperX = offsetValueX / scale;
		var wrapperY = offsetValueY / scale;
		var wrapWidth = (overflowX > 0) ? W / scale : this.img.w;
		var wrapHeight = (overflowY > 0) ? H / scale : this.img.h;

		/**
		 * top = 0; Otherwise labels sliding too much vertically. Aligning it to top;
		 */
		this.$viewScreenWrapper.css({
			'left': wrapperX + 'px',
			'top': wrapperY + 'px',	// may be set to 0 , if we don't need a top stick
			'width': wrapWidth + 'px',
			'height': wrapHeight + 'px'
		});
	},

	/**
	 * Get width and height of the workarea element
	 * Get visual area position from data-attribute
	 * @param workarea {Element} jQuery workarea Element
	 * @param data {Array} Visual area x1,y1,x2,y2 coordinates
	 */
	getCoordinates: function(workarea, data) {
		this.img = {
			w: workarea.outerWidth(),
			h: workarea.outerHeight()
		};

		this.vis = {
			w: data[2] - data[0],
			h: data[3] - data[1],
			left: -(-data[0]),
			top: -(-data[1])
		};
	},

	/**
	 * Calculates Visual Area Left / Top pos, to image size ratio
	 * Set "this.crop" prop
	 */
	getVisualAreaCropPercentage: function() {
		this.crop = {
			left: this.vis.left / (this.img.w - this.vis.w),
			top: this.vis.top / (this.img.h - this.vis.h)
		};
	},

	/**
	 * If zooming-out: find less side scale index (so that other side VisualArea wouldn't be cropped)
	 * If zooming-in: Find scale index of the side that is overextended more, and if other side's Visual Area
	 * with a such scaling index wouldn't be cropped, use it. Otherwise, return VisualArea scaling index.
	 *
	 * When it's being zooming-in, we try to avoid blank(white)-spaces on the sides (vertical/horizontal),
	 * and try to stretch the image where these blank-spaces are bigger. But only if we wouldn't crop VisualArea of
	 * the other side, due to zoom-in.

	 * @param W {Number} Window width
	 * @param H {Number} Window height
	 * @returns {Number} Best fitting/calculated Scale index
	 */
	getScale: function(W, H) {

		/* base width, height calculation value (vis for zoom-out, img for zoom-in) */
		var scaleWidthImg = W / this.img.w;
		var scaleHeightImg = H / this.img.h;

		var scaleWidthVis = W / this.vis.w;
		var scaleHeightVis = H / this.vis.h;

		var isVisHeightDominatesImgWidth = scaleWidthImg < scaleHeightVis;
		var isImgWidthDominatesImgHeight = scaleWidthImg >= scaleHeightImg;


		/* zoom out mode */
		if ( W < this.vis.w || H < this.vis.h ) {
			return (scaleWidthVis <= scaleHeightVis) ? scaleWidthVis : scaleHeightVis;

		} else if ( W > this.img.w || H > this.img.h ) {


			/* zoom in mode */
			if ( isImgWidthDominatesImgHeight && isVisHeightDominatesImgWidth) { return scaleWidthImg; }
			if ( isImgWidthDominatesImgHeight && !isVisHeightDominatesImgWidth) { return scaleHeightVis; }

			if ( !isImgWidthDominatesImgHeight && isVisHeightDominatesImgWidth) { return scaleHeightImg; }
			if ( !isImgWidthDominatesImgHeight && !isVisHeightDominatesImgWidth) { return scaleWidthVis; }
		}

		/* crop */
		this.setTransform('top center');
		return 1;
	}
};

/**
 *
 */
var makeVisible = {
	timeout: null,

	init: function() {

		$('[data-make-visible]').each(function() {
			var that = this;

			var makeVisibleSelector = $(this).attr('data-make-visible');
			var makeVisibleElements = $(makeVisibleSelector);

				$(this).hover(
				/* mouseover */

				function() {
					that.timeout && clearTimeout(that.timeout);
					makeVisibleElements.stop(true, true).fadeIn('fast');
					console.log('show');
				},
				/* mouseout */
				function() {
					that.timeout && clearTimeout(that.timeout);
					that.timeout = setTimeout(function() {
						makeVisibleElements.stop(true, true).fadeOut('fast');
						console.log('hide');
					}, 500);
				}
				);
		});

	}

};
