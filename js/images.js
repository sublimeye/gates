Images = function () {
	var shapes = [];
	var shape_opacity = 0.4;
	var r = null;
	var image_handler = null;
	var self = null;
	var isVisiblePopup = false;

	this.Init = function () {
		self = this;

		r = Raphael('map', 1900, 1010);
		image_handler = r;

		$(".houseDescription .closeBtn").live('click', function () {
			shape_out_handler();
		});

		/* additional event listener */
		$(document).bind('popup.close', function() {
			isVisiblePopup = false;
		});

		$(document).live('click', function(e) {
			if ( !$(e.target).closest('.houseDescription, .houseCompleteDescription').length && isVisiblePopup) {

				shape_out_handler('instant');
				isVisiblePopup = false;
			}
		});

		$(document).live('keydown', function( e ) {
			if (e.which === 27 && isVisiblePopup) {
				shape_out_handler('instant');
				/* very ugly freaky and stupid hack , inherited from theCoder */
				$('#link_city').click();
				isVisiblePopup = false;
			}
		});

		/* broken redundant stupid code */
		$('div.map').live('click', function () {
			shape_out_handler();
		});

		$('div.pin').live('click', function (e) {
			shape_out_handler(1);

			var win = $('div.houseDescription');

			if ($(win).is(':hidden')) {
				shape_over_handler(e, 0, 0, $(this).attr('array_index'));
			}
			else {
				$(win).attr('not_hide', true);
			}
		});

	};


	this.createShape = function (points, attr) {
		var path = pointToPath(points);
		var shape = r.path(path);
		var pin_point = findPoint(points);

		var construction_pin = '<div class="pin construction" array_index="' + shapes.length + '" style="top: ' + pin_point[1] + 'px; left: ' + pin_point[0] + 'px"></div>';
		var soldFurnished = '<div class="pin soldFurnished" array_index="' + shapes.length + '"  style="top: ' + pin_point[1] + 'px; left: ' + pin_point[0] + 'px"></div>';
		var sold = '<div class="pin sold" array_index="' + shapes.length + '"  style="top: ' + pin_point[1] + 'px; left: ' + pin_point[0] + 'px"></div>';

		shape.attr(
			{
				'stroke-width': 0,
				"fill": '#000',
				"fill-opacity": 0,
				"class": shapes.length
			});

		if (attr.state == '0') {
			$('#map').append(construction_pin);

			if (attr.state == '0') {
				shape.attr({
					'stroke-width': 0,
					"fill": '#000',
					"fill-opacity": shape_opacity
				});
			}
		}

		if (attr.state == '2' || attr.state == '1') {
			$('#map').append(sold);
		}

		if (attr.state == '3') {
			$('#map').append(soldFurnished);
		}

		shape.array_index = shapes.length;

		var pin = $('div.pin[array_index="' + shape.array_index + '"]');

		$(pin).hover(pin_hover, pin_out_hover);

		shape.click(function(e) {
			shape_over_handler(e);
		});
		shape.mouseover(shape_hover);
		shape.mouseout(shape_out_hover);

		shapes.push({
			'points': points,
			'shape': shape,
			'path': path,
			'attr': attr,
			'pin_point': pin_point
		});

		return shapes.length;
	}

	var findPoint = function (points) {
		var min_width = 20000000;
		var max_width = 0;

		var min_height = 20000000;
		var max_height = 0;

		for (var i = 0; i < points.length; i++) {

			if (points[i][1] < min_height) {
				min_height = points[i][1];
			}

			if (points[i][1] > max_height) {
				max_height = points[i][1];
			}

			if (points[i][0] < min_width) {
				min_width = points[i][0];
			}

			if (parseInt(points[i][0]) > parseInt(max_width)) {
				max_width = points[i][0];
			}
		}

		var width = (parseInt(min_width) + ((parseInt(max_width) - parseInt(min_width)) / 2)) - (42 / 2);
		var height = (parseInt(min_height) + ((parseInt(max_height) - parseInt(min_height)) / 2)) - (58);

		return [width, height];
	}

	var pointToPath = function (points, isNew) {
		var path_string = "";

		if (points.length > 0) {
			for (var i = 0; i < points.length; i++) {
				if (i == 0) {
					path_string = "M " + points[i][0] + " " + points[i][1];
				}

				path_string += " L " + points[i][0] + " " + points[i][1];
			}

			if (!isNew)
				path_string += " Z";
		}

		return path_string;
	}

	var shape_over_handler = function (e, x, y, array_index) {
		var index = (array_index == undefined) ? this.array_index : array_index;
		var win = $('div.houseDescription');
		var bilding_data = shapes[index];

		if (!bilding_data) {
			return;
		}

		var pin_point = bilding_data.pin_point;
		bilding_data = bilding_data.attr;

		$(win).attr('shape_over', 'true');

		if ($(win).is(':hidden') || $(win).attr('building_id') != bilding_data['id']) {
			$(win).find('h3').html(bilding_data.name);
			if (parseInt(bilding_data.square) > 800) {
				$(win).find('div.footage').html("<b>" + (parseInt(bilding_data.square) / 10000) + "</b> га")
			} else {
				$(win).find('div.footage').html("<b>" + bilding_data.square + "</b> м<sup>2</sup>")
			}
			//$(win).find('div.footage > b').html(bilding_data.square);
			$(win).find('div.shortDesc').html(bilding_data.description);

			$(win).find('div.photo').html('');

			if (bilding_data['img'] != '') {
				var img = '<img>';

				$(win).find('div.photo').addClass('preload');
				$(win).find('div.photo').append(img);

				$(win).find('div.photo > img').bind('load', function () {
					$(win).find('div.photo').removeClass('preload');
				});

				$(win).find('div.photo > img').attr('src', bilding_data['img']);
			}

			if (bilding_data.state == '3') {
				$(win).find('div.interiorStatus, div.footage').addClass('furnished');
				$(win).find('div.interiorStatus, div.footage').removeClass('nofurnished').removeClass('constructed');
				$(win).find('div.interiorStatus').html("Продается меблированным");
			}

			if (bilding_data.state == '2') {
				$(win).find('div.interiorStatus, div.footage').addClass('nofurnished');
				$(win).find('div.interiorStatus, div.footage').removeClass('furnished').removeClass('constructed');
				$(win).find('div.interiorStatus').html("Построен и продается");
			}

			if (bilding_data.state == '1' || bilding_data.state == '0') {
				$(win).find('div.interiorStatus, div.footage').addClass('constructed');
				$(win).find('div.interiorStatus, div.footage').removeClass('furnished').removeClass('nofurnished');

				if (bilding_data.state == '1') {
					$(win).find('div.interiorStatus, div.footage').addClass('nofurnished');

					$(win).find('div.interiorStatus, div.footage').removeClass('furnished').removeClass('constructed');
					$(win).find('div.interiorStatus').html("Продается участок");
				}

				if (bilding_data.state == '0') {
					$(win).find('div.interiorStatus').html("В стадии строительства");
				}

			}

			$('div.gallery').html('');

			if (bilding_data.images.length > 0) {
				var img = "";

				for (var i = 0; i < bilding_data.images.length; i++) {
					img = ' <div class="smallPhoto"><img src="/user_files/building_place_images/small_' + bilding_data.images[i]['img'] + '" /></div>';
					$('div.gallery').append(img);
				}
			}

			/* retardation */
/*
			var x = parseInt(pin_point[0]);
			var y = parseInt(pin_point[1]);

			window.innerWidth = window.innerWidth || document.documentElement.clientWidth;
			window.innerHeight = window.innerHeight || document.documentElement.clientHeight;

			var container_width = $('#city_block').width();
			var offset_x = Math.floor((parseInt(container_width) - window.innerWidth) / 2);
			var container_height = $('#city_block').height();
			var offset_y = Math.floor((parseInt(container_height) - (window.innerHeight - 70)) / 2);

			x = x - offset_x;
			y = y - offset_y;

			if (x + win_width > window.innerWidth) {
				x = window.innerWidth - (win_width + 30);
			}

			if (y + win_height > (window.innerHeight - 70)) {
				y = (window.innerHeight - 70) - (win_height + 30);
			}
*/

//			var xe = e.pageX - $(e.target).offset().left;

			var win_width = $(win).width();
			var win_height = $(win).height();

			var position = getMousePosition(e);

			position.top -= win_height / 2;
			position.left -= win_width / 2;

			if (position.top < 0) {position.top = 20};
			if (position.left < 0) {position.left = 20};

			$(win).draggable();

			$(win).css('left', position.left);
			$(win).css('top', position.top);

			$(win).attr('building_id', bilding_data['id']);
			$(win).attr('item', bilding_data['building_id']);

			$(win).fadeIn('fast', function() {
				isVisiblePopup = true;
			});
		}
	}


		getMousePosition = function(e) {
			return {left: e.pageX, top: e.pageY};
		};

	var shape_out_handler = function () {
		var win = $('div.houseDescription');

		$(win).removeAttr('shape_over');
		$(win).removeAttr('not_hide');

		if ($(win).is(':visible') && $(win).attr('not_hide') != 'true' &&
			$(win).attr('shape_over') != 'true') {
			$(win).removeAttr('building_id');
			$(win).removeAttr('item');
			if (arguments[0]) {
				$(win).hide();
				isVisiblePopup = false;
			} else {
				$(win).fadeOut('fast', function() {
					isVisiblePopup = false;
				});
			}
		}
	}

	var shape_hover = function () {
		var pin = $('div.pin[array_index="' + this.array_index + '"]');

		this.attr(
			{
				"fill-opacity": 0.2
			});

		$(pin).css('opacity', 0.8);
	}

	var shape_out_hover = function () {
		var shape = shapes[this.array_index];
		var pin = $('div.pin[array_index="' + this.array_index + '"]');

		if (shape.attr.state == '0') {
			this.attr({"fill-opacity": shape_opacity});
		}
		else {
			this.attr({ "fill-opacity": 0});
		}

		$(pin).css('opacity', 1);
	}

	var pin_hover = function () {
		var shape = shapes[$(this).attr("array_index")];

		$(this).css('opacity', 0.8);

		shape.shape.attr(
			{
				"fill-opacity": 0.2
			});
	}

	var pin_out_hover = function () {
		var shape = shapes[$(this).attr("array_index")];

		if (shape.attr.state == '0') {
			shape.shape.attr({"fill-opacity": shape_opacity});
		}
		else {
			shape.shape.attr({ "fill-opacity": 0});
		}

		$(this).css('opacity', 1);
	}
}

