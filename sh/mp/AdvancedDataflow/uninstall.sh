#!/bin/bash

CWD="$(pwd)/../../.."

CONFIG_FILE="$CWD/app/etc/local.xml"
INDEXER_FILE="$CWD/shell/indexer.php"

PHP_BIN=`which php`

echo "Do you want to completely uninstall the extension?(y/n)"
read UNINSTALL

if [ "$UNINSTALL" == "y" ]; then

	rm -fr $CWD/app/code/local/Innoexts/AdvancedDataflow/
	rm -fr $CWD/app/design/adminhtml/default/default/template/advanceddataflow/
	rm -f $CWD/app/etc/modules/Innoexts_AdvancedDataflow.xml
	rm -f $CWD/app/locale/en_US/Innoexts_AdvancedDataflow.csv
	
	rm -fr $CWD/var/cache

	$PHP_BIN $INDEXER_FILE --reindexall	
fi
