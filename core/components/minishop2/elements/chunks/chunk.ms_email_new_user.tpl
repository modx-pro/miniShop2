[[!msGetOrder?id=`[[+id]]`]]

<h3>[[%ms2_email_subject_new_user]]</h3>

<div id="msCart">
	<table class="table table-striped">
		<tr class="header">
			<th class="image span2 col-md-2">&nbsp;</th>
			<th class="title span4 col-md-4">[[%ms2_cart_title]]</th>
			<th class="count span2 col-md-2">[[%ms2_cart_count]]</th>
			<th class="weight span1 col-md-1">[[%ms2_cart_weight]]</th>
			<th class="price span1 col-md-1">[[%ms2_cart_cost]]</th>
		</tr>
		[[+goods]]
		<tr class="footer">
			<th class="total" colspan="2">[[%ms2_cart_total]]:</th>
			<th class="total_count"><span class="ms2_total_count">[[+cart_count]]</span> [[%ms2_frontend_count_unit]]</th>
			<th class="total_weight"><span class="ms2_total_weight">[[+cart_weight]]</span> [[%ms2_frontend_weight_unit]]</th>
			<th class="total_cost"><span class="ms2_total_cost">[[+cart_cost]]</span> [[%ms2_frontend_currency]]</th>
		</tr>
	</table>
	<h4>[[%ms2_frontend_order_cost]]: [[+cart_cost]] [[%ms2_frontend_currency]] + [[+delivery_cost]] [[%ms2_frontend_currency]] = <big>[[+cost]]</big> [[%ms2_frontend_currency]]</h4>
</div>

[[+payment_link]]