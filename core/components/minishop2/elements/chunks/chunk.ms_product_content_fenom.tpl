<h1 class="text-center text-md-left">{$_modx->resource.pagetitle}</h1>
<div class="text-center text-md-left mb-2 mb-md-0">
    {if $_modx->resource.new?}
        <span class="badge badge-secondary badge-pill col-auto">{'ms2_frontend_new' | lexicon}</span>
    {/if}
    {if $_modx->resource.popular?}
        <span class="badge badge-secondary badge-pill col-auto">{'ms2_frontend_popular' | lexicon}</span>
    {/if}
    {if $_modx->resource.favorite?}
        <span class="badge badge-secondary badge-pill col-auto">{'ms2_frontend_favorite' | lexicon}</span>
    {/if}
</div>
<div id="msProduct" class="row align-items-center" itemtype="http://schema.org/Product" itemscope>
    <meta itemprop="name" content="{$_modx->resource.pagetitle}">
    <meta itemprop="description" content="{$_modx->resource.description ?: $_modx->resource.pagetitle}">
    <div class="col-12 col-md-6">
        {'!msGallery' | snippet : []}
    </div>
    <div class="col-12 col-md-6" itemtype="http://schema.org/AggregateOffer" itemprop="offers" itemscope>
        <meta itemprop="category" content="{$_modx->resource.parent | resource: "pagetitle"}">
        <meta itemprop="offerCount" content="1">
        <meta itemprop="price" content="{$price | replace:" ":""}">
        <meta itemprop="lowPrice" content="{$price | replace:" ":""}">
        <meta itemprop="priceCurrency" content="RUR">

        <form class="form-horizontal ms2_form" method="post">
            <input type="hidden" name="id" value="{$_modx->resource.id}"/>

            <div class="form-group row align-items-center">
                <label class="col-6 col-md-3 text-right text-md-left col-form-label">{'ms2_product_article' | lexicon}:</label>
                <div class="col-6 col-md-9">
                    {$article ?: '-'}
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label class="col-6 col-md-3 text-right text-md-left col-form-label">{'ms2_product_price' | lexicon}:</label>
                <div class="col-6 col-md-9">
                    {$price} {'ms2_frontend_currency' | lexicon}
                    {if $old_price != 0}
                    <span class="old_price ml-2">{$old_price} {'ms2_frontend_currency' | lexicon}</span>
                    {/if}
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label class="col-6 col-md-3 text-right text-md-left col-form-label" for="product_price">{'ms2_cart_count' | lexicon}:</label>
                <div class="col-6 col-md-9">
                    <div class="input-group">
                        <input type="number" name="count" id="product_price" class="form-control col-md-6" value="1"/>
                        <div class="input-group-append">
                            <span class="input-group-text">{'ms2_frontend_count_unit' | lexicon}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label class="col-6 col-md-3 text-right text-md-left col-form-label">{'ms2_product_weight' | lexicon}:</label>
                <div class="col-6 col-md-9">
                    {$weight} {'ms2_frontend_weight_unit' | lexicon}
                </div>
            </div>

            <div class="form-group row align-items-center">
                <label class="col-6 col-md-3 text-right text-md-left col-form-label">{'ms2_product_made_in' | lexicon}:</label>
                <div class="col-6 col-md-9">
                    {$made_in ?: '-'}
                </div>
            </div>

            {'msOptions' | snippet : [
                'options' => 'color,size'
            ]}

            {'msProductOptions' | snippet : []}

            <div class="form-group row align-items-center">
                <div class="col-12 offset-md-3 col-md-9 text-center text-md-left">
                    <button type="submit" class="btn btn-primary" name="ms2_action" value="cart/add">
                        {'ms2_frontend_add_to_cart' | lexicon}
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
<div class="mt-3">
    {$_modx->resource.content}
</div>
