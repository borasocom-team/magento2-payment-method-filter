<?php

namespace DR\PaymentMethodFilter\Model\Filter;

use DR\PaymentMethodFilter\Model\FilterInterface;
use Magento\Framework\DataObject;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;

class QuoteContent implements FilterInterface
{

    /**
     * Execute
     *
     * @param MethodInterface $paymentMethod
     * @param CartInterface   $quote
     * @param DataObject      $result
     *
     * @return void
     */
    public function execute(MethodInterface $paymentMethod, CartInterface $quote, DataObject $result)
    {
        $visibleItems = $quote->getItems();

        if (!empty($visibleItems)) {
            foreach ($visibleItems as $visibleItem) {
                $product = $visibleItem->getProduct();

                $customAttribute = $product->getCustomAttribute('disallowed_payment_methods');

                if ($customAttribute === null) {
                    continue;
                }

                $disallowedPaymentMethods = $customAttribute->getValue();

                if ($disallowedPaymentMethods == '') {
                    continue;
                }

                $disallowedPaymentMethods = explode(',', $disallowedPaymentMethods);

                if (in_array($paymentMethod->getCode(), $disallowedPaymentMethods)) {
                    $result->setData('is_available', false);

                    return;
                }
            }
        }
    }
}