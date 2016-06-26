{foreach $options as $option}
    <div class="form-group">
        <label class="col-md-2 control-label">{$option.caption}:</label>
        <div class="col-md-10 form-control-static">
            {if $option.value is array}
                {var $values = ''}
                {foreach $option.value as $value}
                    {var $values = $values ~ $value ~ ', '}
                {/foreach}
                {$values | preg_replace : '#, $#': ''}
            {else}
                {$option.value}
            {/if}
        </div>
    </div>
{/foreach}
