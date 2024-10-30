=== Blaze Online eParcel for WooCommerce ===
Contributors: Blaze Online
Tags: Blaze Online, woocommerce, download, downloadable, eParcel, auspost
Requires at least: 4.4
Tested up to: 4.8
Stable tag: 1.2.10
License: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.html)

Manage your eParcel orders without leaving your WordPress WooCommerce store with Blaze Online eParcel for WooCommerce.

== Description ==

Blaze Online eParcel for WooCommerce and manage all aspects of shipping with eParcel right from within your WooCommerce online store, saving you huge amounts of time, money and avoiding the potential for human error.

Save wasted hours by not having to copy and paste order data, or export CSV files, to the Australia Post eParcel portal.

Save money by reducing human error – no more double shipping of orders or mistyping of addresses.

Deliver brilliant customer service by notifying your customers that their order has shipped, along with their tracking number for the delivery.

== Installation ==

= Minimum Requirements =

* WordPress 3.8 or greater
* WooCommerce 2.6.10 or greater
* PHP version 5.2.4 or greater (PHP 5.6 or greater is recommended)
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of Blaze Online eParcel for WooCommerce, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “Blaze Online eParcel for WooCommerce” and click Search Plugins. Once you’ve found our plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading our plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).



== Frequently Asked Questions ==
= Will Blaze Online eParcel calculate shipping charges for customer orders on Checkout? =
No, our eParcel solution is only intended to manage orders with eParcel once an order is received. You’ll still need your own solution for charging shipping for orders on checkout.

= Can I use my existing Shipping Methods? =
Yes, we can apply eParcel shipping to your existing shipping options.

= Does Blaze Online eParcel for WooCommerce validate delivery addresses? =
Yes, all orders associated with eParcel shipping are validated against the Australia Post postcode database. If an address fails validation it’s flagged against the order so that site administrators are notified. A consignment can only be allocated to an order with a valid delivery address.

Now, because we validate orders as they are created, when you first install Blaze Online eParcel, existing orders won’t have been validated, so they’ll all show as invalid.

Don’t be alarmed by this – its normal. You just need to view each order, and if the address passes validation, you can simply refresh your page, and move forward with creating your consignments. If the address is invalid, you just need to correct it, and we’ll revalidate it when you save.

= How does Blaze Online WooCommerce eParcel work? =
With Blaze Online for eParcel and WooCommerce, we generate consignment and article numbers for you, create labels, produce manifest summaries, notify your customers of tracking information and more, then when you’re ready to despatch you manifest, we upload it to the Australia Post eParcel SFTP server on your behalf.

Once you sign-up for Blaze Online for eParcel and WooCommerce, and install the Blaze Online WooCommerce eParcel extension, you can start managing your eParcel consignments from within WooCommerce.

All configuration options are managed from within WooCommerce using the Admin Settings for Blaze Online eParcel for WooCommerce, including your eParcel account number, eParcel shipping options, confirmation email details sent to users when orders ship, and other options.


= Do I need to do a manual sync to get inventory for orders? =
No, you don’t. Blaze Online automatically syncs your orders and products.

= What sort of customer service and support can I expect from Blaze Online? =
Glad you asked. We provide support via chat, phone and email, and every person working at Blaze Online is committed to providing first-rate customer service, so we’ll do what everything in our earthly powers to answer any questions or resolve any issues you might have.


== Screenshots ==

1. You'll see Blaze Online settings in the admin menu.

== Changelog ==

= Version 1.3.3 - 21 July 2020 Release =

* Add ebay shipping method support

= Version 1.3.2 - 04 June 2020 Release =

* Consignment lists display error with shipping types

= Version 1.3.1 - 02 June 2020 Release =

* Address validation issue
* Consignment single page display (Woocommerce compatible for shipping methods)

= Version 1.3.0 - 28 May 2020 Release =

* Consignment cost will only trigger in wordpress cron
* Consignment lists load issue when consignment cost enabled
* Assign shipping types issue

= Version 1.2.24 - 15 May 2020 Release =

