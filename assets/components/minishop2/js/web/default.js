miniShop2 = {
	initialize: function() {
		if(!jQuery().ajaxForm) {
			document.write('<script src="'+miniShop2Config.jsUrl+'lib/jquery.form.min.js"><\/script>');
		}
		if(!jQuery().jGrowl) {
			document.write('<script src="'+miniShop2Config.jsUrl+'lib/jquery.jgrowl.min.js"><\/script>');
		}

		$(document).on('click', 'a.ms2_link,button.ms2_link', function(e) {
			var action = $(this).data('action');
			switch (action) {
				case 'cart/add': miniShop2.Cart.add($(this).data('id'), $(this).data('count'), $(this).data('options')); break;
				case 'cart/remove': miniShop2.Cart.remove($(this).data('key')); break;
				case 'cart/clean': miniShop2.Cart.clean(); break;
				case 'order/submit': miniShop2.Order.submit(); break;
				case 'order/clean': miniShop2.Order.clean(); break;
				default: return;
			}
			return false;
		});

		$(document).on('change', '#msCart input[name="count"]', function(e) {
			miniShop2.Cart.change($(this).data('key'), $(this).val());
			return false;
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
					miniShop2.Cart.add(id, count, json);
				break;
				default: return false;
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
			if (response.success) {
				if (response.message) {
					miniShop2.Message.success(response.message);
				}
				miniShop2.Cart.status(response.data);
			}
			else {
				miniShop2.Message.error(response.message);
			}
		});
	}
	,remove: function(key) {
		$.post(miniShop2Config.actionUrl, {action:"cart/remove", key: key, ctx: miniShop2Config.ctx}, function(response) {
			response = $.parseJSON(response);
			if (response.success) {
				if (response.message) {
					miniShop2.Message.success(response.message);
				}
				miniShop2.Cart.remove_position(key);
				miniShop2.Cart.status(response.data);
			}
			else {
				miniShop2.Message.error(response.message);
			}
		});
	}
	,change: function(key, count) {
		$.post(miniShop2Config.actionUrl, {action:"cart/change", key: key, count: count, ctx: miniShop2Config.ctx}, function(response) {
			response = $.parseJSON(response);
			if (response.success) {
				if (response.message) {
					miniShop2.Message.success(response.message);
				}
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
			$(document).trigger('cartstatus');
		}
	}
	,clean: function() {
		$.post(miniShop2Config.actionUrl, {action:"cart/clean", ctx: miniShop2Config.ctx}, function(response) {
			response = $.parseJSON(response);
			if (response.success) {
				if (response.message) {
					miniShop2.Message.success(response.message);
				}
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
		if (message) {
			$.jGrowl(message, {theme: 'ms2-message-success'});
		}
	}
	,error: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'ms2-message-error', sticky: true});
		}
	}
	,info: function(message) {
		if (message) {
			$.jGrowl(message, {theme: 'ms2-message-info'});
		}
	}
	,close: function() {
		$.jGrowl('close');
	}
};

miniShop2.Utils = {
	empty: function(val) {
		return (typeof(val) == 'undefined' || val == 0 || val === null || val === false
			|| (typeof(val) == 'string' && val.replace(/\s+/g, '') == '')
			|| (typeof(val) == 'array' && val.length == 0)
		);
	}
};

miniShop2.Gallery = {
	initialize: function(selector) {
		var gallery = $(selector);

		$(document).on('click', selector + ' .thumbnail', function(e) {
			var src = $(this).attr('href');
			var href = $(this).data('image');
			$('#mainImage', gallery).attr('src', src).parent().attr('href', href);
			return false;
		});

		$('.thumbnail:first', gallery).trigger('click');
	}
};

miniShop2.Order = {
	element: null
	,initialize: function(selector) {
		var order = this.element = $(selector);
		var deliveries = $('#deliveries', order);
		var payments = $('#payments', order);

		order.on('change', 'input,textarea', function(e) {
			var key = $(this).attr('name');
			var value = $(this).val();
			miniShop2.Order.add(key,value);
		});

		$(document).on('cartstatus', '', function(e,status) {
			miniShop2.Order.getcost();
		});

		$(document).ajaxStart(function() {
			$("#orderSubmit").attr('disabled',true);
		})
		.ajaxComplete(function() {
			$("#orderSubmit").attr('disabled',false);
		});

		this.updatePayments($('input[name="delivery"]:checked', this.element).data('payments'));
	}
	,updatePayments: function(payments) {
		$('input[name="payment"]', this.element).attr('disabled',true).parent().hide();
		if (payments.length > 0) {
			for (i in payments) {
				$('input#payment_'+payments[i]).attr('disabled',false).parent().show();
			}
		}
		if ($('input[name="payment"]:visible:checked', this.element).length == 0) {
			$('input[name="payment"]:visible:first', this.element).trigger('click');
		}
	}
	,add: function(key, value) {
		var old_value = value;
		$.post(miniShop2Config.actionUrl, {action:"order/add", key: key, value: value, ctx: miniShop2Config.ctx}, function(response) {
			response = $.parseJSON(response);
			if (response.success) {
				if (response.message) {
					miniShop2.Message.success(response.message);
				}
				var field = $('[name="'+key+'"]');
				switch (key) {
					case 'delivery':
						field = $('#delivery_'+response.data[key]);
						if (response.data[key] != old_value) {
							field.trigger('click');
						}
						else {
							miniShop2.Order.updatePayments(field.data('payments'));
							$(document).trigger('cartstatus');
						}
					break;
					case 'payment':
						field = $('#payment_'+response.data[key]);
						if (response.data[key] != old_value) {
							field.trigger('click');
						}
					break;
					default: field.val(response.data[key]);
				}
			}
			else {
				miniShop2.Message.error(response.message);
				field.val('');
			}
		});
	}
	,getcost: function() {
		$.post(miniShop2Config.actionUrl, {action:"order/getcost", ctx: miniShop2Config.ctx}, function(response) {
			response = $.parseJSON(response);
			if (response.success) {
				if (response.message) {
					miniShop2.Message.success(response.message);
				}
				$('#ms2_order_cost').text(response.data['cost']);
			}
			else {
				miniShop2.Message.error(response.message);
			}
		});
	}
	,clean: function() {
		$.post(miniShop2Config.actionUrl, {action:"order/clean", ctx: miniShop2Config.ctx}, function(response) {
			response = $.parseJSON(response);
			if (response.success) {
				if (response.message) {
					miniShop2.Message.success(response.message);
				}
				document.location = document.location;
			}
			else {
				miniShop2.Message.error(response.message);
			}
		});
	}
	,submit: function() {
		miniShop2.Message.close();
		$.post(miniShop2Config.actionUrl, {action:"order/submit", ctx: miniShop2Config.ctx}, function(response) {
			response = $.parseJSON(response);
			if (response.success) {
				if (response.message) {
					miniShop2.Message.success(response.message);
				}
				if (response.data['redirect']) {
					document.location.href = response.data['redirect'];
				}
				else if (response.data['msorder']) {
					document.location.href = /\?/.test(document.location.href) ? document.location.href + '&msorder=' + response.data['msorder'] : document.location.href + '?msorder=' +  response.data['msorder'];
				}
				else {
					document.location = document.location;
				}
			}
			else {
				miniShop2.Message.error(response.message);
				$('[name]', this.element).removeClass('error');
				for (i in response.data) {
					var field = $('[name="'+response.data[i]+'"]', this.element);
					if (field.attr('type') == 'checkbox' || field.attr('type') == 'radio') {
						field.parent().addClass('error');
					}
					else {
					field.addClass('error');
					}
				}
			}
		});
	}
};


miniShop2.initialize();