<div data-ms-cart="">
    <p> Это другой чанк корзины </p>
    <div data-ms-cart-empty="" class="container alert alert-warning {($products | length == 0) ? '' : 'ms-hidden'}">
        {'ms2_cart_is_empty' | lexicon}
    </div>
    <div data-ms-cart-full="" class="container {($products | length == 0) ? 'ms-hidden' : ''}">
        <div class="row py-3 cart-header align-items-center d-lg-flex d-none">
            <div class="col-lg-4">{'ms2_cart_title' | lexicon}</div>
            <div class="col-lg-2">{'ms2_cart_count' | lexicon}</div>
            <div class="col-lg-1">{'ms2_cart_weight' | lexicon}</div>
            <div class="col-lg-2">{'ms2_cart_price' | lexicon}</div>
            <div class="col-lg-2">{'ms2_cart_cost' | lexicon}</div>
            <div class="col-lg-1"></div>
        </div>
        <div data-ms-cart-products="{$scriptProperties.tplKey}">
            {$products}
        </div>
        <div class="row py-3 cart-header align-items-center">
            <div class="col-lg-4">{'ms2_cart_total' | lexicon}:</div>
            <div class="col-lg-2 d-flex align-items-center justify-content-center">
                <div>
                    <span class="ms2_total_count" data-ms-cart-count="">{$total.count}</span>
                    {'ms2_frontend_count_unit' | lexicon}
                </div>
            </div>
            <div class="col-lg-1 d-flex align-items-center justify-content-center">
                <div>
                    <span class="ms2_total_weight" data-ms-cart-weight>{$total.weight}</span>
                    {'ms2_frontend_weight_unit' | lexicon}
                </div>
            </div>
            <div class="col-lg-2"></div>
            <div class="col-lg-2 d-flex align-items-center justify-content-center flex-column">
                <div>
                    <span class="ms2_total_cost" data-ms-cart-cost>{$total.cost}</span>
                    {'ms2_frontend_currency' | lexicon}
                </div>

                <div class="old-price">
                    <span data-ms-cart-discount>{$total.discount}</span>
                    {'ms2_frontend_currency' | lexicon}
                </div>
            </div>
            <div class="col-lg-1"></div>
        </div>

        <form method="post" class="row ms2_form my-3">
            <button type="submit" name="ms2_action" value="cart/clean" class="col-auto btn btn-danger">
                {'ms2_cart_clean' | lexicon}
            </button>
        </form>
    </div>
</div>
