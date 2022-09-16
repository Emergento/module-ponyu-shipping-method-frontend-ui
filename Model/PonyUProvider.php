<?php
declare(strict_types=1);

namespace Emergento\PonyUShippingMethodFrontendUi\Model;

use Emergento\PonyUShippingMethod\Model\Config;
use Magento\Checkout\Model\ConfigProviderInterface;

class PonyUProvider implements ConfigProviderInterface
{
    
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function getConfig()
    {
        return [
            'ponyu' => [
                'isAsap' => $this->config->isInstantModeEnabled()
            ]
        ];
    }
}
