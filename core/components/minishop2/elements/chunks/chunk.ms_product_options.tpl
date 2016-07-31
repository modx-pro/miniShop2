{foreach $options as $option}
    <div class="form-group">
        <label class="col-md-2 control-label">{$option.caption}:</label>
        <div class="col-md-10 form-control-static">
            {if $option.value is array}
                {$option.value | join : ', '}
            {else}
                {$option.value}
            {/if}
        </div>
    </div>
{/foreach}
