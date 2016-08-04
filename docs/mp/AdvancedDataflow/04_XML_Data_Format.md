XML, the commonly used data format, is integrated into the Dataflow. It's available in profile wizard additionally to CSV and Excel XML standart formats.

The next 3 parsers are implementing XML data format handling for 3 types of entities:

    advanceddataflow/sales_convert_parser_order_xml – Orders
    advanceddataflow/catalog_convert_parser_product_xml – Products
    advanceddataflow/customer_convert_parser_customer_xml – Customers

For example, the action to parse orders XML is:

	<action type="advanceddataflow/sales_convert_parser_order_xml" method="parse">
	    <var name="adapter">advanceddataflow/sales_convert_adapter_order</var>
	    <var name="method">parse</var>
	</action>

XML samples:

### Orders
	
 - [orders.xml](export_orders.xml)

### Products

 - [products.xml](export_all_products.xml)
 
### Customers

 - [customers.xml](export_customers.xml)
