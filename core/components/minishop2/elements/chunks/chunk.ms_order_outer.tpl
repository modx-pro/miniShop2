<form class="form-horizontal ms2_form" id="msOrder" method="post">
	<div class="row">
		<div class="span6">
			<h4>[[%ms2_frontend_credentials]]:</h4>
			<div class="control-group input-parent [[+errors.email]]">
				<label class="control-label" for="email"><span class="required-star">*</span> [[%ms2_frontend_email]]</label>
				<div class="controls">
					<input type="email" id="email" placeholder="[[%ms2_frontend_email]]" name="email" value="[[+email]]" class="[[+errors.email]]">
				</div>
			</div>
			<div class="control-group input-parent [[+errors.receiver]]">
				<label class="control-label" for="receiver"><span class="required-star">*</span> [[%ms2_frontend_receiver]]</label>
				<div class="controls">
					<input type="text" id="receiver" placeholder="[[%ms2_frontend_receiver]]" name="receiver" value="[[+receiver]]" class="[[+errors.receiver]]">
				</div>
			</div>
			<div class="control-group input-parent [[+errors.phone]]">
				<label class="control-label" for="phone"><span class="required-star">*</span> [[%ms2_frontend_phone]]</label>
				<div class="controls">
					<input type="text" id="phone" placeholder="[[%ms2_frontend_phone]]" name="phone" value="[[+phone]]" class="[[+errors.phone]]">
				</div>
			</div>
			<div class="control-group input-parent [[+errors.comment]]">
				<label class="control-label" for="comment"><span class="required-star">*</span> [[%ms2_frontend_comment]]</label>
				<div class="controls">
					<textarea name="comment" id="comment" placeholder="[[%ms2_frontend_comment]]" class="[[+errors.comment]]">[[+comment]]</textarea>
				</div>
			</div>
		</div>
		<div class="span6" id="payments">
			<h4>[[%ms2_frontend_payments]]:</h4>
			<div class="control-group">
				<label class="control-label"><span class="required-star">*</span> [[%ms2_frontend_payment_select]]</label>
				<div class="controls">
					[[+payments]]
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span6" id="deliveries">
			<h4>[[%ms2_frontend_deliveries]]:</h4>
			<div class="control-group">
				<label class="control-label"><span class="required-star">*</span> [[%ms2_frontend_delivery_select]]</label>
				<div class="controls">
					[[+deliveries]]
				</div>
			</div>
		</div>
		<div class="span6">
			<h4>[[%ms2_frontend_address]]:</h4>
			<div class="control-group input-parent [[+errors.index]]">
				<label class="control-label" for="index"><span class="required-star">*</span> [[%ms2_frontend_index]]</label>
				<div class="controls">
					<input type="text" id="index" placeholder="[[%ms2_frontend_index]]" name="index" value="[[+index]]" class="[[+errors.index]]">
				</div>
			</div>
			<div class="control-group input-parent [[+errors.region]]">
				<label class="control-label" for="region"><span class="required-star">*</span> [[%ms2_frontend_region]]</label>
				<div class="controls">
					<input type="text" id="region" placeholder="[[%ms2_frontend_region]]" name="region" value="[[+region]]" class="[[+errors.region]]">
				</div>
			</div>
			<div class="control-group input-parent [[+errors.city]]">
				<label class="control-label" for="city"><span class="required-star">*</span> [[%ms2_frontend_city]]</label>
				<div class="controls">
					<input type="text" id="city" placeholder="[[%ms2_frontend_city]]" name="city" value="[[+city]]" class="[[+errors.city]]">
				</div>
			</div>
			<div class="control-group input-parent [[+errors.street]]">
				<label class="control-label" for="street"><span class="required-star">*</span> [[%ms2_frontend_street]]</label>
				<div class="controls">
					<input type="text" id="street" placeholder="[[%ms2_frontend_street]]" name="street" value="[[+street]]" class="[[+errors.street]]">
				</div>
			</div>
			<div class="control-group input-parent [[+errors.building]]">
				<label class="control-label" for="building"><span class="required-star">*</span> [[%ms2_frontend_building]]</label>
				<div class="controls">
					<input type="text" id="building" placeholder="[[%ms2_frontend_building]]" name="building" value="[[+building]]" class="span1 [[+errors.building]]">
				</div>
			</div>
			<div class="control-group input-parent [[+errors.room]]">
				<label class="control-label" for="room"><span class="required-star">*</span> [[%ms2_frontend_room]]</label>
				<div class="controls">
					<input type="text" id="room" placeholder="[[%ms2_frontend_room]]" name="room" value="[[+room]]" class="span1 [[+errors.room]]">
				</div>
			</div>
		</div>
	</div>
	<button type="button" name="ms2_action" value="order/clean" class="btn ms2_link" id="orderSubmit"><i class="icon-remove-sign"></i> [[%ms2_frontend_order_cancel]]</button>
	<!-- <a href="#" data-action="order/clean" class="ms2_link"><i class="icon-remove-sign"></i> [[%ms2_frontend_order_cancel]]</a> -->
	<hr/>
	<div class="form-actions">
		<h3>[[%ms2_frontend_order_cost]]: <span id="ms2_order_cost">[[+order_cost:default=`0`]]</span> [[%ms2_frontend_currency]]</h3>
		<button type="submit" name="ms2_action" value="order/submit" class="btn btn-primary ms2_link" id="orderSubmit">[[%ms2_frontend_order_submit]]</button>
	</div>
</form>