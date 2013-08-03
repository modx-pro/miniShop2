// replace 'no-js' class of the 'html' tag to 'js'
(function(H){H.className=H.className.replace(/\bno-js\b/, 'js')})(document.documentElement);

window.jQuery || document.write('<script src="' + miniShop2Config.jsUrl + 'lib/jquery.min.js"><\/script>');
// typeof $.fn.ajaxForm == 'function' || document.write('<script src="'+miniShop2Config.jsUrl+'lib/jquery.form.min.js"><\/script>');
typeof $.fn.jGrowl == 'function' || document.write('<script src="' + miniShop2Config.jsUrl + 'lib/jquery.jgrowl.min.js"><\/script>');

(function(window, document, $, undefined) {

	miniShop2.setup = function() {
		// selectors & $objects
		this.actionName	= 'ms2_action';
		this.action		= ':submit[name=' + this.actionName + ']';
		this.form		= '.ms2_form';
		this.$doc		= $(document);

		this.sendData = {
			$form		: null
			,action		: null
			,formData	: null
		};
		this.xhrs = {};
	};
	miniShop2.initialize = function() {
		miniShop2.setup();
		// Indicator of active ajax request
		ajaxProgress = false;

		miniShop2.$doc
			.ajaxStart(function() {
				ajaxProgress = true;
			})
			.ajaxStop(function() {
				ajaxProgress = false;
			})
			.on('submit', miniShop2.form, function(e) {
				e.preventDefault();
				var $form = $(this),
					// action	= $form.find(self.defaultAction).val();
					action = $form.find(miniShop2.action).val();
				if (!action) return;
				formData = $form.serializeArray();
				formData.push({
					name	: miniShop2.actionName,
					value	: action
				});
				miniShop2.sendData = {
					$form		: $form
					,action		: action
					,formData	: formData
				};

				miniShop2.controller();
			})

		miniShop2.Cart.initialize();
		miniShop2.Message.initialize();
		miniShop2.Order.initialize();
		miniShop2.Gallery.initialize();
	}
	miniShop2.clearSendData = function () {
		miniShop2.sendData = {
			$form		: null
			,action		: null
			,formData	: null
		};
	};
	miniShop2.controller = function() {
		var self = this;
		switch (self.sendData.action) {
			case 'cart/add':
				miniShop2.Cart.add();
				break;
			case 'cart/remove':
				miniShop2.Cart.remove();
				break;
			case 'cart/change':
				miniShop2.Cart.change();
				break;
			case 'cart/clean':
				miniShop2.Cart.clean();
				break;
			case 'order/submit':
				miniShop2.Order.submit();
				break;
			case 'order/clean':
				miniShop2.Order.clean();
				break;
			default:
				return;
		}
	};
	miniShop2.send = function(data, controller, method) {
		// miniShop2.Order.callbacks.getRequired, miniShop2.Callbacks.Order.getRequired
		if (controller && method) {
			var ms2Callbacks	= miniShop2[controller].callbacks[method] || {};
			var userCallbacks	= miniShop2.Callbacks[controller][method] || {};
			var runCallback		= function(callback, obj) {
				var func = miniShop2.Utils.checkPropertyRecursive(callback, obj);
				if (typeof func == 'function') {
					return func.apply(miniShop2, Array.prototype.slice.call(arguments, 2));
				}
			}
		} else {
			var ms2Callbacks	= {};
			var userCallbacks	= {};
			var runCallback		= function(){};
		}

		var action;
		// set context
		if ($.isArray(data)) {
			action = miniShop2.Utils.getValueFromSerializedArray(miniShop2.actionName, data);
			data.push({
				name: 'ctx',
				value: miniShop2Config.ctx
			});
		} else if ($.isPlainObject(data)) {
			action = data[miniShop2.actionName];
			data.ctx = miniShop2Config.ctx;
		} else if (typeof data == 'string') {
			action = miniShop2.Utils.getValueFromQS(miniShop2.actionName, data);
			data += '&ctx=' + miniShop2Config.ctx;
		}

		if (miniShop2.xhrs.hasOwnProperty(action)) {
			miniShop2.xhrs[action].abort();
		}

		// set action url
		var formActionUrl = (miniShop2.sendData.$form) ? miniShop2.sendData.$form.attr('action') : false;
		var url = (formActionUrl) ? formActionUrl : (miniShop2Config.actionUrl) ? miniShop2Config.actionUrl : document.location.href;
		// set request method
		var formMethod = (miniShop2.sendData.$form) ? miniShop2.sendData.$form.attr('method') : false;
		var method = (formMethod) ? formMethod : 'post';
		(function(action, url, method, data, ms2Callbacks, userCallbacks) {
			// callback before
			if (runCallback('before', ms2Callbacks) === false ||
				runCallback('before', userCallbacks) === false
			) {
				return;
			}
			// send
			miniShop2.xhrs[action] = $[method](url, data, function(response) {
				var type = (response.success) ? 'success' : 'error';
				if (response.message) {
					miniShop2.Message[type](response.message);
				}
				runCallback('response.'+ type, ms2Callbacks, response);
				runCallback('response.'+ type, userCallbacks, response);
			}, 'json')
			.done(function() {
				runCallback('ajax.done', ms2Callbacks);
				runCallback('ajax.done', userCallbacks);
			})
			.fail(function() {
				runCallback('ajax.fail', ms2Callbacks);
				runCallback('ajax.fail', userCallbacks);
			})
			.always(function() {
				delete(miniShop2.xhrs[action]);
				runCallback('ajax.always', ms2Callbacks);
				runCallback('ajax.always', userCallbacks);
			});
		})(action, url, method, data, ms2Callbacks, userCallbacks);
	};

	miniShop2.Cart = {
		setup: function() {
			miniShop2.Cart.cart						= '#msCart';
			miniShop2.Cart.miniCart					= '#msMiniCart';
			miniShop2.Cart.miniCartNotEmptyClass	= 'not_empty';
			miniShop2.Cart.countInput				= 'input[name=count]';
			miniShop2.Cart.totalWeight				= '.ms2_total_weight';
			miniShop2.Cart.totalCount				= '.ms2_total_count';
			miniShop2.Cart.totalCost				= '.ms2_total_cost';

			miniShop2.Cart.callbacks = {};
		}
		,initialize: function() {
			miniShop2.Cart.setup();
			if (!$(miniShop2.Cart.cart).length) return;

			miniShop2.$doc
				.on('change', miniShop2.Cart.cart + ' ' + miniShop2.Cart.countInput, function(e) {
					$(this).closest(miniShop2.form).submit();
				});
		}
		,add: function() {
			miniShop2.Cart.callbacks.add = {
				response: {
					success: function(response) {
						miniShop2.Cart.status(response.data);
					}
				}
			};
			miniShop2.send(miniShop2.sendData.formData, 'Cart', 'add');
		}
		,remove: function() {
			miniShop2.Cart.callbacks.remove = {
				response: {
					success: function(response) {
						miniShop2.Cart.remove_position(miniShop2.Utils.getValueFromSerializedArray('key'));
						miniShop2.Cart.status(response.data);
					}
				}
			};

			miniShop2.send(miniShop2.sendData.formData, 'Cart', 'remove');
		}
		,change: function() {
			miniShop2.Cart.callbacks.change = {
				response: {
					success: function(response) {
						if (typeof(response.data.key) == 'undefined') {
							miniShop2.Cart.remove_position(miniShop2.Utils.getValueFromSerializedArray('key'));
						} else {
							$('#' + miniShop2.Utils.getValueFromSerializedArray('key')).find('');
						}
						miniShop2.Cart.status(response.data);
					}
				}
			};
			miniShop2.send(miniShop2.sendData.formData, 'Cart', 'change');
		}
		,status: function(status) {
			if (status.total_count < 1) {
				document.location = document.location;
			} else {
				var $cart = $(miniShop2.Cart.cart);
				var $miniCart = $(miniShop2.Cart.miniCart);
				if (status.total_count > 0) {
					$miniCart.addClass(miniShop2.Cart.miniCartNotEmptyClass);
				} else {
					$miniCart.removeClass(miniShop2.Cart.miniCartNotEmptyClass);
				}
				$(miniShop2.Cart.totalWeight)
					.text(miniShop2.Utils.formatWeight(status.total_weight));
				$(miniShop2.Cart.totalCount).text(status.total_count);
				$(miniShop2.Cart.totalCost)
					.text(miniShop2.Utils.formatPrice(status.total_cost));
				miniShop2.Order.getcost();
			}
		}
		,clean: function() {
			miniShop2.Cart.callbacks.clean = {
				response: {
					success: function(response) {
						miniShop2.Cart.status(response.data);
					}
				}
			};

			miniShop2.send(miniShop2.sendData.formData, 'Cart', 'clean');
		}
		,remove_position: function(key) {
			$('#' + key).remove();
		}
	};

	miniShop2.Gallery = {
		setup: function() {
			miniShop2.Gallery.gallery	= '#msGallery';
			miniShop2.Gallery.mainImage	= '#mainImage';
			miniShop2.Gallery.thumbnail	= '.thumbnail';
		}
		,initialize: function(selector) {
			miniShop2.Gallery.setup();
			if (!$(miniShop2.Gallery.gallery).length) return;

			miniShop2.$doc.on('click', miniShop2.Gallery.gallery + ' ' + miniShop2.Gallery.thumbnail, function(e) {
				var src = $(this).attr('href');
				var href = $(this).data('image');
				$(miniShop2.Gallery.mainImage, miniShop2.Gallery.gallery).attr('src', src).parent().attr('href', href);
				e.preventDefault();
			});

			$(miniShop2.Gallery.thumbnail + ':first', miniShop2.Gallery.gallery).trigger('click');
		}
	};

	miniShop2.Order = {
		setup: function() {
			miniShop2.Order.order						= '#msOrder';
			miniShop2.Order.deliveries					= '#deliveries';
			miniShop2.Order.payments					= '#payments';
			miniShop2.Order.deliveryInput				= 'input[name="delivery"]';
			miniShop2.Order.inputParent					= '.input-parent';
			miniShop2.Order.paymentInput				= 'input[name="payment"]';
			miniShop2.Order.paymentInputUniquePrefix	= 'input#payment_';
			miniShop2.Order.deliveryInputUniquePrefix	= 'input#delivery_';
			miniShop2.Order.orderCost					= '#ms2_order_cost'

			miniShop2.Order.callbacks = {};
		}
		,initialize: function() {
			miniShop2.Order.setup();
			if (!$(miniShop2.Order.order).length) return;

			miniShop2.$doc
				.on('click', miniShop2.Order.order + ' [name="' + miniShop2.actionName + '"][value="order/clean"]', function(e) {
					miniShop2.Order.clean();
					e.preventDefault();
				})
				.on('change', miniShop2.Order.order + ' input, textarea', function(e) {
					var $this	= $(this);
					var key		= $this.attr('name');
					var value	= $this.val();
					miniShop2.Order.add(key, value);
				});

			var $deliveryInputChecked = $(miniShop2.Order.deliveryInput + ':checked', miniShop2.Order.order);
			$deliveryInputChecked.trigger('change');
			miniShop2.Order.updatePayments($deliveryInputChecked.data('payments'));
			return true;
		}
		,updatePayments: function(payments) {
			var $paymentInputs = $(miniShop2.Order.paymentInput, miniShop2.Order.order);

			$paymentInputs.removeAttr('disabled').prop('disabled', true)
				.closest(miniShop2.Order.inputParent).hide();
			if (payments.length > 0) {
				for (i in payments) if (payments.hasOwnProperty(i)) {
					$paymentInputs.filter(miniShop2.Order.paymentInputUniquePrefix + payments[i]).attr('disabled', 'disabled').prop('disabled', false)
						.closest(miniShop2.Order.inputParent).show();
				}
			}
			if ($paymentInputs.filter(':visible:checked').length == 0) {
				$paymentInputs.filter(':visible:first').prop('checked', true).trigger('change');
			}
		}
		,getRequired: function(value) {
			miniShop2.Order.callbacks.getRequired = {
				response: {
					success: function(response) {
						var requires = response.data.requires;
						console.log(requires);
						$('[name]', miniShop2.Order.order).each(function(i, input){
							var $input = $(input);
							// console.log(input.name, $.inArray(input.name, requires));
							if ($.inArray(input.name, requires) >= 0) {
								$input.addClass('required')
									.closest(miniShop2.Order.inputParent).addClass('required');
							} else {
								$input.removeClass('required error')
									.closest(miniShop2.Order.inputParent).removeClass('required error');
							}
						});
					}
					,error: function(response) {
						$('[name]', miniShop2.Order.order).removeClass('required error')
							.closest(miniShop2.Order.inputParent).removeClass('required error');
					}
				}
			};

			var data = {};
			if (typeof value != 'undefined') data.id = value;
			data[miniShop2.actionName] = 'order/getrequired';
			miniShop2.send(data, 'Order', 'getRequired');
		}
		,add: function(key, value) {
			var old_value = value;

			miniShop2.Order.callbacks.add = {
				response: {
					success: function(response) {
						var $field = $('[name="' + key + '"]', miniShop2.Order.order);
						switch (key) {
							case 'delivery':
								$field = $(miniShop2.Order.deliveryInputUniquePrefix + response.data[key]);
								if (response.data[key] != old_value) {
									$field.prop('checked', true).trigger('change');
								} else {
									miniShop2.Order.updatePayments($field.data('payments'));
									miniShop2.Order.getcost();
									miniShop2.Order.getRequired();
								}
								break;
							case 'payment':
								$field = $(miniShop2.Order.paymentInputUniquePrefix + response.data[key]);
								if (response.data[key] != old_value) {
									$field.prop('checked', true).trigger('change');
								}
								break;
							default:
								$field.val(response.data[key]).removeClass('error')
									.closest(miniShop2.Order.inputParent).removeClass('error');
						}
					}
					,error: function(response) {
						var $field = $('[name="' + key + '"]', miniShop2.Order.order);
						$field.val(response.data[key]).addClass('error')
							.closest(miniShop2.Order.inputParent).addClass('error');
					}
				}
			};

			var data = {
				key: key,
				value: value
			};
			data[miniShop2.actionName] = 'order/add';
			miniShop2.send(data, 'Order', 'add');
		}
		,getcost: function(cost) {

			miniShop2.Order.callbacks.getcost = {
				response: {
					success: function(response) {
						$(miniShop2.Order.orderCost, miniShop2.Order.order).text(miniShop2.Utils.formatPrice(response.data['cost']));
					}
				}
			};

			miniShop2.clearSendData();
			var data = {};
			data[miniShop2.actionName] = 'order/getcost';
			miniShop2.send(data, 'Order', 'getcost');
		}
		,clean: function() {
			miniShop2.Order.callbacks.clean = {
				response: {
					success: function(response) {
						document.location = document.location;
					}
				}
			};

			var data = {};
			data[miniShop2.actionName] = 'order/clean';
			miniShop2.send(data, 'Order', 'clean');
		}
		,submit: function() {
			miniShop2.Message.close();

			// Checking for active ajax request
			if (ajaxProgress) {
				miniShop2.$doc.ajaxComplete(function() {
					ajaxProgress = false;
					miniShop2.$doc.unbind('ajaxComplete');
					miniShop2.Order.submit();
				});
				return false;
			}

			miniShop2.Order.callbacks.submit = {
				before: function() {
					$(':button, a', miniShop2.Order.order).attr('disabled', true).prop('disabled', true);
				}
				,ajax: {
					always: function() {
						$(':button, a', miniShop2.Order.order).attr('disabled', false).prop('disabled', false);
					}
				}
				,response: {
					success: function(response) {
						if (response.data.redirect) {
							document.location.href = response.data.redirect;
						} else if (response.data.msorder) {
							document.location.href = /\?/.test(document.location.href) ? document.location.href + '&msorder=' + response.data.msorder : document.location.href + '?msorder=' + response.data.msorder;
						} else {
							document.location = document.location;
						}
					}
					,error: function(response) {
						$('[name]', miniShop2.Order.order).removeClass('error')
							.closest(miniShop2.Order.inputParent).removeClass('error');
						for (i in response.data) {
							var $field = $('[name="' + response.data[i] + '"]', miniShop2.Order.order);
							$field.addClass('error')
								.closest(miniShop2.Order.inputParent).addClass('error');
						}
					}
				}
			};
			miniShop2.send(miniShop2.sendData.formData, 'Order', 'submit');
		}
	};

	miniShop2.Message = {
		initialize: function() {
			if (typeof $.fn.jGrowl != 'undefined') {
				$.jGrowl.defaults.closerTemplate = '<div>[ ' + miniShop2Config.close_all_message + ' ]</div>';

				miniShop2.Message.close = function() {
					$.jGrowl('close');
				}
				miniShop2.Message.show = function(message, options) {
					if (!message) return;
					$.jGrowl(message, options);
				}
			} else {
				miniShop2.Message.close = function() {};
				miniShop2.Message.show = function(message) {
					if (!message) return;
					alert(message);
				}
			}
		}
		,success: function(message) {
			miniShop2.Message.show(message, {
				theme: 'ms2-message-success'
			});
		}
		,error: function(message) {
			miniShop2.Message.show(message, {
				theme: 'ms2-message-error',
				sticky: true
			});
		}
		,info: function(message) {
			miniShop2.Message.show(message, {
				theme: 'ms2-message-info'
			});
		}
	};

	miniShop2.Utils = {
		empty: function(val) {
			return (typeof(val) == 'undefined' || val == 0 || val === null || val === false || (typeof(val) == 'string' && val.replace(/\s+/g, '') == '') || (typeof(val) == 'array' && val.length == 0));
		}
		,formatPrice: function(price) {
			var pf = miniShop2Config.price_format;
			price = this.number_format(price, pf[0], pf[1], pf[2]);

			if (miniShop2Config.price_format_no_zeros) {
				price = price.replace(/(0+)$/, '');
				price = price.replace(/[^0-9]$/, '');
			}

			return price;
		}
		,formatWeight: function(weight) {
			var wf = miniShop2Config.weight_format;
			weight = this.number_format(weight, wf[0], wf[1], wf[2]);

			if (miniShop2Config.weight_format_no_zeros) {
				weight = weight.replace(/(0+)$/, '');
				weight = weight.replace(/[^0-9]$/, '');
			}

			return weight;
		}
		// Format a number with grouped thousands
		,number_format: function(number, decimals, dec_point, thousands_sep) {
			// original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
			// improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// bugfix by: Michael White (http://crestidg.com)
			var i, j, kw, kd, km;

			// input sanitation & defaults
			if (isNaN(decimals = Math.abs(decimals))) {
				decimals = 2;
			}
			if (dec_point == undefined) {
				dec_point = ",";
			}
			if (thousands_sep == undefined) {
				thousands_sep = ".";
			}

			i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

			if ((j = i.length) > 3) {
				j = j % 3;
			} else {
				j = 0;
			}

			km = (j ? i.substr(0, j) + thousands_sep : "");
			kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
			kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");

			return km + kw + kd;
		}
		,getValueFromSerializedArray: function(name, arr) {
			if (!$.isArray(arr)) {
				var arr = miniShop2.sendData.formData;
			}
			for (var i = 0, length = arr.length; i < length; i++) {
				if (arr[i].name == name) return arr[i].value;
			}
		}
		,getValueFromQS: function (key, str) {
			if (!key || !str) return null;
			if (str.indexOf('?') === 0) {
				str = str.substr(1, str.length-1);
			}
			var arr = str.split('&');
			for(var i=0,length=arr.length;i<length;i++) {
				var tmp = arr[i].split('=');
				if (tmp[0] == key) return tmp[1];
			}
		}
		,checkPropertyRecursive: function (str, obj) {
			var arr		= str.split('.');
			var prop	= arr.shift();
			if (typeof obj != 'undefined' && typeof obj[prop] != 'undefined' && !arr.length) {
				return obj[prop];
			} else if (arr.length) {
				return miniShop2.Utils.checkPropertyRecursive(arr.join('.'), obj[prop]);
			} else {
				return false;
			}
		}
	};

	$(document).ready(function($) {
		miniShop2.initialize();
	});
})(this, document, jQuery);