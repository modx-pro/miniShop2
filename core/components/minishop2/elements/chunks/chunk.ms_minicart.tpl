<div id="msMiniCart" class="{$total_count > 0 ? 'full' : ''}">
    <div class="empty">
        <h5><i class="glyphicon glyphicon-shopping-cart"></i> {'ms2_minicart' | lexicon}</h5>
        {'ms2_minicart_is_empty' | lexicon}
    </div>
    <div class="not_empty">
        <h5><i class="glyphicon glyphicon-shopping-cart"></i> {'ms2_minicart' | lexicon}</h5>
        {'ms2_minicart_goods' | lexicon} <strong class="ms2_total_count">{$total_count}</strong> {'ms2_frontend_count_unit' | lexicon},
        {'ms2_minicart_cost' | lexicon} <strong class="ms2_total_cost">{$total_cost}</strong> {'ms2_frontend_currency' | lexicon}
    </div>
</div>