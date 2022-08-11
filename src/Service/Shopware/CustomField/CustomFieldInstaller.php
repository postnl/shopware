<?php

namespace PostNL\Shopware6\Service\Shopware\CustomField;

use PostNL\Shopware6\Service\Shopware\CustomField\Factory\CustomFieldFactory;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\Country\CountryDefinition;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomFieldInstaller
{
    const POSTNL_PRODUCT_SET_NAME = 'postnl_product';
    const POSTNL_PRODUCT_HS_CODE_FIELD_NAME = 'postnl_product_hs_code';
    const POSTNL_PRODUCT_COUNTRY_OF_ORIGIN_FIELD_NAME = 'postnl_product_country_of_origin';

    public static function createFactory(ContainerInterface $container)
    {
        return new self(
            CustomFieldFactory::createFactory($container),
        );
    }

    /**
     * @var CustomFieldFactory
     */
    protected $factory;

    public function __construct(CustomFieldFactory $customFieldFactory)
    {
        $this->factory = $customFieldFactory;
    }

    public function install(Context $context): void
    {
        $this->installHSFields($context);
    }

    public function uninstall(Context $context): void
    {
        $this->uninstallHSFields($context);
    }

    private function installHSFields(Context $context): void
    {
        $setId = $this->factory->createSet(
            self::POSTNL_PRODUCT_SET_NAME,
            [
                'en-GB' => 'PostNL',
                'de-DE' => 'PostNL',
                'nl-NL' => 'PostNL',
            ],
            [
                ProductDefinition::class,
            ],
            false,
            $context
        );

        $hsFieldId = $this->factory->createTextField(
            self::POSTNL_PRODUCT_HS_CODE_FIELD_NAME,
            [
                'en-GB' => 'HS Tariff Code',
                'de-DE' => 'HS-Zolltarifcode',
                'nl-NL' => 'GS-tariefcode',
            ],
            null,
            [
                'en-GB' => '000000',
            ],
            1,
            false,
            $context
        );

        $countryOriginFieldId = $this->factory->createEntitySelectField(
            self::POSTNL_PRODUCT_COUNTRY_OF_ORIGIN_FIELD_NAME,
            CountryDefinition::class,
            null,
            [
                'en-GB' => 'Country of origin',
                'de-DE' => 'Herkunftsland',
                'nl-NL' => 'Land van herkomst',
            ],
            null,
            null,
            2,
            false,
            false,
            $context
        );

        $this->factory->addFieldsToSet(
            $setId,
            [
                $hsFieldId,
                $countryOriginFieldId,
            ],
            $context
        );
    }

    private function uninstallHSFields(Context $context)
    {
        $this->factory->deleteSet(self::POSTNL_PRODUCT_SET_NAME,$context);
        $this->factory->deleteFieldSnippet(self::POSTNL_PRODUCT_HS_CODE_FIELD_NAME,$context);
        $this->factory->deleteFieldSnippet(self::POSTNL_PRODUCT_COUNTRY_OF_ORIGIN_FIELD_NAME,$context);
    }
}
