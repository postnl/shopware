<?php

namespace PostNL\Shopware6\Service\PostNL\Label;

class Label
{
private string $content;
private string $barcode;
private string $type;

    /**
     * @param string $content
     * @param string $barcode
     * @param string $type
     */
    public function __construct(string $content, string $barcode, string $type)
    {
        $this->content = $content;
        $this->barcode = $barcode;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getBarcode(): string
    {
        return $this->barcode;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }


}
