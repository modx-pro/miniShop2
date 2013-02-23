<h1>[[*pagetitle]]</h1>
<div id="msProduct" class="row">
	<div class="span5">
		[[!msGallery]]
	</div>
	<div class="span7">
		<form class="form-horizontal ms2_form" method="post" data-action="cart/add">
			<input type="hidden" name="id" value="[[*id]]" />
			<div class="control-group">
				<label class="control-label">[[%ms2_product_article]]:</label>
				<div class="controls">
					<label class="checkbox">
						[[+article]]
					</label>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">[[%ms2_product_price]]:</label>
				<div class="controls">
					<label class="checkbox">
						[[+price]] [[%ms2_frontend_currency]]
						[[+old_price:gt=`0`:then=`<span class="old_price">[[+old_price]] [[%ms2_frontend_currency]]</span>`:else=``]]
					</label>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="product_price">[[%ms2_cart_count]]:</label>
				<div class="controls">
					<input type="number" name="count" id="product_price" class="input-mini" value="1" /> [[%ms2_frontend_count_unit]]
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">[[%ms2_product_weight]]:</label>
				<div class="controls">
					<label class="checkbox">[[+weight]] [[%ms2_frontend_weight_unit]]</label>
				</div>
			</div>
			[[!msOptions?name=`color`]]
			[[!msOptions?name=`size`]]
			[[-!msOptions?name=`tags`&tplRow=``&tplOuter=``]]
			<div class="control-group">
				<label class="control-label">[[%ms2_product_made_in]]:</label>
				<div class="controls">
					<label class="checkbox">[[+made_in]]</label>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn"><i class="icon-barcode"></i> [[%ms2_frontend_add_to_cart]]</button>
				</div>
			</div>
		</form>

	</div>
</div>
[[*content]]
