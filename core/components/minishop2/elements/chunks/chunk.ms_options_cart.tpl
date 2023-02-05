{foreach $options as $name => $values}
    <div class="form-group row align-items-center">
        <label class="col-6 col-md-3 text-right text-md-left col-form-label" for="option_{$name}_{$scriptProperties.cart_key}">{('ms2_product_' ~ $name) | lexicon}:</label>
        <div class="col-6 col-md-9">
            <select name="options[{$name}]" data-ms-product-options class="form-control col-md-6" id="option_{$name}_{$scriptProperties.cart_key}">
                {foreach $values as $value}
                    <option value="{$value}" {$scriptProperties.selectedValues[$name] == $value  ? 'selected' : ''}>{$value}</option>
                {/foreach}
            </select>
        </div>
    </div>
{/foreach}