* Database duplicate queries and errors

= Version 1.2.23 - 30 Jul 2019 Release =

* Support current version of wordpress and woocommerce
* Add consignment list filter for consignment cost

= Version 1.2.22 - 18 Jan 2019 Release =

* Easy setup configuration page
* Enhance assign shipping types

= Version 1.2.21 - 17 Jan 2019 Release =

* Minor issues under saving configuration

= Version 1.2.20 - 11 Jan 2019 Release =

* Table rate support

= Version 1.2.19 - 05 July 2018 Release =

* Remove consignment if not exist in current manifest.
* Fix delimiter issue when create consignment for international.

= Version 1.2.10 - 26 September 2017 Release =

* Fix displaying of capping message on other views.

= Version 1.2.9 - 20 September 2017 Release =

* Delivery Instructions Validation
* Add consignment capping

= Version 1.2.8 - 12 September 2017 Release =

* Validation errors apply to extension/plugin
* Delimiter issue when creating consignment international

= Version 1.2.7 - 30 August 2017 Release =

* Fix: Validate Combination issues

= Version 1.2.6 - 24 August 2017 Release =

* Add article within consignment fixes
* Edit article within consignment fixes

= Version 1.2.5 - 3 August 2017 Release =

* Plugin fields validation for dimensions and combinations
* Backend changes

= Version 1.2.4 - 27 July 2017 Release =

* Add getting LAID message for notification to cron job to minimize request to server

= Version 1.2.3 - 21 July 2017 Release =

* Fix validation error on dimensions.

= Version 1.2.2 - 19 July 2017 Release =

* Add shipping and tracking api comment instruction field
* Validate/trap if height, lenght and width dimension reach the maximum limit of 105cm

= Version 1.2.1 - 14 July 2017 Release =

* Open manifest checker recode will only check the last manifest generated if open then site will not update merchant account

= Version 1.2.0 - 14 July 2017 Release =

* Fix the consignment layout buttons
* Add validation for shipping and tracking api key and password
* Add/Check if there is open manifest and will not continue the saving merchant in the api
* Limit the good description to 40 in each order details before syncing
* Validate/trap if height dimension reach the maximum limit of 105cm

= Version 1.1.12 - 4 July 2017 Release =

* Shipping and tracking fixes and changes
* Add article dimensions validation when creating or edditing consignments. Add dimensions validation in adding, edditing article presets
* Fix duplicate notifications on admin
* Edit article for modify consignments
* Updated the script for manual function that will complete the manifest process.
* Fix: Create consignment using preset with dimension validation
* Add dimension validation for edit articles


= Version 1.1.11 - 17 May 2017 Release =

* Change version number to 1.1.11

= Version 1.1.10 - 17 May 2017 Release =

* Fix: Properly instantiate class for variant products

= Version 1.1.9 - 10 May 2017 Release =

* Make plugin compatible to WooCommerce Version 3.x above

= Version 1.1.8 - 27 April 2017 Release =

* Update plugin updater
* Add notification message for free trial users

= Version 1.1.7 - 24 January 2017 Release =

* Add 'single plain' option for express post

= Version 1.1.6 - 7 December 2016 Release =

* Add note message for assigning shipping types
* Change file security permission

= Version 1.1.5 - 29 September 2016 Release =

* Fix site mode when despatching on live operation mode
* Fix HS Tarrif issues when creating consignments in bulk for international consignments
* Fix create consignment in single view. Width, height and length field can now be empty
* Fix Internal server error on despatch
* Fix updating of status on orders after despatch

= Version 1.1.4 - 23 September 2016 Release =

* Fix dropdown options on services in adding and edditing shipping types.

= Version 1.1.3 - 22 September 2016 Release =

* Despatch functionality using ajax
* Decrease interval when processing despatch
* Remove the consignments that are not in the plugin database.
* Update all consignments to complete if current manifest successfully despatch
* Fixed email issue not sending even the option is set to yes in the configuration page
* Automatically round off weight to valid value (ex. if weight is 0.0034 then it will automatically round off to 0.01)
* Fixed interchange dimensions issue
* New setup for chargecodes
* Checker if the current manifest status is despatched or not
* Fixed invalid consignment data
* Validate consignments if "Has Commercial Value" option is set then "HS Tariff" input is required
* Address validation was transferred to consignment view

