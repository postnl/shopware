<?php

namespace PostNL\Shipments\Service\Attribute;

use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Framework\Struct\Collection;
use Shopware\Core\Framework\Struct\Struct;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

abstract class AttributeStruct extends Struct
{
    const ADDITIONAL = 'additionalAttributes';
    const ASSIGN_KEYS = 'assignKeys';

    /**
     * @param array<mixed>|null $options
     */
    public function assign(?array $options = [])
    {
        /**
         * Create a struct to store attributes that don't have properties, but whose data still needs to be kept.
         */
        $additionalAttributes = $this->getArrayStructExtension(self::ADDITIONAL);

        /**
         * Create a struct to store the keys that were available during assignment.
         */
        $assignKeys = $this->getArrayStructExtension(self::ASSIGN_KEYS);

        if (empty($options)) {
            return;
        }

        /**
         * Initialize a NameConverter to be able to convert to camel case.
         */
        $caseConverter = new CamelCaseToSnakeCaseNameConverter();

        /**
         * Loop through all attributes in our array and assign the value to the property using
         */
        foreach ($options as $key => $value) {
            /**
             * Save the attributes, so we can determine which properties should be added to the array later
             */
            $assignKeys->offsetSet($key, $value);

            /**
             * Convert the snake_case property name to camelCase for our set methods.
             */
            $camelKey = $caseConverter->denormalize($key);

            /**
             * If a set method exists for this property, call it to set the value.
             */
            $setMethod = 'set' . ucfirst($camelKey);
            if (method_exists($this, $setMethod)) {
                $this->$setMethod($value);
                continue;
            }

            /**
             * Otherwise try to set the property directly
             */
            if (property_exists($this, $key)) {
                $this->$key = $value;
                continue;
            }

            /**
             * If the property doesn't exist in this class at all, store the attribute in the additional attribute struct
             * so we don't lose track of it.
             *
             * Using offsetSet() instead of set() because:
             * 1) Shopware is dumb
             * 2) They both have the same functionality
             * 3) They added dumb type-hinting to set
             * https://github.com/shopware/platform/commit/9c4cbe415d33419d9a9c5a3070007bf5cdf0a00e#diff-f21dd8cab48e2967baac4b0d4fb97e6107099f658733615947db47490e31b511R55
             */
            $additionalAttributes->offsetSet($key, $value);
        }
    }

    /**
     * Returns all the properties of this struct as a key-value array
     *
     * @return array<mixed>
     */
    public function getVars(): array
    {
        /**
         * If we have an extension with additional attributes, use that as the starting point
         */
        $data = $this->getArrayStructExtension(self::ADDITIONAL)->all();

        /**
         * Loop through all the properties of this class.
         */
        foreach (parent::getVars() as $key => $value) {
            /**
             * Ignore these properties, don't add them to the data we return
             */
            if (in_array($key, ['extensions'])) {
                continue;
            }

            /**
             * If the value was not set during construct, and the value is still null, don't add this to our data
             */
            if (!$this->getArrayStructExtension(self::ASSIGN_KEYS)->has($key) && is_null($value)) {
                continue;
            }

            /**
             * If $value is a Collection, return the inner elements array
             */
            if ($value instanceof Collection) {
                $data[$key] = $value->getElements();
                continue;
            }

            /**
             * If $value is a Struct, return all the properties of the struct
             */
            if ($value instanceof Struct) {
                $data[$key] = $value->getVars();
                continue;
            }

            /**
             * Otherwise just set the value in our data array.
             */
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Alias for getVars
     *
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return $this->getVars();
    }

    /**
     * Merges another attribute struct into this attribute struct
     *
     * @param AttributeStruct $struct
     * @return $this
     */
    public function merge(AttributeStruct $struct): self
    {
        /**
         * Initialize a NameConverter to be able to convert to camel case.
         */
        $caseConverter = new CamelCaseToSnakeCaseNameConverter();

        /**
         * Loop through the other struct's properties and set them on this struct,
         * either using the set method for the property, or setting the property directly.
         */
        foreach ($struct->getVars() as $key => $value) {
            /**
             * Convert the snake_case property name to camelCase for our set methods.
             */
            $camelKey = $caseConverter->denormalize($key);

            /**
             * If a set method exists for this property, use it
             */
            $setMethod = 'set' . ucfirst($camelKey);
            if (method_exists($this, $setMethod)) {
                $this->$setMethod($value);
                continue;
            }

            /**
             * Otherwise try to set the property directly
             */
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        return $this;
    }

    /**
     * Gets an extension by name, and makes sure it's an ArrayStruct
     * @param string $extensionName
     * @return ArrayStruct
     */
    protected function getArrayStructExtension(string $extensionName): ArrayStruct
    {
        /**
         * If we don't have an extension with this name, create it.
         */
        if (!$this->hasExtension($extensionName)) {
            $this->addExtension($extensionName, new ArrayStruct());
        }

        /**
         * Get the extension
         */
        $extension = $this->getExtension($extensionName);

        if(!$extension instanceof Struct) {
            return new ArrayStruct();
        }

        /**
         * Return it if it's an ArrayStruct
         */
        if ($extension instanceof ArrayStruct) {
            return $extension;
        }

        /**
         * Otherwise, get all the properties of this Struct, create a new ArrayStruct and return it.
         */
        return new ArrayStruct($extension->getVars());
    }
}
