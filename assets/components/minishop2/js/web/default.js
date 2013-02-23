miniShop2 = {
	initialize: function() {
		if(!jQuery().ajaxForm) {
			document.write('<script src="'+miniShop2Config.jsUrl+'lib/jquery.form.min.js"><\/script>');
		}
		if(!jQuery().jGrowl) {
			document.write('<script src="'+miniShop2Config.jsUrl+'lib/jquery.jgrowl.min.js"><\/script>');
		}

		$(document).on('click', 'a.ms2_link', function(e) {
			var action = $(this).data('action');
			switch (action) {
				case 'cart/add': miniShop2.Cart.add($(this).data('id'), $(this).data('count'), $(this).data('options')); break;
				case 'cart/remove': miniShop2.Cart.remove($(this).data('key')); break;
				case 'cart/clean': miniShop2.Cart.clean(); break;
				default: return;
			}
			e.preventDefault();
		});

		$(document).on('change', '#msCart input[name="count"]', function(e) {
			miniShop2.Cart.change($(this).data('key'), $(this).val());
			e.preventDefault();
		});

		$(document).on('submit', 'form.ms2_form', function(e) {
			var action = $(this).data('action');
			switch (action) {
				case 'cart/add':
					var json = {};
					$.map($(this).serializeArray(), function(n, i) {
						json[n['name']] = n['value'];
					});
					var id = json['id']; delete(json['id']);
					var count = json['count']; delete(json['count']);
					miniShop2.Cart.add(id, count, json); break;
				default: return;
			}
			return false;
		});

		$(document).ready(function() {
			$.jGrowl.defaults.closerTemplate = '<div>[ '+miniShop2Config.close_all_message+' ]</div>';
		});
	}
};

miniShop2.Cart = {
	add: function(id, count, options) {
		params = {
			action: 'cart/add'
			,ctx: miniShop2Config.ctx
			,id: id
			,count: count || 1
			,options: options || []
		};

		$.post(miniShop2Config.actionUrl, params, function(response) {
			response = $.parseJSON(response);
			if (response.success && response.message) {
				miniShop2.Message.success(response.message);
				miniShop2.Cart.status(response.data);
			}
			else {
				miniShop2.Message.error(response.message);
			}
		});
	}
	,remove: function(key) {
		$.post(miniShop2Config.actionUrl, {action:"cart/remove", key: key}, function(response) {
			response = $.parseJSON(response);
			if (response.success && response.message) {
				miniShop2.Message.success(response.message);
				miniShop2.Cart.remove_position(key);
				miniShop2.Cart.status(response.data);
			}
			else {
				miniShop2.Message.error(response.message);
			}
		});
	}
	,change: function(key, count) {
		$.post(miniShop2Config.actionUrl, {action:"cart/change", key: key, count: count}, function(response) {
			response = $.parseJSON(response);
			if (response.success && response.message) {
				miniShop2.Message.success(response.message);
				if (typeof(response.data.key) == 'undefined') {
					miniShop2.Cart.remove_position(key);
				}
				else {
					$('#'+key).find('')
				}
				miniShop2.Cart.status(response.data);
			}
			else {
				miniShop2.Message.error(response.message);
			}
		});
	}
	,status: function(status) {
		if (status.total_count < 1) {
			document.location = document.location;
		}
		else {
			var cart = $('#msMiniCart');
			if (status.total_count > 0 && $('.empty', cart).is(':visible')) {
				$('.empty', cart).hide();
				$('.not_empty', cart).show();
			}
			$('.ms2_total_weight').text(status.total_weight);
			$('.ms2_total_count').text(status.total_count);
			$('.ms2_total_cost').text(status.total_cost);
		}
	}
	,clean: function() {
		$.post(miniShop2Config.actionUrl, {action:"cart/clean"}, function(response) {
			response = $.parseJSON(response);
			if (response.success && response.message) {
				miniShop2.Message.success(response.message);
				miniShop2.Cart.status(response.data);
			}
			else {
				miniShop2.Message.error(response.message);
			}
		});
	}
	,remove_position: function(key) {
		$('#'+key).remove();
	}
};

miniShop2.Message = {
	success: function(message) {
		$.jGrowl(message, {theme: 'ms2-message-success'});
	}
	,error: function(message) {
		$.jGrowl(message, {theme: 'ms2-message-error', sticky: true});
	}
	,info: function(message) {
		$.jGrowl(message, {theme: 'ms2-message-info'});
	}
};

miniShop2.Utils = {
	empty: function(val) {
		return (typeof(val) == 'undefined' || val == 0 || val === null || val === false
			|| (typeof(val) == 'string' && val.replace(/\s+/g, '') == '')
			|| (typeof(val) == 'array' && val.length == 0)
		);
	}
	,money_format: function(value) {
		var i, j, kw, kd, km;
		dec_point = ".";
		thousands_sep = " ";
		decimals = 2;

		if (Math.floor(number) == number) {decimals = 0}
		i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

		if ((j = i.length) > 3) {j = j % 3;} else{j = 0;}

		km = (j ? i.substr(0, j) + thousands_sep : "");
		kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);

		return km + kw;
	}
	,weight_format: function(value) {
		return this.money_format(value);
	}
};

miniShop2.Gallery = {
	initialize: function(selector) {
		var gallery = $(selector);

		$(document).on('click', selector + ' .thumbnail', function(e) {
			e.preventDefault();
			var src = $(this).attr('href');
			var href = $(this).data('image');
			$('#mainImage', gallery).attr('src', src).parent().attr('href', href);
		});

		$('.thumbnail:first', gallery).trigger('click');
	}
};

miniShop2.initialize();