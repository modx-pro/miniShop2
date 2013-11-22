<tr id="[[+key]]">
	<td class="image"><img src="[[+thumb:default=`[[++assets_url]]components/minishop2/img/web/ms2_small.png`]]" /></td>
	<td class="title"><a href="[[~[[+id]]]]">[[+pagetitle]]</a><br/>
		<small><i>[[+option.color]] [[+option.size]]</i></small>
	</td>
	<td class="count">
		<form method="post" class="ms2_form form-inline" role="form">
			<input type="hidden" name="key" value="[[+key]]" />
			<div class="form-group">
				<input type="number" name="count" value="[[+count]]" max-legth="4" class="input-sm form-control" />
				[[%ms2_frontend_count_unit]]
				<button class="btn btn-default" type="submit" name="ms2_action" value="cart/change"><i class="glyphicon glyphicon-refresh"></i></button>
			</div>
		</form>
	</td>
	<td class="weight"><span>[[+weight]]</span> [[%ms2_frontend_weight_unit]]</td>
	<td class="price"><span>[[+price]]</span> [[%ms2_frontend_currency]][[+old_price]]</td>
	<td class="remove">
		<form method="post" class="ms2_form">
			<input type="hidden" name="key" value="[[+key]]">
			<button class="btn btn-default" type="submit" name="ms2_action" value="cart/remove" title="[[%ms2_cart_remove]]"><i class="glyphicon glyphicon-remove"></i></button>
		</form>
	</td>
</tr>
<!--minishop2_option.color [[%ms2_frontend_color]]: [[+option.color]];-->
<!--minishop2_option.size [[%ms2_frontend_size]]: [[+option.size]];-->
<!--minishop2_old_price <br/><span class="old_price">[[+old_price]] [[%ms2_frontend_currency]]</span>-->