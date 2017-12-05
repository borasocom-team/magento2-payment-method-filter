<?php

namespace DR\PaymentMethodFilter\Model\Filter;

use DR\PaymentMethodFilter\Model\FilterInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\DataObject;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;

class Guest implements FilterInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $appState;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
    State $state
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->appState = $state;
    }

    /**
     * Execute
     *
     * @param MethodInterface $paymentMethod
     * @param CartInterface   $quote
     * @param DataObject      $result
     */
    public function execute(MethodInterface $paymentMethod, CartInterface $quote, DataObject $result)
    {
        if($this->appState->getAreaCode() != \Magento\Framework\App\Area::AREA_ADMINHTML){
            $customer = $quote->getCustomer();

            if ($customer && ($customer instanceof CustomerInterface) && $customer->getId()) {
                return;
            }

            $disallowedPaymentMethods = $this->scopeConfig->getValue(FilterGeneral::XML_PATH_DISALLOWED_PAYMENT_METHODS_FOR_GUEST, FilterGeneral::XML_PATH_DISALLOWED_PAYMENT_METHODS_SCOPE);

            $disallowedPaymentMethodsForAll = $this->scopeConfig->getValue(FilterGeneral::XML_PATH_DISALLOWED_PAYMENT_METHODS_FOR_USERS, FilterGeneral::XML_PATH_DISALLOWED_PAYMENT_METHODS_SCOPE);

            if ( ! empty($disallowedPaymentMethodsForAll)) {
                if ($disallowedPaymentMethods === null || $disallowedPaymentMethods === '') {
                    $disallowedPaymentMethods = $disallowedPaymentMethodsForAll;
                } else {
                    $disallowedPaymentMethods = $disallowedPaymentMethodsForAll . ',' . $disallowedPaymentMethods;
                }
            }

            if ($disallowedPaymentMethods === null || $disallowedPaymentMethods === '') {
                return;
            }

            $disallowedPaymentMethods = explode(',', $disallowedPaymentMethods);
            $result->setData('is_available', ! in_array($paymentMethod->getCode(), $disallowedPaymentMethods));
        }
    }
}