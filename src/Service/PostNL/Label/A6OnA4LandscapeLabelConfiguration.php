<?php

namespace PostNL\Shopware6\Service\PostNL\Label;

/**
 *
 */
class A6OnA4LandscapeLabelConfiguration
{
    const TOP_LEFT_FREE = 'topLeftFree';
    const TOP_RIGHT_FREE = 'topRightFree';
    const BOTTOM_LEFT_FREE = 'bottomLeftFree';
    const BOTTOM_RIGHT_FREE = 'bottomRightFree';
    public bool $topLeftFree;
    public bool $topRightFree;
    public bool $bottomLeftFree;
    public bool $bottomRightFree;
    public bool $active;

    /**
     * @param bool $topLeftFree
     * @param bool $topRightFree
     * @param bool $bottomLeftFree
     * @param bool $bottomRightFree
     */
    public function __construct(bool $topLeftFree, bool $topRightFree, bool $bottomLeftFree, bool $bottomRightFree)
    {
        $this->topLeftFree = $topLeftFree;
        $this->topRightFree = $topRightFree;
        $this->bottomLeftFree = $bottomLeftFree;
        $this->bottomRightFree = $bottomRightFree;
        $this->active = false;
    }

    public function hasFreeSlots(): bool
    {
        return $this->topLeftFree || $this->topRightFree || $this->bottomLeftFree || $this->bottomRightFree;
    }

    public function getFreeSlot(): ?string
    {
        foreach ($this->getSlotsArray() as $slotName => $free) {
            if ($free) {
                return $slotName;
            }
        }
        return null;
    }

    public function fillSlot(string $slot)
    {
        switch ($slot) {
            case self::TOP_LEFT_FREE:
                $this->topLeftFree = false;
                break;
            case self::TOP_RIGHT_FREE:
                $this->topRightFree = false;
                break;
            case self::BOTTOM_LEFT_FREE:
                $this->bottomLeftFree = false;
                break;
            case self::BOTTOM_RIGHT_FREE:
                $this->bottomRightFree = false;
                break;
        }
    }

    public static function getCoordinatesXYForSlot(string $slot)
    {
        switch ($slot) {
            case self::TOP_LEFT_FREE:
                return [0,0];
            case self::TOP_RIGHT_FREE:
                return [148,0];
            case self::BOTTOM_LEFT_FREE:
                return [0,105];
            case self::BOTTOM_RIGHT_FREE:
                return [148,105];
        }
    }

    public static function createFullLabel(): A6OnA4LandscapeLabelConfiguration
    {
        return new A6OnA4LandscapeLabelConfiguration(true, true, true, true);
    }

    private function getSlotsArray(): array
    {
        return [
            self::TOP_LEFT_FREE => $this->topLeftFree,
            self::TOP_RIGHT_FREE => $this->topRightFree,
            self::BOTTOM_LEFT_FREE => $this->bottomLeftFree,
            self::BOTTOM_RIGHT_FREE => $this->bottomRightFree
        ];
    }

}