= Version 1.1.2 Stable - 9 August 2016 Release =

* International chargecode will only apply for international and domestic chargecode for Australia only. Prevent assigning chargecode.
* Add "Declare maximum value" option in bulk action.
* Apply Order value modification feature.
* Shipping types that support woo 2.6.1 up

= Version 1.1.1 Stable - 1 June 2016 Release =

**LPS User name and Password required!**
*You must Register for Australia Post Label Print Service (LPS) and have your LPS User name and Password for this release. Click here if to register.*

* Stable release with support for international consignment creation
* Added support for Australia Post Label Printing Service (LPS) for domestic and international label creation
* Support for setting label margins so that printing of labels can be tweaked.
* Blaze Online API key is now tied to a site, so that if a site is cloned to create a development site, consignments and manifests can not accidentally be altered on the development site.
* Add support for recent Australia Post Safe Drop changes.
* Added the option to copy order notes to Delivery Instructions on labels.
* Added the ability to edit Delivery Instructions for each consignment.
* Added additional columns to the Consignment View that can be customised via WordPress Screen Options.
* Added menu support for the WooCommerce Admin Bar Addition
* Labels, Customs Docs and Manifest PDFs now located in /wp-content folder so that they are not deleted when updating the Blaze Online plugin.
* Updates to the Blaze Online plugin can be done with a single click via the WordPress plugin page.
* Config option to set a default product 'description' for all international consignments.


= Version 1.1.1 Beta 8 - 13 May 2016 Release =

* [LEPW-183] - remove ‘shipment contains dangerous goods’ from international consignment options.
* [LEPW-187] - ‘order is over the maximum weight’ incorrectly displaying for international consignments created from the order view.
* [LEPW-188] - add a check to bulk ‘create consignment’ for detecting existing consignments on orders.
* [LEPW-190] - phone number fields stripped of non number, non space or non dash.
* [LEPW-191] - set unitValue to .01 if unitValue is 0 (zero) to prevent rejection of consignment by Aust Post.
* [LEPW-194] - Zero is not allowed for good weight - added logic to set good weight based on consignment weight.
* [LEPW-195] - add check for weight under .5kg for ECDx charge codes.
* [LEPW-196] - limit countries for PTIx charge code.
* [LEPW-197] - Issue with ‘goodsDescription’ on international consignments.
* [LEPW-198] - order notes missing when using bulk create consignment option.
* [LEPW-199] - remove ‘customs docs’ from Screen options.
* [LEPW-202] - consignment defaults for bulk consignment for international were incorrectly set for some options.
* [LEPW-204] - add ‘order notes’ column to Consignment View.


= Version 1.1.1 Beta 7 - 18 April 2016 Release =

