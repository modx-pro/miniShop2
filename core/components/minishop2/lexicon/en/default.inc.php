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
$_lang['ms2_settings_intro'] = 'Main settings of the shop. Here you can specify the methods of payment, delivery and the status of orders';
$_lang['ms2_settings_desc'] = 'Status of orders, options for payments and delivery';
$_lang['ms2_payment'] = 'Payment';
$_lang['ms2_payments'] = 'Payments';
$_lang['ms2_payments_intro'] = 'You can create any type of payment method. The logic for a payment medthod (sending of the buyer to the remote service, reception of payment, etc.) is controlled by the Handler class that you specify.<br/>For these methods of payment a parameter for "Handler class" is required e.g. PayPal refering to the file core/components/minishop2/custom/payment/paypal.class.php.';
$_lang['ms2_delivery'] = 'Delivery';
$_lang['ms2_deliveries'] = 'Delivery Methods';
$_lang['ms2_deliveries_intro'] = 'Possible methods of delivery. The logic for the calculation of the cost of delivery dependant on the distance and weight is implemented by a Handler class, which you specify in the individual shipping method&#39;s settings and refers to your custom file in core/components/minishop2/custom/delivery/ <br/>If you do not specify a class, the calculations will be made by the default algorithm.';
$_lang['ms2_statuses'] = 'Statuses';
$_lang['ms2_statuses_intro'] = 'There are several mandatory status for an order: "new", "paid", "sent" and "cancelled". They can be modified, but cannot be removed, as they are necessary for the operation of the shop. You can include your own status for an extended logic to work with custom order methods.<br/>A status defined as "final" cannot be altered to a another state, e.g. "sent" and "cancelled". A "fixed" status means that the state cannot be reverted to earlier status, e.g. "paid" cannot be reverted on "new".';
$_lang['ms2_vendors'] = 'Vendors of goods';
$_lang['ms2_vendors_intro'] = 'The list of possible manufacturers of goods. Enteries added here will be available in the drop-down field "vendor" of a product page in the manager.';
$_lang['ms2_link'] = 'Link of goods';
$_lang['ms2_links'] = 'Links of goods';
$_lang['ms2_links_intro'] = 'The list of possible links of goods with each other. The connection types are created here, the linking is individual goods achieved on the product pages.';
$_lang['ms2_customer'] = 'Customer';
$_lang['ms2_all'] = 'All';
$_lang['ms2_action'] = 'All';
$_lang['ms2_type'] = 'Type';

$_lang['ms2_btn_create'] = 'Create';
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

$_lang['ms2_bulk_actions'] = 'Actions';
$_lang['ms2_search'] = 'Search';
$_lang['ms2_search_clear'] = 'Clear';

$_lang['ms2_category'] = 'Category for products';
$_lang['ms2_category_tree'] = 'Category tree';
$_lang['ms2_category_type'] = 'Category for products';
$_lang['ms2_category_create'] = 'Add category';
$_lang['ms2_category_create_here'] = 'Category for products';
$_lang['ms2_category_manage'] = 'Manage category';
$_lang['ms2_category_duplicate'] = 'Copy category';
$_lang['ms2_category_publish'] = 'Publish category';
$_lang['ms2_category_unpublish'] = 'Unpublish category';
$_lang['ms2_category_delete'] = 'Delete category';
$_lang['ms2_category_undelete'] = 'Undelete category';
$_lang['ms2_category_view'] = 'View on site';
$_lang['ms2_category_new'] = 'New category';

$_lang['ms2_product'] = 'Product for shop';
$_lang['ms2_product_type'] = 'Product for shop';
$_lang['ms2_product_create_here'] = 'Product in this category';
$_lang['ms2_product_create'] = 'Add product';

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
$_lang['ms2_frontend_deliveries'] = 'Delivery method';
$_lang['ms2_frontend_payments'] = 'Payments';
$_lang['ms2_frontend_delivery_select'] = 'Select delivery method';
$_lang['ms2_frontend_payment_select'] = 'Select payment method';
$_lang['ms2_frontend_credentials'] = 'Details';
$_lang['ms2_frontend_address'] = 'Address';

$_lang['ms2_frontend_comment'] = 'Comment';
$_lang['ms2_frontend_receiver'] = 'Name';
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
$_lang['ms2_frontend_order_success'] = 'Thank you for your order <b>#[[+num]]</b> on our website <b>[[++site_name]]</b>!';

$_lang['ms2_message_close_all'] = 'close all';
$_lang['ms2_err_unknown'] = 'Unknown error';
$_lang['ms2_err_ns'] = 'This field is required';
$_lang['ms2_err_ae'] = 'This field must be unique';
$_lang['ms2_err_json'] = 'This field requires JSON string';
$_lang['ms2_err_order_nf'] = 'The order with this id not found.';
$_lang['ms2_err_status_nf'] = 'The status with this id not found.';
$_lang['ms2_err_delivery_nf'] = 'The delivery with this id not found.';
$_lang['ms2_err_payment_nf'] = 'The payment with this id not found.';
$_lang['ms2_err_status_final'] = 'Final status is set, no further changes are allowed.';
$_lang['ms2_err_status_fixed'] = 'Fixed status is set. You cannot revert to a previous status.';
$_lang['ms2_err_status_same'] = 'This status is already set.';
$_lang['ms2_err_register_globals'] = 'Error: php parameter <b>register_globals</b> must be off.';
$_lang['ms2_err_link_equal'] = 'You trying to add link of product to itself';

$_lang['ms2_err_gallery_save'] = 'Could not save file';
$_lang['ms2_err_gallery_ns'] = 'Could not read file';
$_lang['ms2_err_gallery_ext'] = 'Wrong file extension';
$_lang['ms2_err_gallery_thumb'] = 'Could not generate thumbnails. See system log for details.';
$_lang['ms2_err_gallery_exists'] = 'This image is already in the product gallery.';

$_lang['ms2_email_subject_new_user'] = 'You made the order #[[+num]] on the [[++site_name]]';
$_lang['ms2_email_subject_new_manager'] = 'You have a new order #[[+num]]';
$_lang['ms2_email_subject_paid_user'] = 'You have paid for the order #[[+num]]';
$_lang['ms2_email_subject_paid_manager'] = 'Order #[[+num]] was paid';
$_lang['ms2_email_subject_sent_user'] = 'Your order #[[+num]] was sent';
$_lang['ms2_email_subject_cancelled_user'] = 'Your order #[[+num]] was cancelled';

$_lang['ms2_payment_link'] = 'If you accidentally cancel the payment, you can always <a href="[[+link]]">complete it at this link</a>.';
