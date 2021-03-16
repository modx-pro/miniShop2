<h1 class="text-center text-md-left">[[*pagetitle]]</h1>
<div class="text-center text-md-left mb-2 mb-md-0">
    [[+new:isnot=``:then=`<span class="badge badge-secondary badge-pill col-auto">[[%ms2_frontend_new]]</span>`]]
    [[+popular:isnot=``:then=`<span class="badge badge-secondary badge-pill col-auto">[[%ms2_frontend_popular]]</span>`]]
    [[+favorite:isnot=``:then=`<span class="badge badge-secondary badge-pill col-auto">[[%ms2_frontend_favorite]]</span>`]]
</div>
<div id="msProduct" class="row align-items-center" itemtype="http://schema.org/Product" itemscope>
    <meta itemprop="name" content="[[*pagetitle]]">
    <meta itemprop="description" content="[[*description:default=`[[*pagetitle]]`]]">
    <div class="col-12 col-md-6">
        [[!msGallery]]
    </div>
    <div class="col-12 col-md-6" itemtype="http://schema.org/AggregateOffer" itemprop="offers" itemscope>
        <meta itemprop="category" content="[[#[[*parent]].pagetitle]]">
        <meta itemprop="offerCount" content="1">
        <meta itemprop="price" content="[[+price:replace=` ==`]]">
        <meta itemprop="lowPrice" content="[[+price:replace=` ==`]]">
        <meta itemprop="priceCurrency" content="RUR">

        <form class="form-horizontal ms2_form" method="post">
            <input type="hidden" name="id" value="[[*id]]"/>

            <div class="form-group row align-items-center">
                <label class="col-6 col-md-3 text-right text-md-left col-form-label">[[%ms2_product_article]]:</label>
                <div class="col-6 col-md-9">
                    [[+article:default=`-`]]
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label class="col-6 col-md-3 text-right text-md-left col-form-label">[[%ms2_product_price]]:</label>
                <div class="col-6 col-md-9">
                    [[+price]] [[%ms2_frontend_currency]]
                    [[+old_price:gt=`0`:then=`
                    <span class="old_price ml-2">[[+old_price]] [[%ms2_frontend_currency]]</span>
                    `:else=``]]
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label class="col-6 col-md-3 text-right text-md-left col-form-label" for="product_price">[[%ms2_cart_count]]:</label>
                <div class="col-6 col-md-9">
                    <div class="input-group">
                        <input type="number" name="count" id="product_price" class="form-control col-md-6" value="1"/>
                        <div class="input-group-append">
                            <span class="input-group-text">[[%ms2_frontend_count_unit]]</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label class="col-6 col-md-3 text-right text-md-left col-form-label">[[%ms2_product_weight]]:</label>
                <div class="col-6 col-md-9">
                    [[+weight]] [[%ms2_frontend_weight_unit]]
                </div>
            </div>

            <div class="form-group row align-items-center">
                <label class="col-6 col-md-3 text-right text-md-left col-form-label">[[%ms2_product_made_in]]:</label>
                <div class="col-6 col-md-9">
                    [[+made_in:default=`-`]]
                </div>
            </div>

            [[msOptions?options=`color,size`]]

            [[msProductOptions]]

            <div class="form-group row align-items-center">
                <div class="col-12 offset-md-3 col-md-9 text-center text-md-left">
                    <button type="submit" class="btn btn-primary" name="ms2_action" value="cart/add">
                        [[%ms2_frontend_add_to_cart]]
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
<div class="mt-3">
    [[*content]]
</div>
