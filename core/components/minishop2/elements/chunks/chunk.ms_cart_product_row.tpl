{var $image}
{if $thumb?}
    <img src="{$thumb}" alt="{$pagetitle}" title="{$pagetitle}"/>
{else}
    <img src="{'assets_url' | option}components/minishop2/img/web/ms2_small.png"
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
                <a href="{$id | url}">{$pagetitle}</a>
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
                    {'!msOptions' | snippet:['product' => $id, 'options' => ($optionKeys | join: ','), 'selectedValues' => $options]}
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
        <span class="text-nowrap">{$weight} {'ms2_frontend_weight_unit' | lexicon}</span>
    </div>
    <div class="col-lg-2 col-2 flex-column d-flex align-items-center justify-content-center">
        <span class="mr-2 text-nowrap">{$price} {'ms2_frontend_currency' | lexicon}</span>
        {if $old_price?}
            <span class="old_price text-nowrap text-decoration-line-through">{$old_price} {'ms2_frontend_currency' | lexicon}</span>
        {/if}
    </div>
    <div class="col-lg-2 col-2 d-flex align-items-center justify-content-center">
        <span class="mr-2 text-nowrap"><span class="ms2_cost" data-ms-product-cost>{$cost}</span> {'ms2_frontend_currency' | lexicon}</span>
    </div>

    <div class="col-lg-1 col-2 d-flex align-items-center justify-content-center">
        <form method="post" class="ms2_form text-md-right">
            <input type="hidden" name="key" value="{$key}">
            <button class="btn btn-sm btn-danger" type="submit" name="ms2_action" value="cart/remove">&times;</button>
        </form>
    </div>
</div>
