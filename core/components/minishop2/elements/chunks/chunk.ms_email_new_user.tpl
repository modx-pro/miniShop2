{extends 'tpl.msEmail'}

{block 'title'}
    {'ms2_email_subject_new_user' | lexicon : $order}
{/block}

{block 'products'}
    {parent}
    {if $payment_link?}
        <p style="margin-left:20px;{$style.p}">
            {'ms2_payment_link' | lexicon : ['link' => $payment_link]}
        </p>
    {/if}
{/block}