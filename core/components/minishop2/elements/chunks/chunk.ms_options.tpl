{foreach $options as $name => $values}
    <div class="form-group">
        <label class="col-md-2 control-label" for="option_{$name}">{('ms2_product_' ~ $name) | lexicon}:</label>
        <div class="col-md-10">
            <select name="options[{$name}]" class="input-sm form-control" id="option_{$name}">
                {foreach $values as $value}
                    <option value="{$value}">{$value}</option>
                {/foreach}
            </select>
        </div>
    </div>
{/foreach}