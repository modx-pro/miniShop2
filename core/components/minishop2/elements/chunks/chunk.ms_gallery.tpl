<div id="msGallery">
    {if $files}
        <div>
            {foreach $files as $file}
                <a href="{$file['url']}" target="_blank">
                    <img src="{$file['small']}" alt="{$file['description']}" title="{$file['name']}">
                </a>
            {/foreach}
        </div>
    {else}
        <img src="{('assets_url' | option) ~ 'components/minishop2/img/web/ms2_medium.png'}"
            srcset="{('assets_url' | option) ~ 'components/minishop2/img/web/ms2_medium@2x.png'} 2x"
            alt="" title=""/>
    {/if}
</div>
