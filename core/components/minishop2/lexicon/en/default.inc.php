<?php
/**
 * Default English Lexicon Entries for miniShop2
 *
 * @package minishop2
 * @subpackage lexicon
 */

include_once('setting.inc.php');
$files = scandir(dirname(__FILE__));
foreach ($files as $file) {
    if (strpos($file, 'msp.') === 0) {
        @include_once($file);
    }
}

$_lang['minishop2'] = 'miniShop2';
$_lang['ms2_menu_desc'] = 'Awesome e-commerce extension';
$_lang['ms2_order'] = 'Order';
$_lang['ms2_orders'] = 'Orders';
$_lang['ms2_orders_intro'] = 'Manage your orders';
$_lang['ms2_orders_desc'] = 'Manage your orders';
$_lang['ms2_settings'] = 'Settings';
$_lang['ms2_settings_intro'] = 'Main settings of the shop. Here you can specify the methods of payments, deliveries and the statuses of orders';
$_lang['ms2_settings_desc'] = 'Statuses of orders, options of payments and deliveries';
$_lang['ms2_payment'] = 'Payment';
$_lang['ms2_payments'] = 'Payments';
$_lang['ms2_payments_intro'] = 'You can create any type of payments. The logic of payment (sending of the buyer on the remote service, reception of payment, etc.) is being implemented in the classroom that you specify.<br/>For methods of payment for the parameter "class" is required.';
$_lang['ms2_delivery'] = 'Delivery';
$_lang['ms2_deliveries'] = 'Deliveries';
$_lang['ms2_deliveries_intro'] = 'Possible variants of delivery. The logic of the calculation of the cost of delivery depending on the distance and weight is implemented by a class, which you specify in the settings.<br/>If you do not specify a class, the calculations will be made to the algorithm by default.';
$_lang['ms2_statuses'] = 'Statuses';
$_lang['ms2_statuses_intro'] = 'There are several mandatory status of the order: "new", "paid", "sent" and "cancelled". They can be configured, but can not be removed, as they are necessary for the operation of the shop. You can indicate your status for an extended the logic of work with orders.<br/>Status may be the final, it means that it cannot be switched to another, for example, "sent" and "cancelled". Стутус can be fixed, that is, with him you cannot switch to earlier statuses, such as "paid" cannot be switched on "new".';
$_lang['ms2_vendors'] = 'Vendors of goods';
$_lang['ms2_vendors_intro'] = 'The list of possible manufacturers of goods. What you add here, you can choose in the field "vendor" of the goods.';
$_lang['ms2_link'] = 'Link of goods';
$_lang['ms2_links'] = 'Links of goods';
$_lang['ms2_links_intro'] = 'The list of possible links of goods with each other. Connection type describes exactly how it will work, it is impossible to create, you can only select from the list.';
$_lang['ms2_option'] = 'Product option';
$_lang['ms2_options'] = 'Product options';
$_lang['ms2_options_intro'] = 'List of available product options. Category tree is used for filtering options by checked categories.<br/>To assign multiple options to the categories, you need to choose them using the Ctrl(Cmd) or Shift.';
$_lang['ms2_options_category_intro'] = 'List of available product options in the category.';
$_lang['ms2_default_value'] = 'Default value';
$_lang['ms2_customer'] = 'Customer';
$_lang['ms2_all'] = 'All';
$_lang['ms2_type'] = 'Type';

$_lang['ms2_btn_create'] = 'Create';
$_lang['ms2_btn_copy'] = 'Copy';
$_lang['ms2_btn_save'] = 'Save';
$_lang['ms2_btn_edit'] = 'Edit';
$_lang['ms2_btn_view'] = 'View';
$_lang['ms2_btn_delete'] = 'Delete';
$_lang['ms2_btn_undelete'] = 'Undelete';
$_lang['ms2_btn_publish'] = 'Publish';
$_lang['ms2_btn_unpublish'] = 'Unpublish';
$_lang['ms2_btn_cancel'] = 'Cancel';
$_lang['ms2_btn_back'] = 'Back (alt + &uarr;)';
$_lang['ms2_btn_prev'] = 'Previous btn (alt + &larr;)';
$_lang['ms2_btn_next'] = 'Next btn (alt + &rarr;)';
$_lang['ms2_btn_help'] = 'Help';
$_lang['ms2_btn_duplicate'] = 'Duplicate product';
$_lang['ms2_btn_addoption'] = 'Add option';
$_lang['ms2_btn_assign'] = 'Assign';

$_lang['ms2_actions'] = 'Actions';
$_lang['ms2_search'] = 'Search';
$_lang['ms2_search_clear'] = 'Clear';

$_lang['ms2_category'] = 'Category of the products';
$_lang['ms2_category_tree'] = 'Category tree';
$_lang['ms2_category_type'] = 'Category of the products';
$_lang['ms2_category_create'] = 'Add category';
$_lang['ms2_category_create_here'] = 'Category with the products';
$_lang['ms2_category_manage'] = 'Manage category';
$_lang['ms2_category_duplicate'] = 'Copy category';
$_lang['ms2_category_publish'] = 'Publish category';
$_lang['ms2_category_unpublish'] = 'Unpublish category';
$_lang['ms2_category_delete'] = 'Delete category';
$_lang['ms2_category_undelete'] = 'Undelete category';
$_lang['ms2_category_view'] = 'View on site';
$_lang['ms2_category_new'] = 'New category';
$_lang['ms2_category_option_add'] = 'Add option';
$_lang['ms2_category_option_rank'] = 'Rank';
$_lang['ms2_category_show_nested'] = 'Show nested products';

