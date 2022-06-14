<?php declare(strict_types=1);

namespace PostNL\Shopware6\Struct\Attribute;


use PostNL\Shopware6\Service\Attribute\AttributeStruct;
use Shopware\Core\System\Country\CountryEntity;

class ProductAttributeStruct extends AttributeStruct
{
    /**
     * @var string|null
     */
    protected $postnl_product_hs_code;

    /**
     * @var CountryEntity|null
     */
    protected $postnl_product_country_of_origin;

    /**
     * @return string|null
     */
    public function getPostnlProductHsCode(): ?string
    {
        return $this->postnl_product_hs_code;
    }

    /**
     * @param string|null $postnl_product_hs_code
     */
    public function setPostnlProductHsCode(?string $postnl_product_hs_code): void
    {
        $this->postnl_product_hs_code = $postnl_product_hs_code;
    }

    /**
     * @return CountryEntity|null
     */
    public function getPostnlProductCountryOfOrigin(): ?CountryEntity
    {
        return $this->postnl_product_country_of_origin;
    }

    /**
     * @param CountryEntity|null $postnl_product_country_of_origin
     */
    public function setPostnlProductCountryOfOrigin(?CountryEntity $postnl_product_country_of_origin): void
    {
        $this->postnl_product_country_of_origin = $postnl_product_country_of_origin;
    }

}
