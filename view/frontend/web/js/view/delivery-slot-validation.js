define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Emergento_PonyUShippingMethodFrontendUi/js/model/delivery-slot-validator'
    ],
    function (Component, additionalValidators, yourValidator) {
        'use strict';
        additionalValidators.registerValidator(yourValidator);
        return Component.extend({});
    }
);
