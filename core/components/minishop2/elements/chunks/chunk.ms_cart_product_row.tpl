{var $image}
{if $thumb?}
    <img data-ms-product-thumb src="{$thumb}" alt="{$pagetitle}" title="{$pagetitle}"/>
{else}
    <img data-ms-product-thumb src="{'assets_url' | option}components/minishop2/img/web/ms2_small.png"
         srcset="{'assets_url' | option}components/minishop2/img/web/ms2_small@2x.png 2x"
         alt="{$pagetitle}" title="{$pagetitle}"/>
{/if}
{/var}
<div data-ms-product-id="{$key}" class="row justify-content-between py-3 product-row">
    <div class="col-lg-4 col-8 mb-lg-0 mb-3 d-flex">
        <div class="mw-100">
            {if $id?}
                <a href="{$id | url}">{$image}</a>
            {else}
                {$image}
            {/if}
        </div>
        <div class="ps-3 title">
            {if $id?}
                <a href="{$id | url}" data-ms-product-name>{$pagetitle}</a>
            {else}
                {$name}
            {/if}
            {if $options?}
                {foreach $options as $k => $v}
                    {set $optionKeys[] = $k}
                {/foreach}
                <form method="post" class="ms2_form" role="form">
                    <input type="hidden" name="key" value="{$key}"/>
                    <button type="submit" class="ms-hidden" name="ms2_action" value="cart/change"/>&nbsp;</button>
                    {'!msOptions' | snippet:[
                    'product' => $id,
                    'tpl' => 'tpl.msOptionsCart',
                    'options' => ($optionKeys | join: ','),
                    'selectedValues' => $options,
                    'cart_key' => $key
                    ]}
                    <label for="">
                        <input name="options[test2]" type="checkbox" value="1" {$options['test2'] ? 'checked' : ''} data-ms-product-options>
                        Вторая опция
                    </label>

                </form>
            {/if}
        </div>
    </div>
    <div class="col-lg-2 col-4 mb-lg-0 mb-3 d-flex align-items-center justify-content-center">
        <form method="post" class="ms2_form" role="form">
            <input type="hidden" name="key" value="{$key}"/>
            <button type="submit" class="ms-hidden" name="ms2_action" value="cart/change"/>&nbsp;</button>
            <div class="ms-input-number-wrap">
                <button class="ms-input-number-btn ms-input-number-minus btn btn-sm btn-secondary" type="button">&#8722;</button>
                <input class="ms-input-number-emulator" data-ms-product-count value="{$count}" name="count" type="text">
                <button class="ms-input-number-btn ms-input-number-plus btn btn-sm btn-secondary" type="button">&#43;</button>
            </div>
        </form>
    </div>
    <div class="col-lg-1 col-2 d-flex align-items-center justify-content-center">
        <span class="text-nowrap"><span data-ms-product-weight>{$weight}</span> {'ms2_frontend_weight_unit' | lexicon}</span>
    </div>
    <div class="col-lg-2 col-2 flex-column d-flex align-items-center justify-content-center">
        <span class="mr-2 text-nowrap"><span data-ms-product-price>{$price}</span> {'ms2_frontend_currency' | lexicon}</span>
        <span class="old_price text-nowrap text-decoration-line-through {!$old_price ? 'ms-hidden' : ''}" data-ms-fields-wrap>
                <span data-ms-product-old-price>{$old_price}</span> {'ms2_frontend_currency' | lexicon}
            </span>

        <span data-ms-fields-wrap class="{!$discount_price? 'ms-hidden' : ''}">
                Скидка:<br>
                <span data-ms-product-discount-price>{$discount_price}</span> {'ms2_frontend_currency' | lexicon}
                (<span data-ms-product-discount-percent>{$discount_percent}</span>%)
            </span>

    </div>
    <div class="col-lg-2 col-2 flex-column d-flex align-items-center justify-content-center">
        <span class="mr-2 text-nowrap"><span class="ms2_cost" data-ms-product-cost>{$cost}</span> {'ms2_frontend_currency' | lexicon}</span>
        <span data-ms-fields-wrap class="text-nowrap text-decoration-line-through {!$old_cost ? 'ms-hidden' : ''}">
                <span data-ms-product-old-cost>{$old_cost}</span> {'ms2_frontend_currency' | lexicon}
            </span>
        <span data-ms-fields-wrap class="{!$discount_cost ? 'ms-hidden' : ''}">
                Скидка:<br>
                <span data-ms-product-discount-cost>{$discount_cost}</span> {'ms2_frontend_currency' | lexicon}
            </span>
    </div>

    <div class="col-lg-1 col-2 d-flex align-items-center justify-content-center">
        <form method="post" class="ms2_form text-md-right">
            <input type="hidden" name="key" value="{$key}">
            <button class="btn btn-sm btn-danger" type="submit" name="ms2_action" value="cart/remove">&times;</button>
        </form>
    </div>
</div>
