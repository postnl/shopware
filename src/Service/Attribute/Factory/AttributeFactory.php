<?php

namespace PostNL\Shopware6\Service\Attribute\Factory;

use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Exception\Attribute\EntityCustomFieldsException;
use PostNL\Shopware6\Exception\Attribute\MissingAttributeStructException;
use PostNL\Shopware6\Exception\Attribute\MissingEntityAttributeStructException;
use PostNL\Shopware6\Exception\Attribute\MissingPropertyAccessorMethodException;
use PostNL\Shopware6\Exception\Attribute\MissingReturnTypeException;
use PostNL\Shopware6\Exception\Attribute\MissingTypeHandlerException;
use PostNL\Shopware6\Service\Attribute\AttributeStruct;
use PostNL\Shopware6\Service\Attribute\EntityAttributeStruct;
use PostNL\Shopware6\Service\Attribute\TypeHandler\AttributeTypeHandlerInterface;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

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
     * @throws EntityCustomFieldsException
     * @throws MissingAttributeStructException
     * @throws MissingEntityAttributeStructException
     * @throws MissingPropertyAccessorMethodException
     * @throws MissingReturnTypeException
     * @throws MissingTypeHandlerException
     */
    public function createFromEntity(Entity $entity, Context $context): EntityAttributeStruct
    {
        $this->logger->debug("Creating struct for entity", [
            'entity' => get_class($entity),
        ]);

        /**
         * If this entity does not contain getCustomFields, we can't do anything, and we shouldn't even create
         * an attribute struct in the first place.
         */
        if (!method_exists($entity, 'getCustomFields')) {
            throw new EntityCustomFieldsException([
                'entity' => get_class($entity),
            ]);
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
        if (!empty($customFields) && array_key_exists(Defaults::CUSTOM_FIELDS_KEY, $customFields)) {
            $data = $customFields[Defaults::CUSTOM_FIELDS_KEY];
        }

        /** @var EntityAttributeStruct $struct */
        $struct = $this->create($structClass, $data, $context);

        return $struct;
    }

    /**
     * @param string $structName
     * @param array $data
     * @return AttributeStruct
     * @throws MissingAttributeStructException
     * @throws MissingPropertyAccessorMethodException
     * @throws MissingReturnTypeException
     * @throws MissingTypeHandlerException
     */
    public function create(string $structName, array $data, Context $context): AttributeStruct
    {
        $this->logger->debug("Creating struct", [
            'struct' => $structName,
            'data' => $data,
        ]);

        /** @var AttributeStruct $struct */
        $struct = new $structName();
        $structData = [];

        try {
            $reflectionClass = new \ReflectionClass($structName);
        } catch (\ReflectionException $e) {
            throw new MissingAttributeStructException([
                'class' => $structName,
            ], $e);
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->getDeclaringClass()->getName() !== $structName) {
                continue;
            }

            $reflectionType = $this->resolvePropertyType($reflectionProperty, $reflectionClass);

            if (!array_key_exists($reflectionProperty->getName(), $data) && $reflectionType->allowsNull()) {
                $structData[$reflectionProperty->getName()] = null;
                continue;
            }

            $value = $data[$reflectionProperty->getName()] ?? null;

            if (!$reflectionType->isBuiltin()) {
                $handledValue = $this->getTypeHandler($reflectionType)->handle($value, $context);

                if(is_null($handledValue) && !$reflectionType->allowsNull()) {
                    // TODO throw specific exception
                    throw new \Exception('Value cannot be null');
                }

                $structData[$reflectionProperty->getName()] = $handledValue;
                continue;
            }

            if(is_null($value)) {
                switch($reflectionType->getName()) {
                    case "string":
                        $value = "";
                        break;
                    case "int":
                        $value = 0;
                        break;
                    case "bool":
                        $value = false;
                        break;
                    case "array":
                        $value = [];
                        break;
                }
            }

            $structData[$reflectionProperty->getName()] = $value;
        }

        $extraData = array_diff_key($data, $structData);

        $struct->assign($structData);
        $struct->assign($extraData);
        return $struct;
    }

    /**
     * @param string $entityName
     * @return string
     * @throws MissingEntityAttributeStructException
     */
    protected function getStructClassForEntity(string $entityName): string
    {
        try {
            $this->logger->debug("Getting struct class for entity", [
                'entity' => $entityName,
            ]);

            foreach ($this->entityStructs as $struct) {
                if ($entityName === $struct->supports()) {
                    return get_class($struct);
                }
            }

            throw new MissingEntityAttributeStructException([
                'entity' => $entityName,
            ]);
        } catch (MissingEntityAttributeStructException $e) {
            $this->logger->critical($e->getMessage(), $e->getParameters());
            throw $e;
        }
    }

    /**
     * @param \ReflectionNamedType $reflectionType
     * @return AttributeTypeHandlerInterface
     * @throws MissingTypeHandlerException
     */
    protected function getTypeHandler(\ReflectionNamedType $reflectionType): AttributeTypeHandlerInterface
    {
        try {
            $this->logger->debug("Getting handler for property type", [
                'type' => $reflectionType->getName(),
            ]);

            foreach ($this->typeHandlers as $handler) {
                if (in_array($reflectionType->getName(), $handler->supports())) {
                    return $handler;
                }
            }

            throw new MissingTypeHandlerException([
                'type' => $reflectionType->getName(),
            ]);
        } catch (MissingTypeHandlerException $e) {
            $this->logger->critical($e->getMessage(), $e->getParameters());
            throw $e;
        }
    }

    /**
     * @param \ReflectionProperty $reflectionProperty
     * @param \ReflectionClass $reflectionClass
     * @return \ReflectionNamedType|null
     * @throws MissingPropertyAccessorMethodException
     * @throws MissingReturnTypeException
     */
    private function resolvePropertyType(
        \ReflectionProperty $reflectionProperty,
        \ReflectionClass    $reflectionClass
    ): \ReflectionNamedType
    {
        try {
            $reflectionType = $reflectionProperty->getType();
            if ($reflectionType instanceof \ReflectionNamedType) {
                return $reflectionType;
            }
        } catch (\Throwable $e) {
            //Ignore error because typed properties exist since 7.4, thus getType is not available.
        }

        try {
            $converter = new CamelCaseToSnakeCaseNameConverter();
            $propertyName = ucfirst($converter->denormalize($reflectionProperty->getName()));

            $getMethod = 'get' . $propertyName;
            $isMethod = 'is' . $propertyName;

            if ($reflectionClass->hasMethod($getMethod)) {
                $reflectionMethod = $reflectionClass->getMethod($getMethod);
            } elseif ($reflectionClass->hasMethod($isMethod)) {
                $reflectionMethod = $reflectionClass->getMethod($isMethod);
            } else {
                throw new MissingPropertyAccessorMethodException([
                    'class' => $reflectionClass->getName(),
                    'property' => $reflectionProperty->getName(),
                    'example' => [$getMethod, $isMethod],
                ]);
            }

            $reflectionType = $reflectionMethod->getReturnType();

            if ($reflectionType instanceof \ReflectionNamedType) {
                return $reflectionType;
            }

            throw new MissingReturnTypeException([
                'class' => $reflectionClass->getName(),
                'method' => $reflectionMethod->getName(),
            ]);
        } catch (MissingPropertyAccessorMethodException|MissingReturnTypeException $e) {
            $this->logger->critical($e->getMessage(), $e->getParameters());
            throw $e;
        }
    }
}
