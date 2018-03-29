<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\CustomerGraphQl\Model\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\CustomerGraphQl\Model\Resolver\Customer\CustomerDataProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Resolver\ResolverInterface;
use Magento\Framework\GraphQl\Resolver\Value;
use Magento\Framework\GraphQl\Resolver\ValueFactory;
use Magento\GraphQl\Model\ResolverContextInterface;

/**
 * Customers field resolver, used for GraphQL request processing.
 */
class Customer implements ResolverInterface
{
    /**
     * @var CustomerDataProvider
     */
    private $customerResolver;

    /**
     * @var ValueFactory
     */
    private $valueFactory;

    /**
     * @param CustomerDataProvider $customerResolver
     * @param ValueFactory $valueFactory
     */
    public function __construct(
        CustomerDataProvider $customerResolver,
        ValueFactory $valueFactory
    ) {
        $this->customerResolver = $customerResolver;
        $this->valueFactory = $valueFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(
        Field $field,
        array $value = null,
        array $args = null,
        $context,
        ResolveInfo $info
    ) : ?Value {
        /** @var ResolverContextInterface $context */
        if ((!$context->getUserId()) || $context->getUserType() == 4) {
            throw new GraphQlAuthorizationException(
                __(
                    'Current customer does not have access to the resource "%1"',
                    [\Magento\Customer\Model\Customer::ENTITY]
                )
            );
        }

        try {
            $data = $this->customerResolver->getCustomerById($context->getUserId());
            $result = function () use ($data) {
                return $data;
            };
            return $this->valueFactory->create($result);
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlNoSuchEntityException(__('Customer id %1 does not exist.', [$context->getUserId()]));
        }
    }
}
