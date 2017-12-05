<?php

namespace DR\PaymentMethodFilter\Model\Filter;

abstract class FilterGeneral{
    const XML_PATH_DISALLOWED_PAYMENT_METHODS_FOR_GUEST = 'checkout/options/disallowed_payment_methods_for_guest';
    const XML_PATH_DISALLOWED_PAYMENT_METHODS_FOR_USERS = 'checkout/options/disallowed_payment_methods_for_users';

    const XML_PATH_DISALLOWED_PAYMENT_METHODS_SCOPE = 'store';
}