var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'Emergento_PonyUShippingMethodFrontendUi/js/model/shipping-save-processor/payload-extender-mixin': true
            },
            'Magento_Checkout/js/model/shipping-rates-validation-rules': {
                'Emergento_PonyUShippingMethodFrontendUi/js/checkout/model/shipping-rates-validation-rules-mixin': true
            }
        }
    }
};
