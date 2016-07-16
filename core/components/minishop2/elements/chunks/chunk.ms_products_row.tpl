<div class="row ms2_product">
    <div class="col-md-2">
        {if $thumb?}
            <img src="{$thumb}" alt="{$pagetitle}" title="{$pagetitle}"/>
        {else}
            <img src="{'assets_url' | option}components/minishop2/img/web/ms2_small.png"
                 srcset="{'assets_url' | option}components/minishop2/img/web/ms2_small@2x.png 2x"
                 alt="{$pagetitle}" title="{$pagetitle}"/>
        {/if}
    </div>
    <div class="col-md-10">
        <form method="post" class="ms2_form">
            <a href="{$id | url}">{$pagetitle}</a>
            <span class="flags">
                {if $new?}
                    <i class="glyphicon glyphicon-flag" title="{'ms2_frontend_new' | lexicon}"></i>
                {/if}
                {if $popular?}
                    <i class="glyphicon glyphicon-star" title="{'ms2_frontend_popular' | lexicon}"></i>
                {/if}
                {if $favorite?}
                    <i class="glyphicon glyphicon-bookmark" title="{'ms2_frontend_favorite' | lexicon}"></i>
                {/if}
            </span>
            <span class="price">
                {$price} {'ms2_frontend_currency' | lexicon}
            </span>
            {if $old_price?}
                <span class="old_price">{$old_price} {'ms2_frontend_currency' | lexicon}</span>
            {/if}
            <button class="btn btn-default pull-right" type="submit" name="ms2_action" value="cart/add">
                <i class="glyphicon glyphicon-barcode"></i> {'ms2_frontend_add_to_cart' | lexicon}
            </button>
            <input type="hidden" name="id" value="{$id}">
            <input type="hidden" name="count" value="1">
            <input type="hidden" name="options" value="[]">
        </form>
        {if $introtext}
            <p>
                <small>{$introtext}</small>
            </p>
        {/if}
    </div>
</div>