{extends 'tpl.msEmail'}

{block 'title'}
    {'ms2_email_subject_cancelled_user' | lexicon : $order}
{/block}