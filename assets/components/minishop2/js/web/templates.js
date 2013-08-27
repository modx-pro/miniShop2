miniShop2Config.callbacksObjectTemplate = function() {
	return {
		before: function() {
			// return false to prevent send data
		},
		response: {
			success: function(response) {},
			error: function(response) {}
		},
		ajax: {
			done: function(xhr) {},
			fail: function(xhr) {},
			always: function(xhr) {}
		}
	};
};
// Define user Callbacks template
miniShop2.Callbacks = miniShop2Config.Callbacks = {
	Cart: {
		add: miniShop2Config.callbacksObjectTemplate(),
		remove: miniShop2Config.callbacksObjectTemplate(),
		change: miniShop2Config.callbacksObjectTemplate(),
		clean: miniShop2Config.callbacksObjectTemplate()
	}
	,Order: {
		add: miniShop2Config.callbacksObjectTemplate(),
		getcost: miniShop2Config.callbacksObjectTemplate(),
		clean: miniShop2Config.callbacksObjectTemplate(),
		submit: miniShop2Config.callbacksObjectTemplate(),
		getRequired: miniShop2Config.callbacksObjectTemplate()
	}
};