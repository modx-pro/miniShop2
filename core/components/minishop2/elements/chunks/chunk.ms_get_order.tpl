<div id="msCart">
    <div class="table-responsive">
        <table class="table table-striped">
            <tr class="header">
                <th class="image col-md-2">&nbsp;</th>
                <th class="title col-md-4">{'ms2_cart_title' | lexicon}</th>
                <th class="count col-md-2">{'ms2_cart_count' | lexicon}</th>
                <th class="weight col-md-1">{'ms2_cart_weight' | lexicon}</th>
                <th class="price col-md-1">{'ms2_cart_cost' | lexicon}</th>
            </tr>
            {foreach $products as $product}
                <tr>
                    <td class="image">
                        {if $product.thumb?}
                            <img src="{$product.thumb}" alt="{$product.pagetitle}" title="{$product.pagetitle}"/>
                        {else}
                            <img src="{'assets_url' | option}components/minishop2/img/web/ms2_small.png"
                                 srcset="{'assets_url' | option}components/minishop2/img/web/ms2_small@2x.png 2x"
                                 alt="{$product.pagetitle}" title="{$product.pagetitle}"/>
                        {/if}
                    </td>
                    <td class="title">
                        {if $product.id?}
                            <a href="{$product.id | url}">{$product.name}</a>
                        {else}
                            {$product.name}
                        {/if}
                        {if $product.options?}
                            <div class="small">
                                {$product.options | join : '; '}
                            </div>
                        {/if}
                    </td>
                    <td class="count">{$product.count} {'ms2_frontend_count_unit' | lexicon}</td>
                    <td class="weight">{$product.weight} {'ms2_frontend_weight_unit' | lexicon}</td>
                    <td class="price">{$product.price} {'ms2_frontend_currency' | lexicon}</td>
                </tr>
            {/foreach}
            <tr class="footer">
                <th class="total" colspan="2">{'ms2_cart_total' | lexicon}:</th>
                <th class="total_count">
                    <span class="ms2_total_count">{$total.cart_count}</span> {'ms2_frontend_count_unit' | lexicon}
                </th>
                <th class="total_weight">
                    <span class="ms2_total_weight">{$total.cart_weight}</span> {'ms2_frontend_weight_unit' | lexicon}
                </th>
                <th class="total_cost">
                    <span class="ms2_total_cost">{$total.cart_cost}</span> {'ms2_frontend_currency' | lexicon}
                </th>
            </tr>
        </table>
    </div>

    <h4>
        {'ms2_frontend_order_cost' | lexicon}:
        {if $total.delivery_cost}
            {$total.cart_cost} {'ms2_frontend_currency' | lexicon} + {$total.delivery_cost}
            {'ms2_frontend_currency' | lexicon} =
        {/if}
        <strong>{$total.cost}</strong> {'ms2_frontend_currency' | lexicon}
    </h4>
</div>