* Removed international ‘Document’ function, as the documents and labels have been merged into a single file per the [Australia Post update of 18 April 2016 here](http://auspost.com.au/media/documents/changes-to-international-product-labelling-from-apr2016.pdf).
* Added bulk generation of international labels from the Consignment View.

= Version 1.1.1 Beta 6 - 5 February 2016 Release =

* Fix issue where international consignments are not creating if an order contains products with variants.
* Fix issue where screen options may conflict with other enabled plugins
* Removed unnecessary SQL code relating to Safe Drop flag.

= Version 1.1.1 Beta 5 - 12 January 2016 Release =

**Note**: you must be registered for Australia Post Label Print Service (LPS) to use this release.

* [LEPW-173] - limit articles for international to one
* [LEPW-179] - ‘Safe Drop’ option add for domestic consignments
* [LEPW-160] - moved location of labels, manifest and customs docs to ‘wp-content/Blaze Online’ folder
* [LEPW-180] - remove obsolete version check logic.

= Version 1.1.1 Beta 4 - 5 Jan 2016 Release =

**Note**: you must be registered for [Australia Post Label Print Service (LPS)](https://help.Blaze Online.com/hc/en-us/articles/206355963) to use this release.

* Added support for international consignment creation
* Added support for Australia Post Label Printing Service (LPS) for domestic and international label creation
* Support for setting label margins so that printing of labels can be tweaked.
* Blaze Online API key is now tied to a site, so that if a site is cloned to create a development site, consignments and manifests can not accidentally be altered on the development site.
* Add support for recent Australia Post Safe Drop changes.
* Added the option to copy order notes to Delivery Instructions on labels.
* Added the ability to edit Delivery Instructions for each consignment.
* Added additional columns to the Consignment View that can be customised via WordPress Screen Options.
* Added menu support for the [WooCommerce Admin Bar Addition](https://wordpress.org/plugins/woocommerce-admin-bar-addition/)
* Labels, Customs Docs and Manifest PDFs now located in /wp-content folder so that they are not deleted when updating the Blaze Online plugin.
* Updates to the Blaze Online plugin can be done with a single click via the WordPress plugin page.

= Version 0.3.3 - 8 Oct 2015 Release =

* LEPW-128 - Consignment view - add order search, and enable number of records
* LEPW-123 - copy order notes to deliveryInstructions field on consignment/labels
* LEPW-122 - Issue with latest version of WordPress and/or Woo
* LEPW-121 - Module conflicts with 'Aveda' admin theme


= Version 0.3.1 - 10 Feb 2015 Release =

* LEPW-118 - fixed issue where https-secured sites not able to despatch.

= Version 0.3.0 - 14 Jan 2015 Release =

* LEPW-114 - Rounding issues with total order weight.
* LEPW-111 - Correct article preset not be selected based on order total weight.
* LEPW-110 - Resolved conflict with LayerSlider WP plugin.
* LEPW-109 - Update to better support Table Rates modules by enabling assignment of shipping eParcel charge codes to specific table rates shipping methods.
* LEPW-105 - Uses WooCommerce 'Weight Unit' to determine total order weight in KGs.
* LEPW-92 - Invalid address warning not showing on order page.

= Version 0.2.1 - 23 Oct 2014 Release =

* LEPW-83 - When dispatching manifests with Operation Mode set to 'live' the popup title said 'Submit Test Manifest'.

= Version 0.2.0 - 21 Oct 2014 Release =

* LEPW-102 - Added config option to manually select order status to show on consignment view.
* LEPW-91 - Added support for 'WooCommerce Sequential Order Numbers' extension.
* LEPW-83 - Added notification that Blaze Online eParcel is in test mode when despatching manifests if operation mode is set to 'test'.
* LEPW-54 - Added config options to use order weight to set consignment weight, and option to not use dimensions for clients on cubic volume contracts with Aust Post.
* Minor fixes

= Version 0.1.5 - 10 Oct 2014 Release =

* LEPW-86 - Added option to show order status on consignment view.
* LEPW-77 - Additional fixes for re-factoring of order statuses in WooCommerce 2.2.x.

= Version 0.1.2 - 1 Oct 2014 Release =

* LEPW-75 - Link to despatched manifest from the order view was generating an error message.
* LEPW-77 - Update to support re-factoring of order statuses in WooCommerce 2.2.x.

= Version 0.1.1 - 23 Sep 2014 Release =

* LEPW-72 - Resolved an issue where consignments could not be created when using Safari browser.

= Version 0.1.0 - 19 Sep 2014 Release =

* LEPW-66 - Setting 'none' for a shipping type in 'Assign Shipping Types' was failing.
* LEPW-67 - Failed to allocated charge codes for 'Table Rates' shipping type, and was not able to generate consignments as a result.
* LEPW-68 - Enable access to eParcel for 'Shop Manager' role.
* LEPW-70 - No longer opens orders in new window from Consignment, Consignment Search and Manifest views.

= Version 0.0.9 - 11 Sep 2014 Release =

* Minor fixes

= Version 0.0.8 - 9 Sep 2014 Release =

* Initial beta release