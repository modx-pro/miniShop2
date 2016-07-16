{extends 'tpl.msEmail'}

{block 'title'}
    {'ms2_email_subject_sent_user' | lexicon : $order}
{/block}