Use the next action XML to load all orders for the specified store.

	<action type="advanceddataflow/sales_convert_adapter_order" method="load">
	    <var name="store"><![CDATA[0]]></var>
	</action>

In most cases, you will need to filter orders to output. It may be done by filter variable defining. The full order filters list is:

Property | Description
----- | -----
filter/increment_id | to load with id starting with value
filter/real_order_id | to load with real id starting with value
filter/coupon_code | to load with coupon code starting with value
filter/status | to load bystatus. Valid values are: canceled,closed, complete, fraud, holded, payment_review, pending, pending_payment, pending_paypal, processing
filter/shipping_method | to load by shipping method code. Like tablerate_bestway, flatrate_flatrate, freeshipping_freeshipping, etc
filter/customer_group_id | to load by customer group identifier
filter/customer_email | to load with customer email containing value
filter/customer_firstname | to load with customer first name starting with value
filter/customer_lastname | to load with customer last name starting with value
filter/created_at/from | to loadcreated after date specified
filter/created_at/to | to loadcreated before date specified (including that date)
filter/updated_at/from | to loadupdated after date specified
filter/updated_at/to | to loadupdated before date specified (including that date)
filter/weight/from | to load with weight starting from value
filter/weight/to | to load with weight up to value
filter/total_item_count/from | to load with items count starting from value
filter/total_item_count/to | to load with items count up to value
filter/order_currency_code | to load by currency code
filter/shipping_amount/from | to load with shipping price starting from value
filter/shipping_amount/to | to load with shipping price up to value
filter/tax_amount/from | to load with tax starting from value
filter/tax_amount/to | to load with tax up to value
filter/subtotal/from | to load with subtotal starting from value
filter/subtotal/to | to load with subtotal up to value
filter/grand_total/from | to load with grand total starting from value
filter/grand_total/to | to load with grand total up to value
filter/discount_amount/from | to load with discount starting from value
filter/discount_amount/to | to load with discount up to value
filter/subtotal_incl_tax/from | to load with subtotal including tax starting from value
filter/subtotal_incl_tax/to | to load with subtotal including tax up to value
filter/total_due/from | to load with total due starting from value
filter/total_due/to | to load with total due up to value
filter/shipping_address_email | to load with shipping email containing value
filter/shipping_address_firstname | to load with shipping first name starting with value
filter/shipping_address_lastname | to load with shipping first name starting with value
filter/shipping_address_company | to load with shipping company containing value
filter/shipping_address_city | to load with shipping city containing value
filter/shipping_address_country_id | to load by shipping country. All ISO2 countries codes are valid
filter/shipping_address_region | to load with shipping region containing value
filter/shipping_address_postcode | to load with shipping postal code / zip containing value
filter/shipping_address_telephone | to load with shipping telephone starting with value
filter/billing_address_email | to load with billing email containing value
filter/billing_address_firstname | to load with billing first name starting with value
filter/billing_address_lastname | to load with billing first name starting with value
filter/billing_address_company | to load with billing company containing value
filter/billing_address_city | to load with billing city containing value
filter/billing_address_country_id | to load by billing country. All ISO-2 countries codes are valid
filter/billing_address_region | to load with billing region containing value
filter/billing_address_postcode | to load with billing postal code / zip containing value
filter/billing_address_telephone | to load with billing telephone starting with value
filter/item_sku | to load with item SKU starting with value
filter/item_name | to load with item name starting with value
filter/item_qty_ordered/from | to load with item quantity starting from value
filter/item_qty_ordered/to | to load with item quantity up to value
filter/item_weight/from | to load with item weight starting from value
filter/item_weight/to | to load with item weight up to value
filter/item_row_weight/from | to load with item total weight starting from value
filter/item_row_weight/to | to load with item total weight up to value
filter/item_price/from | to load with item price starting from value
filter/item_price/to | to load with item price up to value
filter/item_tax_amount/from | to load with item tax starting from value
filter/item_tax_amount/to | to load with item tax up to value
filter/item_discount_amount/from | to load with item discount starting from value
filter/item_discount_amount/to | to load with item discount up to value
filter/item_row_total/from | to load with item total starting from value
filter/item_row_total/to | to load with item total up to value
filter/price_incl_tax/from | to load with item price including tax starting from value
filter/price_incl_tax/to | to load with item price including tax up to value
filter/row_total_incl_tax/from | to load with total due starting from value
filter/row_total_incl_tax/to | to load with total due up to value

All filters are reflected in the “Profile Wizard” tab for profile in “Export Filters” section once the “Orders” entity type selected to export. The best way to set up filters is to save the profile and copy the resulting XML from “Profile Action XML” tab.

 
