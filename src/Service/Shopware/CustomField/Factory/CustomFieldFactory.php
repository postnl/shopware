<?php

namespace PostNL\Shopware6\Service\Shopware\CustomField\Factory;

use PostNL\Shopware6\Exception\CustomField\CustomFieldException;
use PostNL\Shopware6\Exception\CustomField\CustomFieldNotExistsException;
use PostNL\Shopware6\Exception\CustomField\CustomFieldSetNotExistsException;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\CustomField\Aggregate\CustomFieldSet\CustomFieldSetEntity;
use Shopware\Core\System\CustomField\CustomFieldEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomFieldFactory
{
    public static function createFactory(ContainerInterface $container)
    {
        return new self(
            $container->get('custom_field.repository'),
            $container->get('custom_field_set.repository'),
            $container->get(DefinitionInstanceRegistry::class)
        );
    }

    /**
     * @var EntityRepositoryInterface
     */
    protected $customFieldRepository;

    /**
     * @var EntityRepositoryInterface
     */
    protected $customFieldSetRepository;

    /**
     * @var DefinitionInstanceRegistry
     */
    protected $definitionInstanceRegistry;

    /**
     * @param EntityRepositoryInterface  $customFieldRepository
     * @param EntityRepositoryInterface  $customFieldSetRepository
     * @param DefinitionInstanceRegistry $definitionInstanceRegistry
     */
    public function __construct(
        EntityRepositoryInterface  $customFieldRepository,
        EntityRepositoryInterface  $customFieldSetRepository,
        DefinitionInstanceRegistry $definitionInstanceRegistry
    )
    {
        $this->customFieldRepository = $customFieldRepository;
        $this->customFieldSetRepository = $customFieldSetRepository;
        $this->definitionInstanceRegistry = $definitionInstanceRegistry;
    }

    /**
     * @param string  $id
     * @param Context $context
     * @return CustomFieldEntity
     * @throws CustomFieldNotExistsException
     */
    public function getField(string $id, Context $context): CustomFieldEntity
    {
        $field = $this->customFieldRepository->search(
            (new Criteria([$id]))
                ->addAssociation('customFieldSet.relations'),
            $context
        )->first();

        if ($field instanceof CustomFieldEntity) {
            return $field;
        }

        throw new CustomFieldNotExistsException(['id' => $id]);
    }

    /**
     * @param string  $name
     * @param Context $context
     * @return CustomFieldEntity
     * @throws CustomFieldNotExistsException
     */
    public function getFieldByName(string $name, Context $context): CustomFieldEntity
    {
        $field = $this->customFieldRepository->search(
            (new Criteria())
                ->addFilter(new EqualsFilter('name', $name))
                ->addAssociation('customFieldSet.relations'),
            $context
        )->first();

        if ($field instanceof CustomFieldEntity) {
            return $field;
        }

        throw new CustomFieldNotExistsException(['name' => $name]);
    }

    /**
     * @param string  $id
     * @param Context $context
     * @return CustomFieldSetEntity
     * @throws CustomFieldSetNotExistsException
     */
    public function getSet(string $id, Context $context): CustomFieldSetEntity
    {
        $set = $this->customFieldSetRepository->search(
            (new Criteria([$id]))
                ->addAssociation('customFields')
                ->addAssociation('relations'),
            $context
        )->first();

        if ($set instanceof CustomFieldSetEntity) {
            return $set;
        }

        throw new CustomFieldSetNotExistsException(['id' => $id]);
    }

    /**
     * @param string  $name
     * @param Context $context
     * @return CustomFieldSetEntity
     * @throws CustomFieldSetNotExistsException
     */
    public function getSetByName(string $name, Context $context): CustomFieldSetEntity
    {
        $set = $this->customFieldSetRepository->search(
            (new Criteria())
                ->addFilter(new EqualsFilter('name', $name))
                ->addAssociation('customFields')
                ->addAssociation('relations'),
            $context
        )->first();

        if ($set instanceof CustomFieldSetEntity) {
            return $set;
        }

        throw new CustomFieldSetNotExistsException(['name' => $name]);
    }

    public function addFieldsToSet(string $setId, array $fieldIds, Context $context): void
    {
        $updates = array_map(function ($fieldId) use ($setId) {
            return [
                'id' => $fieldId,
                'customFieldSetId' => $setId,
            ];
        }, $fieldIds);

        $this->customFieldRepository->update($updates, $context);
    }

    /**
     * @param string                $name        Technical name of the custom field set
     * @param array<string, string> $labels      array of labels, where each key is a locale (i.e. en-GB)
     * @param string[]              $definitions an array of entity definition classnames
     * @param bool                  $editable    If this custom field set can be edited. Inverse of 'global'
     * @param Context               $context
     * @return string
     */
    public function createSet(
        string  $name,
        array   $labels,
        array   $definitions,
        bool    $editable,
        Context $context
    ): string
    {
        try {
            $set = $this->getSetByName($name, $context);
            return $set->getId();
        } catch (CustomFieldException $e) {
            // Do nothing
        }

        $id = Uuid::randomHex();

        $relations = array_map(function (string $definitionClass) {
            return ['entityName' => $this->definitionInstanceRegistry->get($definitionClass)->getEntityName()];
        }, $definitions);

        $this->customFieldSetRepository->create(
            [
                [
                    'id' => $id,
                    'name' => $name,
                    'global' => !$editable,
                    'config' => [
                        'label' => $labels,
                    ],
                    'relations' => $relations,
                ],
            ],
            $context
        );

        return $id;
    }

    /**
     * @param string     $name
     * @param array      $options
     * @param array      $labels
     * @param array|null $helpText
     * @param array|null $placeholder
     * @param int        $position
     * @param bool       $required
     * @param bool       $multiSelect
     * @param Context    $context
     * @return string
     */
    public function createSelectField(
        string  $name,
        array   $options,
        array   $labels,
        ?array  $helpText,
        ?array  $placeholder,
        int     $position,
        bool    $required,
        bool    $multiSelect,
        Context $context
    ): string
    {
        $mappedOptions = [];
        foreach ($options as $value => $optLabels) {
            $mappedOptions[] = [
                'label' => $optLabels,
                'value' => $value,
            ];
        }

        $config = [
            'options' => $mappedOptions,
            'componentName' => $multiSelect ? 'sw-multi-select' : 'sw-single-select',
            'customFieldType' => 'select',
        ];

        $config = $this->mergeDefaultConfig($config, $labels, $helpText, $placeholder, $position, $required);

        return $this->createField($name, 'select', $config, $context);
    }

    /**
     * @param string     $name
     * @param string     $entityDefinition
     * @param array|null $labelProperties
     * @param array      $labels
     * @param array|null $helpText
     * @param array|null $placeholder
     * @param int        $position
     * @param bool       $required
     * @param bool       $multiSelect
     * @param Context    $context
     * @return string
     */
    public function createEntitySelectField(
        string  $name,
        string  $entityDefinition,
        ?array  $labelProperties,
        array   $labels,
        ?array  $helpText,
        ?array  $placeholder,
        int     $position,
        bool    $required,
        bool    $multiSelect,
        Context $context
    ): string
    {
        // Customer has a different label in the select field.
        if (empty($labelProperties) && $entityDefinition === CustomerDefinition::class) {
            $labelProperties = [
                'firstName',
                'lastName',
            ];
        }

        $entity = $this->definitionInstanceRegistry->get($entityDefinition)->getEntityName();

        $config = [
            'entity' => $entity,
            'componentName' => $multiSelect ? 'sw-entity-multi-id-select' : 'sw-entity-single-select',
            'customFieldType' => 'entity',
        ];

        if (!empty($labelProperties)) {
            $config['labelProperty'] = $labelProperties;
        }

        $config = $this->mergeDefaultConfig($config, $labels, $helpText, $placeholder, $position, $required);

        return $this->createField($name, 'select', $config, $context);
    }

    /**
     * @param string     $name
     * @param array      $labels
     * @param array|null $helpText
     * @param array|null $placeholder
     * @param int        $position
     * @param bool       $required
     * @param Context    $context
     * @return string
     */
    public function createTextField(
        string  $name,
        array   $labels,
        ?array  $helpText,
        ?array  $placeholder,
        int     $position,
        bool    $required,
        Context $context
    ): string
    {
        $config = [
            'type' => 'text',
            'componentName' => 'sw-field',
            'customFieldType' => 'text',
        ];

        $config = $this->mergeDefaultConfig($config, $labels, $helpText, $placeholder, $position, $required);

        return $this->createField($name, 'text', $config, $context);
    }

    /**
     * @param string     $name
     * @param array      $labels
     * @param array|null $helpText
     * @param int        $position
     * @param bool       $required
     * @param Context    $context
     * @return string
     */
    public function createMediaField(
        string  $name,
        array   $labels,
        ?array  $helpText,
        int     $position,
        bool    $required,
        Context $context
    ): string
    {
        $config = [
            'componentName' => 'sw-media-field',
            'customFieldType' => 'media',
        ];

        $config = $this->mergeDefaultConfig($config, $labels, $helpText, [], $position, $required);

        return $this->createField($name, 'text', $config, $context);
    }

    /**
     * @param string     $name
     * @param bool       $isFloat
     * @param float|null $max
     * @param float|null $min
     * @param float|null $step
     * @param array      $labels
     * @param array|null $helpText
     * @param array|null $placeholder
     * @param int        $position
     * @param bool       $required
     * @param Context    $context
     * @return string
     */
    public function createNumberField(
        string  $name,
        bool    $isFloat,
        ?float  $max,
        ?float  $min,
        ?float  $step,
        array   $labels,
        ?array  $helpText,
        ?array  $placeholder,
        int     $position,
        bool    $required,
        Context $context
    ): string
    {
        $type = $isFloat ? 'float' : 'int';

        $config = [
            'type' => 'number',
            'numberType' => $type,
            'componentName' => 'sw-field',
            'customFieldType' => 'number',
        ];

        if (!is_null($max)) {
            $config['max'] = $max;
        }

        if (!is_null($min)) {
            $config['min'] = $min;
        }

        if (!is_null($step)) {
            $config['step'] = $step;
        }

        $config = $this->mergeDefaultConfig($config, $labels, $helpText, $placeholder, $position, $required);

        return $this->createField($name, $type, $config, $context);
    }

    /**
     * @param string     $name
     * @param string     $dateType
     * @param array      $labels
     * @param array|null $helpText
     * @param int        $position
     * @param bool       $required
     * @param Context    $context
     * @return string
     */
    public function createDateTimeField(
        string  $name,
        string  $dateType,
        array   $labels,
        ?array  $helpText,
        int     $position,
        bool    $required,
        Context $context
    ): string
    {
        if (!in_array($dateType, ['time', 'date', 'datetime'])) {
            $dateType = 'datetime';
        }

        $config = [
            'type' => $dateType,
            'config' => [
                'time_24hr' => true,
            ],
            'componentName' => 'sw-field',
            'customFieldType' => 'date',
        ];

        $config = $this->mergeDefaultConfig($config, $labels, $helpText, [], $position, $required);

        return $this->createField($name, $dateType, $config, $context);
    }

    /**
     * @param string     $name
     * @param string     $type
     * @param array      $labels
     * @param array|null $helpText
     * @param int        $position
     * @param bool       $required
     * @param Context    $context
     * @return string
     */
    protected function createBooleanField(
        string  $name,
        string  $type,
        array   $labels,
        ?array  $helpText,
        int     $position,
        bool    $required,
        Context $context
    ): string
    {
        if (!in_array($type, ['checkbox', 'switch'])) {
            $type = 'switch';
        }

        $config = [
            'type' => $type,
            'componentName' => 'sw-field',
            'customFieldType' => $type,
        ];

        $config = $this->mergeDefaultConfig($config, $labels, $helpText, [], $position, $required);

        return $this->createField($name, 'bool', $config, $context);
    }

    /**
     * @param string     $name
     * @param array      $labels
     * @param array|null $helpText
     * @param int        $position
     * @param bool       $required
     * @param Context    $context
     * @return string
     */
    public function createCheckboxField(
        string  $name,
        array   $labels,
        ?array  $helpText,
        int     $position,
        bool    $required,
        Context $context
    ): string
    {
        return $this->createBooleanField($name, 'checkbox', $labels, $helpText, $position, $required, $context);
    }

    /**
     * @param string     $name
     * @param array      $labels
     * @param array|null $helpText
     * @param int        $position
     * @param bool       $required
     * @param Context    $context
     * @return string
     */
    public function createSwitchField(
        string  $name,
        array   $labels,
        ?array  $helpText,
        int     $position,
        bool    $required,
        Context $context
    ): string
    {
        return $this->createBooleanField($name, 'switch', $labels, $helpText, $position, $required, $context);
    }

    /**
     * @param string     $name
     * @param array      $labels
     * @param array|null $helpText
     * @param array|null $placeholder
     * @param int        $position
     * @param bool       $required
     * @param Context    $context
     * @return string
     */
    public function createHtmlEditorField(
        string  $name,
        array   $labels,
        ?array  $helpText,
        ?array  $placeholder,
        int     $position,
        bool    $required,
        Context $context
    ): string
    {
        $config = [
            'componentName' => 'sw-text-editor',
            'customFieldType' => 'textEditor',
        ];

        $config = $this->mergeDefaultConfig($config, $labels, $helpText, $placeholder, $position, $required);

        return $this->createField($name, 'html', $config, $context);
    }

    /**
     * @param string  $name
     * @param array   $labels
     * @param int     $position
     * @param bool    $required
     * @param Context $context
     * @return string
     */
    public function createColorPickerField(
        string  $name,
        array   $labels,
        int     $position,
        bool    $required,
        Context $context
    ): string
    {
        $config = [
            'type' => 'colorpicker',
            'componentName' => 'sw-field',
            'customFieldType' => 'colorpicker',
        ];

        $config = $this->mergeDefaultConfig($config, $labels, [], [], $position, $required);

        return $this->createField($name, 'text', $config, $context);
    }

    /**
     * @param string  $name
     * @param string  $type
     * @param array   $config
     * @param Context $context
     * @return string
     */
    protected function createField(
        string  $name,
        string  $type,
        array   $config,
        Context $context
    ): string
    {
        try {
            $field = $this->getFieldByName($name, $context);
            return $field->getId();
        } catch (CustomFieldException $e) {
            // Do nothing
        }

        $id = Uuid::randomHex();

        $this->customFieldRepository->create(
            [
                [
                    'id' => $id,
                    'name' => $name,
                    'type' => $type,
                    'config' => $config,
                    'active' => true,
                ],
            ],
            $context
        );

        return $id;
    }

    /**
     * @param array      $config
     * @param array      $labels
     * @param array|null $helpText
     * @param array|null $placeholder
     * @param int        $position
     * @param bool       $required
     * @return array
     */
    private function mergeDefaultConfig(
        array  $config,
        array  $labels,
        ?array $helpText,
        ?array $placeholder,
        int    $position,
        bool   $required
    ): array
    {
        $defaultConfig = [
            'label' => $labels,
            'customFieldPosition' => $position,
        ];

        if (!empty($helpText)) {
            $defaultConfig['helpText'] = $helpText;
        }

        if (!empty($placeholder)) {
            $defaultConfig['placeholder'] = $placeholder;
        }

        if ($required) {
            $defaultConfig['validation'] = 'required';
        }

        return array_merge($defaultConfig, $config);
    }
}
