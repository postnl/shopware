<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL\Label;

interface LabelInterface
{
    public function getContent(): string;
    public function getType(): string;
}