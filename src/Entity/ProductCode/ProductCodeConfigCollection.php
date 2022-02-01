<?php

declare(strict_types=1);

namespace PostNL\Shipments\Entity\ProductCode;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                         add(ProductCodeConfigEntity $entity)
 * @method void                         set(string $key, ProductCodeConfigEntity $entity)
 * @method ProductCodeConfigEntity[]    getIterator()
 * @method ProductCodeConfigEntity[]    getElements()
 * @method ProductCodeConfigEntity|null get(string $key)
 * @method ProductCodeConfigEntity|null first()
 * @method ProductCodeConfigEntity|null last()
 */
class ProductCodeConfigCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ProductCodeConfigEntity::class;
    }

    public function getAvailableOptions(): array
    {
        return [
            'nextDoorDelivery' => $this->isNextDoorDeliveryAvailable(),
            'returnIfNotHome' => $this->isReturnNotHomeAvailable(),
            'insurance' => $this->isInsuranceAvailable(),
            'signature' => $this->isSignatureAvailable(),
            'ageCheck' => $this->isAgeCheckAvailable(),
            'notification' => $this->isNotificationAvailable(),
        ];
    }

    public function isNextDoorDeliveryAvailable(): bool
    {
        return count($this->reduceToProperty('nextDoorDelivery')) > 1;
    }

    public function isReturnNotHomeAvailable(): bool
    {
        return count($this->reduceToProperty('returnIfNotHome')) > 1;
    }

    public function isInsuranceAvailable(): bool
    {
        return count($this->reduceToProperty('insurance')) > 1;
    }

    public function isSignatureAvailable(): bool
    {
        return count($this->reduceToProperty('signature')) > 1;
    }

    public function isAgeCheckAvailable(): bool
    {
        return count($this->reduceToProperty('ageCheck')) > 1;
    }

    public function isNotificationAvailable(): bool
    {
        return count($this->reduceToProperty('notification')) > 1;
    }

    private function reduceToProperty(string $property): array
    {
        $map = $this->map(function(ProductCodeConfigEntity $element) use ($property) {
            return $element->get($property);
        });

        return array_unique(array_values($map));
    }
}
