<?php

namespace PostNL\Shipments\Service\Attribute;

use PostNL\Shipments\Defaults;
use PostNL\Shipments\Service\Attribute\TypeHandler\AttributeTypeHandlerInterface;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class AttributeFactory
{
    /**
     * @var EntityAttributeStruct[]
     */
    private $entityStructs;

    /**
     * @var AttributeTypeHandlerInterface[]
     */
    private $typeHandlers;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param iterable $entityStructs
     * @param iterable $typeHandlers
     * @param LoggerInterface $logger
     */
    public function __construct(iterable $entityStructs, iterable $typeHandlers, LoggerInterface $logger)
    {
        $this->entityStructs = $entityStructs;
        $this->typeHandlers = $typeHandlers;
        $this->logger = $logger;
    }

    /**
     * @param Entity $entity
     * @return EntityAttributeStruct
     * @throws \ReflectionException
     */
    public function createFromEntity(Entity $entity): EntityAttributeStruct
    {
        /**
         * If this entity does not contain getCustomFields, we can't do anything, and we shouldn't even create
         * an attribute struct in the first place.
         */
        // TODO 001 specific exception
        if(!method_exists($entity, 'getCustomFields')) {
            throw new \Exception('Entity does not contain custom fields');
        }

        $structClass = $this->getStructClassForEntity(get_class($entity));

        /**
         * Use the custom fields from this translated array instead of the regular ones.
         *
         * The getTranslated array contains all the translated fields of this entity, merged on top of the data
         * in the same fields from the default system language.
         */
        $customFields = $entity->getTranslation('customFields') ?? $entity->getCustomFields();

        /**
         * Initialize the data to an empty array, in case this entity does not contain our custom fields key yet.
         */
        $data = [];

        /**
         * If this entity has custom fields, and our key exists in the custom fields,
         * grab it and create the struct with it.
         */
        if(!empty($customFields) && array_key_exists(Defaults::CUSTOM_FIELDS_KEY, $customFields)) {
            $data = $customFields[Defaults::CUSTOM_FIELDS_KEY];
        }

        /** @var EntityAttributeStruct $struct */
        $struct = $this->create($structClass, $data);

        return $struct;
    }

    /**
     * @param string $structName
     * @param array $data
     * @return AttributeStruct
     * @throws \ReflectionException
     */
    public function create(string $structName, array $data = []): AttributeStruct
    {
        /** @var AttributeStruct $struct */
        $struct = new $structName();
        $structData = [];

        $reflectionClass = new \ReflectionClass($structName);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->getDeclaringClass()->getName() !== $structName) {
                continue;
            }

            if (!array_key_exists($reflectionProperty->getName(), $data)) {
                continue;
            }

            $value = $data[$reflectionProperty->getName()];

            $reflectionType = $this->resolvePropertyType($reflectionProperty, $reflectionClass);

            if (!$reflectionType instanceof \ReflectionNamedType) {
                $this->logger->critical(
                    sprintf('Could not infer type for property "%s"', $reflectionProperty->getName()),
                    [
                        'class' => $structName,
                        'data' => $data,
                    ]
                );
                continue;
            }

            if (!$reflectionType->isBuiltin()) {
                $structData[$reflectionProperty->getName()] = $this->getTypeHandler($reflectionType)->handle($value);
                continue;
            }

            $structData[$reflectionProperty->getName()] = $value;
        }

        $struct->assign($structData);
        return $struct;
    }

    /**
     * @param string $entityName
     * @return string
     * @throws \Exception
     */
    protected function getStructClassForEntity(string $entityName): string
    {
        foreach ($this->entityStructs as $struct) {
            if ($entityName === $struct->supports()) {
                return get_class($struct);
            }
        }

        throw new \Exception(sprintf('No struct found for entity "%s"', $entityName));
    }

    /**
     * @param \ReflectionNamedType $reflectionType
     * @return AttributeTypeHandlerInterface
     * @throws \Exception
     */
    protected function getTypeHandler(\ReflectionNamedType $reflectionType): AttributeTypeHandlerInterface
    {
        foreach ($this->typeHandlers as $handler) {
            if (in_array($reflectionType->getName(), $handler->supports())) {
                return $handler;
            }
        }

        throw new \Exception(sprintf('No handler found for type "%s"', $reflectionType->getName()));
    }

    /**
     * @param \ReflectionProperty $reflectionProperty
     * @param \ReflectionClass $reflectionClass
     * @return \ReflectionNamedType|null
     * @throws \Exception
     */
    private function resolvePropertyType(
        \ReflectionProperty $reflectionProperty,
        \ReflectionClass    $reflectionClass
    ): ?\ReflectionNamedType
    {
        $reflectionType = $reflectionProperty->getType();
        if ($reflectionType instanceof \ReflectionNamedType) {
            return $reflectionType;
        }

        $getMethod = 'get' . ucfirst($reflectionProperty->getName());
        $isMethod = 'is' . ucfirst($reflectionProperty->getName());

        if ($reflectionClass->hasMethod($getMethod)) {
            $reflectionMethod = $reflectionClass->getMethod($getMethod);
        } elseif ($reflectionClass->hasMethod($isMethod)) {
            $reflectionMethod = $reflectionClass->getMethod($isMethod);
        } else {
            throw new \Exception(sprintf('Accessor method for property "%s" not found',
                $reflectionProperty->getName()
            ));
        }

        return $reflectionMethod->getReturnType();
    }
}
