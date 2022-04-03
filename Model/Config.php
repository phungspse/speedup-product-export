<?php

namespace PhungSpse\SpeedupProductExport\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const CONFIG_PATH_ENABLE = 'spse_export_product/general/enable';

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return (bool)$this->scopeConfig->getValue(self::CONFIG_PATH_ENABLE);
    }
}