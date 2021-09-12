<form class="ms2_form" id="msOrder" method="post">
    <div class="row">
        <div class="col-12 col-md-6">
            <h4>{'ms2_frontend_credentials' | lexicon}:</h4>
            {foreach ['email','receiver','phone'] as $field}
                <div class="form-group row input-parent">
                    <label class="col-md-4 col-form-label" for="{$field}">
                        {('ms2_frontend_' ~ $field) | lexicon} <span class="required-star">*</span>
                    </label>
                    <div class="col-md-8">
                        <input type="text" id="{$field}" placeholder="{('ms2_frontend_' ~ $field) | lexicon}"
                            name="{$field}" value="{$form[$field]}"
                            class="form-control{($field in list $errors) ? ' error' : ''}">
                    </div>
                </div>
            {/foreach}

            <div class="form-group row input-parent">
                <label class="col-md-4 col-form-label" for="comment">
                    {'ms2_frontend_comment' | lexicon} <span class="required-star">*</span>
                </label>
                <div class="col-md-8">
                    <textarea name="comment" id="comment" placeholder="{'ms2_frontend_comment' | lexicon}"
                        class="form-control{('comment' in list $errors) ? ' error' : ''}">{$form.comment}</textarea>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6" id="payments">
            <h4>{'ms2_frontend_payments' | lexicon}:</h4>
            <div class="form-group row">
                <div class="col-12">
                    {foreach $payments as $payment index=$index}
                        {var $checked = !($order.payment in keys $payments) && $index == 0 || $payment.id == $order.payment}
                        <div class="checkbox">
                            <label class="col-form-label payment input-parent">
                                <input type="radio" name="payment" value="{$payment.id}" id="payment_{$payment.id}"{$checked ? 'checked' : ''}>
                                {if $payment.logo?}
                                    <img src="{$payment.logo}" alt="{$payment.name}" title="{$payment.name}" class="mw-100"/>
                                {else}
                                    {$payment.name}
                                {/if}
                                {if $payment.description?}
                                    <p class="small">{$payment.description}</p>
                                {/if}
                            </label>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6" id="deliveries">
            <h4>{'ms2_frontend_deliveries' | lexicon}:</h4>
            <div class="form-group row">
                <div class="col-12">
                    {foreach $deliveries as $delivery index=$index}
                        {var $checked = !($order.delivery in keys $deliveries) && $index == 0 || $delivery.id == $order.delivery}
                        <div class="checkbox">
                            <label class="col-form-label delivery input-parent">
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

        <div class="col-12 col-md-6">
            <h4>{'ms2_frontend_address' | lexicon}:</h4>
            {foreach ['index','region','city', 'street', 'building', 'entrance','floor', 'room'] as $field}
                <div class="form-group row input-parent">
                    <label class="col-md-4 col-form-label" for="{$field}">
                        {('ms2_frontend_' ~ $field) | lexicon} <span class="required-star">*</span>
                    </label>
                    <div class="col-md-8">
                        <input type="text" id="{$field}" placeholder="{('ms2_frontend_' ~ $field) | lexicon}"
                            name="{$field}" value="{$form[$field]}"
                            class="form-control{($field in list $errors) ? ' error' : ''}">
                    </div>
                </div>
            {/foreach}

            <div class="form-group row input-parent">
                <label class="col-md-4 col-form-label" for="text_address">
                    {'ms2_frontend_text_address' | lexicon} <span class="required-star">*</span>
                </label>
                <div class="col-md-8">
                    <textarea name="text_address" id="text_address" placeholder="{'ms2_frontend_text_address' | lexicon}"
                        class="form-control{('text_address' in list $errors) ? ' error' : ''}">{$form.text_address}</textarea>
                </div>
            </div>

        </div>

    </div>

    <button type="button" name="ms2_action" value="order/clean" class="btn btn-danger ms2_link">
        {'ms2_frontend_order_cancel' | lexicon}
    </button>

    <hr class="mt-4 mb-4"/>


    <div class="d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-end mb-5">
        <h4 class="mb-md-0">{'ms2_frontend_order_cost' | lexicon}:</h4>
        <h3 class="mb-md-0 ml-md-2">
            <span id="ms2_order_cart_cost">{$order.cart_cost ?: 0}</span> {'ms2_frontend_currency' | lexicon} +
            <span id="ms2_order_delivery_cost">{$order.delivery_cost ?: 0}</span> {'ms2_frontend_currency' | lexicon} =
            <span id="ms2_order_cost">{$order.cost ?: 0}</span> {'ms2_frontend_currency' | lexicon}
        </h3>

        <button type="submit" name="ms2_action" value="order/submit" class="btn btn-lg btn-primary ml-md-2 ms2_link">
            {'ms2_frontend_order_submit' | lexicon}
        </button>
    </div>
</form>
