<div id="msCart">
	<table class="table table-striped">
		<tr class="header">
			<th class="image span2">&nbsp;</th>
			<th class="title span4">[[%ms2_cart_title]]</th>
			<th class="count span2">[[%ms2_cart_count]]</th>
			<th class="weight span1">[[%ms2_cart_weight]]</th>
			<th class="price span1">[[%ms2_cart_price]]</th>
			<th class="remove span2">[[%ms2_cart_remove]]</th>
		</tr>
		[[+goods]]
		<tr class="footer">
			<th class="total" colspan="2">[[%ms2_cart_total]]:</th>
			<th class="total_count"><span class="ms2_total_count">[[+total_count]]</span> [[%ms2_frontend_count_unit]]</th>
			<th class="total_weight"><span class="ms2_total_weight">[[+total_weight]]</span> [[%ms2_frontend_weight_unit]]</th>
			<th class="total_cost"><span class="ms2_total_cost">[[+total_cost]]</span> [[%ms2_frontend_currency]]</th>
			<th>&nbsp;</th>
		</tr>
	</table>
	<a href="#" data-action="cart/clean" class="ms2_link"><i class="icon-remove-sign"></i> [[%ms2_cart_clean]]</a>
</div>