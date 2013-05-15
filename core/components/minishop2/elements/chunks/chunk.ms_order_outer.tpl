<div class="form-horizontal ms2_form" data-action="order/submit" id="msOrder">
	<div class="row">
		<div class="span6">
			<h4>[[%ms2_frontend_credentials]]:</h4>
			<div class="control-group">
				<label class="control-label" for="email">[[%ms2_frontend_email]]</label>
				<div class="controls">
					<input type="text" id="email" placeholder="[[%ms2_frontend_email]]" name="email" value="[[+email]]" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="receiver">[[%ms2_frontend_receiver]]</label>
				<div class="controls">
					<input type="text" id="receiver" placeholder="[[%ms2_frontend_receiver]]" name="receiver" value="[[+receiver]]" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="phone">[[%ms2_frontend_phone]]</label>
				<div class="controls">
					<input type="text" id="phone" placeholder="[[%ms2_frontend_phone]]" name="phone" value="[[+phone]]" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="comment">[[%ms2_frontend_comment]]</label>
				<div class="controls">
					<textarea name="comment" id="comment" placeholder="[[%ms2_frontend_comment]]">[[+comment]]</textarea>
				</div>
			</div>
		</div>
		<div class="span6" id="payments">
			<h4>[[%ms2_frontend_payments]]:</h4>
			<div class="control-group">
				<label class="control-label">[[%ms2_frontend_payment_select]]</label>
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
				<label class="control-label">[[%ms2_frontend_delivery_select]]</label>
				<div class="controls">
					[[+deliveries]]
				</div>
			</div>
		</div>
		<div class="span6">
			<h4>[[%ms2_frontend_address]]:</h4>
			<div class="control-group">
				<label class="control-label" for="index">[[%ms2_frontend_index]]</label>
				<div class="controls">
					<input type="text" id="index" placeholder="[[%ms2_frontend_index]]" class="span2" name="index" value="[[+index]]" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="region">[[%ms2_frontend_region]]</label>
				<div class="controls">
					<input type="text" id="region" placeholder="[[%ms2_frontend_region]]" name="region" value="[[+region]]" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="city">[[%ms2_frontend_city]]</label>
				<div class="controls">
					<input type="text" id="city" placeholder="[[%ms2_frontend_city]]" name="city" value="[[+city]]" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="street">[[%ms2_frontend_street]]</label>
				<div class="controls">
					<input type="text" id="street" placeholder="[[%ms2_frontend_street]]" class="span2" name="street" value="[[+street]]" />
					<input type="text" id="building" placeholder="[[%ms2_frontend_building]]" class="span1" name="building" value="[[+building]]" />
					<input type="text" id="room" placeholder="[[%ms2_frontend_room]]" class="span1" name="room" value="[[+room]]" />
				</div>
			</div>
		</div>
	</div>
	<a href="#" data-action="order/clean" class="ms2_link"><i class="icon-remove-sign"></i> [[%ms2_frontend_order_cancel]]</a>
	<hr/>
	<div class="form-actions">
		<h3>[[%ms2_frontend_order_cost]]: <span id="ms2_order_cost">[[+order_cost:default=`0`]]</span> [[%ms2_frontend_currency]]</h3>
		<button  class="btn btn-primary ms2_link" data-action="order/submit" id="orderSubmit">[[%ms2_frontend_order_submit]]</button>
	</div>
</div>