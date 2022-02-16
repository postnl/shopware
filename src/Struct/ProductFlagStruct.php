<?php

namespace PostNL\Shipments\Struct;

use Shopware\Core\Framework\Struct\Struct;

class ProductFlagStruct extends Struct
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $visible;

    /**
     * @var bool
     */
    protected $disabled;

    /**
     * @var bool
     */
    protected $selected;

    public function __construct(string $name, bool $visible = false, bool $disabled = false, bool $selected = false)
    {
        $this->name = $name;
        $this->visible = $visible;
        $this->disabled = $disabled;
        $this->selected = $selected;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }

}
