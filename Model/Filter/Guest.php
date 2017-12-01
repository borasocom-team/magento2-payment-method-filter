<?php

namespace DR\PaymentMethodFilter\Model\Filter;

use DR\PaymentMethodFilter\Model\FilterInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;

class Guest implements FilterInterface
{
    const XML_PATH_DISALLOWED_PAYMENT_METHODS_FOR_GUEST = 'checkout/options/disallowed_payment_methods_for_guest';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }


    /**
     * Execute
     *
     * @param MethodInterface $paymentMethod
     * @param CartInterface $quote
     * @param DataObject $result
     */
    public function execute(MethodInterface $paymentMethod, CartInterface $quote, DataObject $result)
    {
        $customer = $quote->getCustomer();

        if ($customer && ($customer instanceof CustomerInterface) && $customer->getId()) {
            return;
        }

        $disallowedPaymentMethods = $this->scopeConfig->getValue(self::XML_PATH_DISALLOWED_PAYMENT_METHODS_FOR_GUEST);

        if ($disallowedPaymentMethods === null || $disallowedPaymentMethods === '') {
            return;
        }

        $disallowedPaymentMethods = explode(',', $disallowedPaymentMethods);
        $result->setData('is_available', !in_array($paymentMethod->getCode(), $disallowedPaymentMethods));
    }
}