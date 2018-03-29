<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GroupedProductGraphQl\Model\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\Deferred\Product;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Resolver\ResolverInterface;
use Magento\Framework\GraphQl\Resolver\Value;
use Magento\Framework\GraphQl\Resolver\ValueFactory;
use Magento\GroupedProduct\Model\Product\Initialization\Helper\ProductLinks\Plugin\Grouped;

/**
 * {@inheritdoc}
 */
class GroupedItems implements ResolverInterface
{
    /**
     * @var ValueFactory
     */
    private $valueFactory;

    /**
     * @var Product
     */
    private $productResolver;

    /**
     * @param ValueFactory $valueFactory
     * @param Product $productResolver
     */
    public function __construct(
        ValueFactory $valueFactory,
        Product $productResolver
    ) {
        $this->valueFactory = $valueFactory;
        $this->productResolver = $productResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(
        Field $field,
        array $value = null,
        array $args = null,
        $context,
        ResolveInfo $info
    ): ?Value {
        if (!isset($value['model'])) {
            return null;
        }

        /** @var \Magento\Catalog\Model\Product $productModel */
        $productModel = $value['model'];
        $links = $productModel->getProductLinks();
        foreach ($links as $link) {
            if ($link->getLinkType() !== Grouped::TYPE_NAME) {
                continue;
            }

            $data[] = [
                'position' => (int)$link->getPosition(),
                'qty' => $link->getExtensionAttributes()->getQty(),
                'sku' => $link->getLinkedProductSku()
            ];
        }

        $result = function () use ($data) {
            return $data;
        };

        return $this->valueFactory->create($result);
    }
}
