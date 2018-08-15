<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryConfiguration\Model\ConfigurationOptions;

use Magento\InventoryConfiguration\Model\ResourceModel\GetConfigurationValue;
use Magento\InventoryConfigurationApi\Api\GetManageStockConfigurationValueInterface;
use Magento\InventoryConfigurationApi\Api\StockItemConfigurationInterface;

class GetManageStockConfigurationValue implements GetManageStockConfigurationValueInterface
{
    /**
     * @var GetConfigurationValue
     */
    private $getConfigurationValue;

    /**
     * @param GetConfigurationValue $getConfigurationValue
     */
    public function __construct(
        GetConfigurationValue $getConfigurationValue
    ) {
        $this->getConfigurationValue = $getConfigurationValue;
    }

    /**
     * @inheritdoc
     */
    public function forStockItem(string $sku, int $stockId): ?int
    {
        $result = $this->getConfigurationValue->execute(
            StockItemConfigurationInterface::MANAGE_STOCK,
            $stockId,
            null,
            $sku
        );
        return isset($result) ? (int)$result : $result;
    }

    /**
     * @inheritdoc
     */
    public function forStock(int $stockId): ?int
    {
        $result = $this->getConfigurationValue->execute(StockItemConfigurationInterface::MANAGE_STOCK, $stockId);
        return isset($result) ? (int)$result : $result;
    }

    /**
     * @inheritdoc
     */
    public function forGlobal(): int
    {
        return (int)$this->getConfigurationValue->execute(StockItemConfigurationInterface::MANAGE_STOCK);
    }
}
