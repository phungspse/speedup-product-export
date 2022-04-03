# speedup-product-export
this is just a small trick, we will remove unused product types from list of export product types.

1. EXPORT COMMAND
- run queue message export by command
- get {{queue message id}} from table queue_message
- bin/magento phung-spse:export:manual-export -m {{queue message id}}

2. SPEEDUP EXPORT PRODUCT
- Enable Speedup Export Product Process
- goto admin > stores > configuration > PHUNG SPSE > Speedup Export Product > General > Enable Speedup Export Product Process

This module can work with all magento edition (CE, EE, ECE) and version >= 2.3.2

If this extension can help your business and make you happy, you can treat me to a cup of coffee. :D 
https://paypal.me/phung1470
