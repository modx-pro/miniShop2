<form class="form-horizontal ms2_form" id="msOrder" method="post">
	<div class="row">
		<div class="span6 col-md-6">
			<h4>[[%ms2_frontend_credentials]]:</h4>
			<div class="form-group input-parent">
				<label class="col-sm-4 control-label" for="email"><span class="required-star">*</span> [[%ms2_frontend_email]]</label>
				<div class="col-sm-6">
					<input type="email" id="email" placeholder="[[%ms2_frontend_email]]" name="email" value="[[+email]]" class="form-control [[+errors.email]]">
				</div>
			</div>
			<div class="form-group input-parent">
				<label class="col-sm-4 control-label"  for="receiver"><span class="required-star">*</span> [[%ms2_frontend_receiver]]</label>
				<div class="col-sm-6">
					<input type="text" id="receiver" placeholder="[[%ms2_frontend_receiver]]" name="receiver" value="[[+receiver]]" class="form-control [[+errors.receiver]]">
				</div>
			</div>
			<div class="form-group input-parent">
				<label class="col-sm-4 control-label"  for="phone"><span class="required-star">*</span> [[%ms2_frontend_phone]]</label>
				<div class="col-sm-6">
					<input type="text" id="phone" placeholder="[[%ms2_frontend_phone]]" name="phone" value="[[+phone]]" class="form-control [[+errors.phone]]">
				</div>
			</div>
			<div class="form-group input-parent">
				<label class="col-sm-4 control-label"  for="comment"><span class="required-star">*</span> [[%ms2_frontend_comment]]</label>
				<div class="col-sm-6">
					<textarea name="comment" id="comment" placeholder="[[%ms2_frontend_comment]]" class="form-control [[+errors.comment]]">[[+comment]]</textarea>
				</div>
			</div>
		</div>
		<div class="span6 col-md-6" id="payments">
			<h4>[[%ms2_frontend_payments]]:</h4>
			<div class="form-group">
				<label class="col-sm-4 control-label" ><span class="required-star">*</span> [[%ms2_frontend_payment_select]]</label>
				<div class="col-sm-6">
					[[+payments]]
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span6 col-md-6" id="deliveries">
			<h4>[[%ms2_frontend_deliveries]]:</h4>
			<div class="form-group">
				<label class="col-sm-4 control-label" ><span class="required-star">*</span> [[%ms2_frontend_delivery_select]]</label>
				<div class="col-sm-6">
					[[+deliveries]]
				</div>
			</div>
		</div>
		<div class="span6 col-md-6">
			<h4>[[%ms2_frontend_address]]:</h4>
			<div class="form-group input-parent">
				<label class="col-sm-4 control-label" for="index"><span class="required-star">*</span> [[%ms2_frontend_index]]</label>
				<div class="col-sm-4">
					<input type="text" id="index" placeholder="[[%ms2_frontend_index]]"  name="index" value="[[+index]]" class="form-control [[+errors.index]]">
				</div>
			</div>
			<div class="form-group input-parent">
				<label class="col-sm-4 control-label"  for="region"><span class="required-star">*</span> [[%ms2_frontend_region]]</label>
				<div class="col-sm-6">
					<input type="text" id="region" placeholder="[[%ms2_frontend_region]]" name="region" value="[[+region]]" class="form-control [[+errors.region]]">
				</div>
			</div>
			<div class="form-group input-parent">
				<label class="col-sm-4 control-label"  for="city"><span class="required-star">*</span> [[%ms2_frontend_city]]</label>
				<div class="col-sm-6">
					<input type="text" id="city" placeholder="[[%ms2_frontend_city]]" name="city" value="[[+city]]" class="form-control [[+errors.city]]">
				</div>
			</div>
			<div class="form-group input-parent">
				<label class="col-sm-4 control-label"  for="street"><span class="required-star">*</span> [[%ms2_frontend_street]]</label>
				<div class="col-md-6">
					<div class="col-md-4">
						<input type="text" id="street" placeholder="[[%ms2_frontend_street]]" name="street" value="[[+street]]" class="form-control [[+errors.street]]">
					</div>
					<div class="col-md-4">
						<input type="text" id="building" placeholder="[[%ms2_frontend_building]]" name="building" value="[[+building]]" class="form-control [[+errors.building]]">
					</div>
					<div class="col-md-4">
						<input type="text" id="room" placeholder="[[%ms2_frontend_room]]" name="room" value="[[+room]]" class="form-control [[+errors.room]]">
					</div>
				</div>
			</div>
		</div>
	</div>
	<button type="button" name="ms2_action" value="order/clean" class="btn btn-default ms2_link"><i class="glyphicon glyphicon-remove"></i> [[%ms2_frontend_order_cancel]]</button>
	<hr/>
	<div class="well">
		<div class="col-md-offset-2">
			<h3>[[%ms2_frontend_order_cost]]: <span id="ms2_order_cost">[[+order_cost:default=`0`]]</span> [[%ms2_frontend_currency]]</h3>
			<button type="submit" name="ms2_action" value="order/submit" class="btn btn-default btn-primary ms2_link">[[%ms2_frontend_order_submit]]</button>
		</div>
	</div>
</form>