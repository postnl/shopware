<?php

namespace PostNL\Shopware6\Service\PostNL\Label;

class MergedLabelResponse
{
    private string $type;
    private string $content;

    /**
     * @param string $type
     * @param string $content
     */
    public function __construct(string $type, string $content)
    {
        $this->type = $type;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }


}
