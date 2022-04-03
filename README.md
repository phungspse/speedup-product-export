# speedup-product-export
this is just a small trick, we will remove unused product types from list of export product types.

1. EXPORT COMMAND
- run queue message export by command
- get {{queue message id}} from table queue_message
bin/magento phung-spse:export:manual-export -m {{queue message id}}

2. SPEEDUP EXPORT PRODUCT
- Enable Speedup Export Product Process
goto admin > stores > configuration > PHUNG SPSE > Speedup Export Product > General > Enable Speedup Export Product Process
