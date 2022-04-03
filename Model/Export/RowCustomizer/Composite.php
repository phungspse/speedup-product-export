<?php

namespace PhungSpse\SpeedupProductExport\Model\Export\RowCustomizer;

use Magento\CatalogImportExport\Model\Export\RowCustomizer\Composite as MagentoExportRowCustomizerComposite;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use PhungSpse\SpeedupProductExport\Model\Config as SpeedupProductExportConfig;

class Composite extends MagentoExportRowCustomizerComposite
{
    /** @var SpeedupProductExportConfig */
    protected $speedupProductExportConfig;

    public function __construct(
        ObjectManagerInterface $objectManager,
        SpeedupProductExportConfig $speedupProductExportConfig,
        $customizers = []
    ) {
        parent::__construct($objectManager, $customizers);

        $this->speedupProductExportConfig = $speedupProductExportConfig;
    }

    /**
     * @inheritDoc
     */
    public function prepareData($collection, $productIds)
    {
        if ($this->speedupProductExportConfig->isEnable()) {
            /** @var ProductCollection $productCollection */
            $productCollection = clone $collection;
            $productCollection
                ->addAttributeToFilter('entity_id', ['in' => $productIds])
                ->distinct(true);

            $connection = $productCollection->getConnection();
            $queryStr = str_replace('*', 'type_id', $productCollection->getSelect()->__toString());
            $productTypes = $connection->fetchAssoc($queryStr);
            foreach ($this->customizers as $customizerName => $className) {
                $type = strtolower(str_replace('Product', '', $customizerName));
                if (!isset($productTypes[$type])) {
                    unset($this->customizers[$customizerName]); // remove product type from export process
                }
            }
        }

        parent::prepareData($collection, $productIds);
    }
}