$_lang['ms2_product'] = 'Product of the shop';
$_lang['ms2_product_type'] = 'Product of the shop';
$_lang['ms2_product_create_here'] = 'Product of the shop';
$_lang['ms2_product_create'] = 'Add product';

$_lang['ms2_option_type'] = 'Option type';

$_lang['ms2_frontend_currency'] = 'USD';
$_lang['ms2_frontend_weight_unit'] = 'pt.';
$_lang['ms2_frontend_count_unit'] = 'pcs.';
$_lang['ms2_frontend_add_to_cart'] = 'Add to cart';
$_lang['ms2_frontend_tags'] = 'Tags';
$_lang['ms2_frontend_colors'] = 'Colors';
$_lang['ms2_frontend_color'] = 'Color';
$_lang['ms2_frontend_sizes'] = 'Sizes';
$_lang['ms2_frontend_size'] = 'Size';
$_lang['ms2_frontend_popular'] = 'Popular';
$_lang['ms2_frontend_favorite'] = 'Favorite';
$_lang['ms2_frontend_new'] = 'New';
$_lang['ms2_frontend_deliveries'] = 'Deliveries';
$_lang['ms2_frontend_delivery'] = 'Delivery';
$_lang['ms2_frontend_payments'] = 'Payments';
$_lang['ms2_frontend_payment'] = 'Payment';
$_lang['ms2_frontend_delivery_select'] = 'Select delivery';
$_lang['ms2_frontend_payment_select'] = 'Select payment';
$_lang['ms2_frontend_credentials'] = 'Credentials';
$_lang['ms2_frontend_address'] = 'Address';

$_lang['ms2_frontend_comment'] = 'Comment';
$_lang['ms2_frontend_receiver'] = 'Receiver';
$_lang['ms2_frontend_email'] = 'Email';
$_lang['ms2_frontend_phone'] = 'Phone';
$_lang['ms2_frontend_index'] = 'Zip/Postal code';
$_lang['ms2_frontend_region'] = 'State/Province';
$_lang['ms2_frontend_city'] = 'City';
$_lang['ms2_frontend_street'] = 'Street';
$_lang['ms2_frontend_building'] = 'Building';
$_lang['ms2_frontend_room'] = 'Room';

$_lang['ms2_frontend_order_cost'] = 'Total cost';
$_lang['ms2_frontend_order_submit'] = 'Checkout!';
$_lang['ms2_frontend_order_cancel'] = 'Reset form';
$_lang['ms2_frontend_order_success'] = 'Thank you for created order <b>#[[+num]]</b> on our website <b>[[++site_name]]</b>!';

$_lang['ms2_message_close_all'] = 'close all';
$_lang['ms2_err_unknown'] = 'Unknown error';
$_lang['ms2_err_ns'] = 'This field is required';
$_lang['ms2_err_ae'] = 'This field must be unique';
$_lang['ms2_err_json'] = 'This field requires JSON string';
$_lang['ms2_err_order_nf'] = 'The order with this id not found.';
$_lang['ms2_err_status_nf'] = 'The status with this id not found.';
$_lang['ms2_err_delivery_nf'] = 'The delivery with this id not found.';
$_lang['ms2_err_payment_nf'] = 'The payment with this id not found.';
$_lang['ms2_err_status_final'] = 'Final status is set, you can not change it.';
$_lang['ms2_err_status_fixed'] = 'Fixed status is set. You can not change it to previous status.';
$_lang['ms2_err_status_wrong'] = 'Wrong status of order.';
$_lang['ms2_err_status_same'] = 'This status is already set.';
$_lang['ms2_err_register_globals'] = 'Error: php parameter <b>register_globals</b> must be off.';
$_lang['ms2_err_link_equal'] = 'You trying to add link of product to itself';
$_lang['ms2_err_value_duplicate'] = 'You have not entered a value or entered a duplicate.';

$_lang['ms2_err_gallery_save'] = 'Could not save file';
$_lang['ms2_err_gallery_ns'] = 'Could not read file';
$_lang['ms2_err_gallery_ext'] = 'Wrong file extension';
$_lang['ms2_err_gallery_thumb'] = 'Could not generate thumbnails. See system log for details.';
$_lang['ms2_err_gallery_exists'] = 'Such an image is already in the product gallery.';
$_lang['ms2_err_wrong_image'] = 'File is not a valid image.';

$_lang['ms2_email_subject_new_user'] = 'You made the order #[[+num]] on the [[++site_name]]';
$_lang['ms2_email_subject_new_manager'] = 'You have a new order #[[+num]]';
$_lang['ms2_email_subject_paid_user'] = 'You have paid for the order #[[+num]]';
$_lang['ms2_email_subject_paid_manager'] = 'Order #[[+num]] was paid';
$_lang['ms2_email_subject_sent_user'] = 'Your order #[[+num]] was sent';
$_lang['ms2_email_subject_cancelled_user'] = 'Your order #[[+num]] was cancelled';

$_lang['ms2_payment_link'] = 'If you accidentally cancel the payment, you can always <a href="[[+link]]" style="color:#348eda;">to continue it at this link</a>.';

$_lang['ms2_category_err_ns'] = 'Category is not specified';
$_lang['ms2_option_err_ns'] = 'Option is not specified';
$_lang['ms2_option_err_nf'] = 'Option is not found';
$_lang['ms2_option_err_ae'] = 'Option already exists';
$_lang['ms2_option_err_save'] = 'An error while saving option';
$_lang['ms2_option_err_reserved_key'] = 'Option key is reserved';
$_lang['ms2_option_err_invalid_key'] = 'Option key is invalid';