<div id="msMiniCart">

	<div class="empty" [[+total_count:isnot=`0`:then=`style="display:none"`:else=``]]>
		<h5><i class="icon-shopping-cart"></i> [[%ms2_minicart]]</h5>
		[[%ms2_minicart_is_empty]]
	</div>
	<div class="not_empty" [[+total_count:is=`0`:then=`style="display:none"`:else=``]]>
		<h5><i class="icon-shopping-cart"></i> [[%ms2_minicart]]</h5>
		[[%ms2_minicart_goods]]: <strong class="ms2_total_count">[[+total_count]]</strong> [[%ms2_frontend_count_unit]],
		[[%ms2_minicart_cost]]: <strong class="ms2_total_cost">[[+total_cost]]</strong> [[%ms2_frontend_currency]]
	</div>
</div>