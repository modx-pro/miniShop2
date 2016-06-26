<form class="form-horizontal ms2_form" id="msOrder" method="post">
    <div class="row">
        <div class="col-md-6">
            <h4>{'ms2_frontend_credentials' | lexicon}:</h4>
            {foreach ['email','receiver','phone'] as $field}
                <div class="form-group input-parent">
                    <label class="col-md-4 control-label" for="{$field}">
                        <span class="required-star">*</span> {('ms2_frontend_' ~ $field) | lexicon}
                    </label>
                    <div class="col-sm-6">
                        <input type="text" id="{$field}" placeholder="{('ms2_frontend_' ~ $field) | lexicon}"
                               name="{$field}" value="{$form[$field]}"
                               class="form-control{($field in list $errors) ? ' error' : ''}">
                    </div>
                </div>
            {/foreach}

            <div class="form-group input-parent">
                <label class="col-md-4 control-label" for="comment">
                    <span class="required-star">*</span> {'ms2_frontend_comment' | lexicon}</label>
                <div class="col-sm-6">
                    <textarea name="comment" id="comment" placeholder="{'ms2_frontend_comment' | lexicon}"
                              class="form-control{('comment' in list $errors) ? ' error' : ''}">{$form.comment}</textarea>
                </div>
            </div>
        </div>

        <div class="col-md-6" id="payments">
            <h4>{'ms2_frontend_payments' | lexicon}:</h4>
            <div class="form-group">
                <label class="col-md-4 control-label"><span class="required-star">*</span>
                    {'ms2_frontend_payment_select' | lexicon}</label>
                <div class="col-sm-6">
                    {foreach $payments as $payment}
                        <div class="checkbox">
                            <label class="payment input-parent">
                                <input type="radio" name="payment" value="{$payment.id}" id="payment_{$payment.id}"
                                        {$payment.id == $order.payment ? 'checked' : ''}>
                                {if $payment.logo?}
                                    <img src="{$payment.logo}" alt="{$payment.name}" title="{$payment.name}"/>
                                {else}
                                    {$payment.name}
                                {/if}
                                {if $payment.description?}
                                    <p class="small">
                                        {$payment.description}
                                    </p>
                                {/if}
                            </label>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6" id="deliveries">
            <h4>{'ms2_frontend_deliveries' | lexicon}:</h4>
            <div class="form-group">
                <label class="col-md-4 control-label">
                    <span class="required-star">*</span> {'ms2_frontend_delivery_select' | lexicon}
                </label>
                <div class="col-sm-6">
                    {var $i = 0}
                    {foreach $deliveries as $idx => $delivery}
                        {var $checked = !$order.delivery && $i == 0 || $delivery.id == $order.delivery}
                        {var $i += 1}
                        <div class="checkbox">
                            <label class="delivery input-parent">
                                <input type="radio" name="delivery" value="{$delivery.id}" id="delivery_{$delivery.id}"
                                       data-payments="{$delivery.payments | json_encode}"
                                        {$checked ? 'checked' : ''}>
                                {if $delivery.logo?}
                                    <img src="{$delivery.logo}" alt="{$delivery.name}" title="{$delivery.name}"/>
                                {else}
                                    {$delivery.name}
                                {/if}
                                {if $delivery.description?}
                                    <p class="small">
                                        {$delivery.description}
                                    </p>
                                {/if}
                            </label>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h4>{'ms2_frontend_address' | lexicon}:</h4>
            {foreach ['index','region','city'] as $field}
                <div class="form-group input-parent">
                    <label class="col-md-4 control-label" for="{$field}">
                        <span class="required-star">*</span> {('ms2_frontend_' ~ $field) | lexicon}
                    </label>
                    <div class="col-sm-6">
                        <input type="text" id="{$field}" placeholder="{('ms2_frontend_' ~ $field) | lexicon}"
                               name="{$field}" value="{$form[$field]}"
                               class="form-control{($field in list $errors) ? ' error' : ''}">
                    </div>
                </div>
            {/foreach}
            <div class="form-group input-parent">
                <label class="col-md-4 control-label" for="street">
                    <span class="required-star">*</span> {'ms2_frontend_street' | lexicon}</label>
                <div class="col-md-6 row">
                    {foreach ['street','building','room'] as $field}
                        <div class="col-md-4">
                            <input type="text" id="{$field}" placeholder="{('ms2_frontend_' ~ $field) | lexicon}"
                                   name="{$field}" value="{$form[$field]}"
                                   class="form-control{($field in list $errors) ? ' error' : ''}">
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>

    </div>

    <button type="button" name="ms2_action" value="order/clean" class="btn btn-default ms2_link">
        <i class="glyphicon glyphicon-remove"></i> {'ms2_frontend_order_cancel' | lexicon}
    </button>

    <hr/>
    <div class="well">
        <div class="col-md-offset-2">
            <h3>{'ms2_frontend_order_cost' | lexicon}:
                <span id="ms2_order_cost">{$order.cost ?: 0}</span>
                {'ms2_frontend_currency' | lexicon}
            </h3>
            <button type="submit" name="ms2_action" value="order/submit" class="btn btn-default btn-primary ms2_link">
                {'ms2_frontend_order_submit' | lexicon}
            </button>
        </div>
    </div>
</form>