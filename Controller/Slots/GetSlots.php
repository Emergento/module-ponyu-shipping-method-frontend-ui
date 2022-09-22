<?php
declare(strict_types=1);

namespace Emergento\PonyUShippingMethodFrontendUi\Controller\Slots;

use Emergento\PonyUShipment\Model\Service\GetSlotsByShippingMethodCode;
use Emergento\PonyUShippingMethod\Model\Config;
use Emergento\PonyUShippingMethod\Model\GenerateSlotLabel;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\GetLatLngFromAddressInterface;
use Magento\InventorySourceSelectionApi\Api\Data\AddressInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class GetSlots implements HttpGetActionInterface
{

    public function __construct(
        private readonly JsonFactory $resultJsonFactory,
        private readonly AddressInterfaceFactory $addressFactory,
        private readonly GetLatLngFromAddressInterface $getLatLngFromAddress,
        private readonly RequestInterface $request,
        private readonly StoreManagerInterface $storeManager,
        private readonly GetSlotsByShippingMethodCode $getSlotsByShippingMethodCode,
        private readonly GenerateSlotLabel $generateSlotLabel,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(): ResultInterface
    {
        $result = $this->resultJsonFactory->create();
        $ponyUTimeZone = new \DateTimeZone(Config::PONYU_TIMEZONE);
        $slots = [];
        $methodCode = $this->request->getParam('method_code');
        $addressToInventorySourceSelectionAddress = $this->addressFactory->create([
            'street' => implode('', $this->request->getParam('street') ?? []) ?? '',
            'postcode' => $this->request->getParam('postcode') ?? '',
            'city' => $this->request->getParam('city') ?? '',
            'country' => $this->request->getParam('country') ?? '',
            'region' => $this->request->getParam('region') ?? ''
        ]);

        try {
            $slots = $this->getSlotsByShippingMethodCode->execute(
                $methodCode,
                $this->getLatLngFromAddress->execute($addressToInventorySourceSelectionAddress),
                '',
                (int) $this->storeManager->getStore()->getId()
            );

            foreach ($slots as &$slot) {
                $slot['label'] = $this->generateSlotLabel->execute(
                    new \DateTime($slot['deliveryDateStart'], $ponyUTimeZone),
                    new \DateTime($slot['deliveryDateEnd'], $ponyUTimeZone)
                );
            }
        } catch (NoSuchEntityException | GuzzleException $e) {
            $this->logger->debug(sprintf('Slot is missing %s', $e->getMessage()));
        }

        return $result->setData(['slot' => $slots]);
    }
}
