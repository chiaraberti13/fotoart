<?PHP
//SHARED CLIPS
echo "
{logo}
{\$config.settings.site_title}
{\$config.settings.site_url}
{\$config.settings.business_name}
{\$config.settings.business_address}
{\$config.settings.business_address2}
{\$config.settings.business_city}
{\$config.settings.business_state}
{\$config.settings.business_zip}
{\$config.settings.business_country}
{\$config.settings.support_email}
{\$config.settings.sales_email} 
------------------------------------------------
{\$loggedIn}
{\$member.f_name}
{\$member.l_name}
{\$member.comp_name}
{\$member.email}
{\$member.phone}
{\$member.website}
{\$member.primaryAddress.address}
{\$member.primaryAddress.address_2}
{\$member.primaryAddress.city}
{\$member.primaryAddress.state}
{\$member.primaryAddress.postal_code}
{\$member.primaryAddress.country}
{\$member.unencryptedPassword}
";

//PRIVATE CLIPS (ONLY FOR SPECIFIC CONTENT)
switch($content->content_code){
default:
break;
//checkConfirmPage
case 'checkConfirmPage':
echo "------------------------------------------------
{\$cartTotals.cartGrandTotalLocal.display}
{\$cartInfo.orderNumber}
";
break;
//invoiceTemplate
case 'invoiceTemplate':
echo "------------------------------------------------
{\$invoice.bill_id}
{\$invoice.bill_name}
{\$invoice.bill_address}
{\$invoice.bill_address2}
{\$invoice.bill_city}
{\$invoice.bill_state}
{\$invoice.bill_zip}
{\$invoice.bill_country}
{\$invoice.invoice_date_display}
{\$invoice.due_date_display}
-------START FOREACH-------
 {foreach \$invoiceItems as \$invoiceItem}
  {\$invoiceItem.quantity}
  {\$invoiceItem.thumbnail}
  {\$invoiceItem.name}
  {\$invoiceItem.cost_value} 
  {\$invoiceItem.paytype}
  {\$invoiceItem.cart_item_notes}
  {\$invoiceItem.itemDetails.licenseLang}
  {\$invoiceItem.asset_id}
  {\$invoiceItem.itemDetails.media.filename}
  {\$invoiceItem.itemDetails.media.galleriesHTML}
 {/foreach}
---------END FOREACH--------
{\$invoice.credits_subtotal}
{\$invoice.credits_discounts_total}
{\$invoice.credits_total}
{\$invoice.subtotal}
{\$invoice.shipping_cost}
{\$invoice.tax_total}
{\$invoice.taxa_cost}
{\$invoice.taxb_cost}
{\$invoice.taxc_cost}
{\$invoice.discounts_total}
{\$invoice.total}
{\$invoice.payment}
{\$invoice.balance}
{\$invoice.taxid}
{\$invoice.cart_notes}
{\$adminCurrency.code}
";
break;
//welcomeEmail
case 'welcomeEmail':
echo "------------------------------------------------
{\$loginPageURL}
";
break;
//verifyAccountEmail
case 'verifyAccountEmail':
echo "------------------------------------------------
{\$confirmLink}
";
break;
//orderEmail
case 'orderEmail':
echo "------------------------------------------------
{\$order.orderLink}
----LINK----
{linkto page=\$order.orderLink}
----------------
{\$invoice.invoice_number}
{\$order.order_number}
{\$invoice.invoice_date_display}
{\$invoice.payment_status_lang}
{\$order.order_status_lang}
{\$invoice.bill_name}
{\$invoice.bill_address}
{\$invoice.bill_address2}
{\$invoice.bill_city}
{\$invoice.bill_state}
{\$invoice.bill_zip}
{\$invoice.bill_country}
{\$invoice.ship_name}
{\$invoice.ship_address}
{\$invoice.ship_address2}
{\$invoice.ship_city}
{\$invoice.ship_state}
{\$invoice.ship_zip}
{\$invoice.ship_country}
-------START FOREACH-------
 {foreach \$invoiceItems as \$invoiceItem}
  {\$invoiceItem.quantity}
  {\$invoiceItem.thumbnail}
  {\$invoiceItem.name}
  {\$invoiceItem.cost_value} 
  {\$invoiceItem.paytype}
  {\$invoiceItem.cart_item_notes}
  {\$invoiceItem.itemDetails.licenseLang}
  {\$invoiceItem.asset_id}
  {\$invoiceItem.itemDetails.media.filename}
  {\$invoiceItem.itemDetails.media.galleriesHTML}
 {/foreach}
---------END FOREACH--------
{\$invoice.credits_subtotal}
{\$invoice.credits_discounts_total}
{\$invoice.credits_total}
{\$invoice.subtotal}
{\$invoice.shipping_cost}
{\$invoice.tax_total}
{\$invoice.taxa_cost}
{\$invoice.taxb_cost}
{\$invoice.taxc_cost}
{\$invoice.discounts_total}
{\$invoice.total}
{\$invoice.payment}
{\$invoice.balance}
{\$invoice.taxid}
{\$invoice.cart_notes}
{\$adminCurrency.code}
";
break;
//emailFriendMedia
case 'emailFriendMedia':
echo "------------------------------------------------
{\$form.fromName}
{\$form.message}
----ACTUAL MEDIA THUMBNAIL----
{mediaImage mediaID=\$media.encryptedID type=thumb folderID=\$media.encryptedFID mode=imgtag}
------------------------------------------------
{\$media.linkto}
";
break;
//emailForgottenPassword
case 'emailForgottenPassword':
echo "------------------------------------------------
{\$form.memberName}
{\$form.password}
";
break;
//newOrderEmailAdmin
case 'newOrderEmailAdmin':
echo "------------------------------------------------
{\$invoice.invoice_number}
{\$order.order_number}
{\$invoice.invoice_date_display_admin}
{\$invoice.payment_status_lang}
{\$order.order_status_lang}
{\$invoice.ship_email}
{\$invoice.bill_name}
{\$invoice.bill_address}
{\$invoice.bill_address2}
{\$invoice.bill_city}
{\$invoice.bill_state}
{\$invoice.bill_zip}
{\$invoice.bill_country}
{\$invoice.ship_name}
{\$invoice.ship_address}
{\$invoice.ship_address2}
{\$invoice.ship_city}
{\$invoice.ship_state}
{\$invoice.ship_zip}
{\$invoice.ship_country}
-------START FOREACH-------
 {foreach \$invoiceItems as \$invoiceItem}
  {\$invoiceItem.quantity}
  {\$invoiceItem.thumbnail}
  {\$invoiceItem.name}
  {\$invoiceItem.cost_value} 
  {\$invoiceItem.paytype}
  {\$invoiceItem.cart_item_notes}
  {\$invoiceItem.itemDetails.licenseLang}
  {\$invoiceItem.asset_id}
  {\$invoiceItem.itemDetails.media.filename} 
  {\$invoiceItem.itemDetails.media.galleriesHTML} 
 {/foreach}
---------END FOREACH--------
{\$invoice.credits_subtotal}
{\$invoice.credits_discounts_total}
{\$invoice.credits_total}
{\$invoice.subtotal}
{\$invoice.shipping_cost}
{\$invoice.tax_total}
{\$invoice.taxa_cost}
{\$invoice.taxb_cost}
{\$invoice.taxc_cost}
{\$invoice.discounts_total}
{\$invoice.total}
{\$invoice.payment}
{\$invoice.balance}
{\$invoice.taxid}
{\$invoice.cart_notes}
{\$adminCurrency.code}
";
break;
//newMemberEmailAdmin
case 'newMemberEmailAdmin':
echo "------------------------------------------------
{\$member.status}
";
break;
//newRatingEmailAdmin
case 'newRatingEmailAdmin':
echo "------------------------------------------------
{\$user}
{\$rating}
----ACTUAL MEDIA THUMBNAIL----
{mediaImage mediaID=\$media.encryptedID type=thumb folderID=\$media.encryptedFID mode=imgtag}
------------------------------------------------
{\$media.media_id}
{\$media.linkto}
{\$autoApprove}
";
break;
//newCommentEmailAdmin
case 'newCommentEmailAdmin':
echo "------------------------------------------------
{\$user}
{\$comment}
----ACTUAL MEDIA THUMBNAIL----
{mediaImage mediaID=\$media.encryptedID type=thumb folderID=\$media.encryptedFID mode=imgtag}
------------------------------------------------
{\$media.media_id}
{\$media.linkto}
{\$autoApprove}
";
break;
//newTagEmailAdmin
case 'newTagEmailAdmin':
echo "------------------------------------------------
{\$user}
{\$tag}
----ACTUAL MEDIA THUMBNAIL----
{mediaImage mediaID=\$media.encryptedID type=thumb folderID=\$media.encryptedFID mode=imgtag}
------------------------------------------------
{\$media.media_id}
{\$media.linkto}
{\$autoApprove}
";
break;
//requestFileEmailAdmin
case 'requestFileEmailAdmin':
echo "------------------------------------------------
{\$user}
{\$member.mem_id}
{\$email}
----ACTUAL MEDIA THUMBNAIL----
{mediaImage mediaID=\$media.encryptedID type=thumb folderID=\$media.encryptedFID mode=imgtag}
------------------------------------------------
{\$media.media_id}
{\$dsp.name}
{\$media.linkto}
";
break;
//newLightboxEmailAdmin
case 'newLightboxEmailAdmin':
echo "------------------------------------------------
{\$user}
{\$lightboxName}
{\$description}
{\$lightboxLink}
----LINK----
{linkto page=\$lightboxLink}
----------------
";
break;
//quoteEmailAdmin
case 'quoteEmailAdmin':
echo "------------------------------------------------
{\$user}
{\$contactForm.name}
{\$contactForm.email}
----ACTUAL MEDIA THUMBNAIL----
{mediaImage mediaID=\$media.encryptedID type=thumb folderID=\$media.encryptedFID mode=imgtag}
------------------------------------------------
{\$media.media_id}
{\$dsp.name}
{\$contactForm.message}
";
break;
//memInfoUpdateEmailAdmin
case 'memInfoUpdateEmailAdmin':
echo "------------------------------------------------
{\$member.mem_id}
{\$mode}
";
break;
//contactFormEmailAdmin
case 'contactFormEmailAdmin':
echo "------------------------------------------------
{\$form.name}
{\$form.email}
{\$form.question}
";
break;
case 'orderApprovalMessage':
echo "------------------------------------------------
{\$order.orderLink}
{\$order.order_number}
";
break;
}
?>