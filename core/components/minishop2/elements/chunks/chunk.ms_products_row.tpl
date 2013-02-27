<div class="row ms2_product">
	<div class="span2"><img src="[[+thumb:default=`[[++assets_url]]components/minishop2/img/web/ms2_small.png`]]" width="120" height="90" /></div>
	<div class="row span10">
		<p><a href="[[~[[+id]]]]">[[+pagetitle]]</a>
			<span class="flags">[[+new]] [[+popular]] [[+favorite]]</span>
			<span class="price">[[+price]] [[%ms2_frontend_currency]]</span>
			[[+old_price:gt=`0`:then=`<span class="old_price">[[+old_price]] [[%ms2_frontend_currency]]</span>`:else=``]]
			<a href="#" class="ms2_link" data-action="cart/add" data-id="[[+id]]" data-count="1" data-options='[]'><i class="icon-barcode"></i> [[%ms2_frontend_add_to_cart]]</a>
		</p>
		<p><small>[[+introtext]]</small></p>
		[[+tags:notempty=`<span class="tags">[[%ms2_frontend_tags]]: [[+tags]];</span>`]]
		[[+color:notempty=`<span class="color">[[%ms2_frontend_colors]]: [[+color]];</span>`]]
		[[+size:notempty=`<span class="size">[[%ms2_frontend_sizes]]: [[+size]];</span>`]]
	</div>
</div>
<br/><br/>
<!--minishop2_tags , [[+value]]-->
<!--minishop2_color , [[+value]]-->
<!--minishop2_size , [[+value]]-->
<!--minishop2_popular <i class="icon-star" title="[[%ms2_frontend_popular]]"></i>-->
<!--minishop2_new <i class="icon-flag" title="[[%ms2_frontend_new]]"></i>-->
<!--minishop2_favorite <i class="icon-bookmark" title="[[%ms2_frontend_favorite]]"></i>-->
