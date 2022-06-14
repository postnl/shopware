<?php

namespace PostNL\Shopware6\Service\PostNL\Label;

/**
 * All titles are on a landscape page
 */
class A6OnA4LandscapeLabelConfiguration
{
    //A6 configurations
    const TOP_LEFT_FREE = 'topLeftFree';
    const TOP_RIGHT_FREE = 'topRightFree';
    const BOTTOM_LEFT_FREE = 'bottomLeftFree';
    const BOTTOM_RIGHT_FREE = 'bottomRightFree';
    //A5 configurations
    const LEFT_FREE = 'leftFree';
    const RIGHT_FREE = 'rightFree';

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

    public function hasFreeSlots(string $labelFormat): bool
    {
        if ($labelFormat == LabelDefaults::LABEL_FORMAT_A6) {
            return $this->topLeftFree || $this->topRightFree || $this->bottomLeftFree || $this->bottomRightFree;
        }
        if ($labelFormat == LabelDefaults::LABEL_FORMAT_A5) {
            return $this->topLeftFree && $this->bottomLeftFree || $this->topRightFree && $this->bottomRightFree;
        }
        return false;
    }

    public function getFreeSlot(string $labelFormat): ?string
    {
        if ($labelFormat == LabelDefaults::LABEL_FORMAT_A6) {
            foreach ($this->getSlotsArray() as $slotName => $free) {
                if ($free) {
                    return $slotName;
                }
            }
        }

        if ($labelFormat == LabelDefaults::LABEL_FORMAT_A5) {
            if ($this->topLeftFree && $this->bottomLeftFree) {
                return self::LEFT_FREE;
            }
            if ($this->topRightFree && $this->bottomRightFree) {
                return self::RIGHT_FREE;
            }
        }
        return null;
    }

    public function fillSlot(string $slot, string $labelFormat)
    {
        if ($labelFormat == LabelDefaults::LABEL_FORMAT_A5) {
            switch ($slot) {
                case self::LEFT_FREE:
                    $this->topLeftFree = false;
                    $this->bottomLeftFree = false;
                    break;
                case self::RIGHT_FREE:
                    $this->topRightFree = false;
                    $this->bottomRightFree = false;
                    break;
            }
        }
        if ($labelFormat == LabelDefaults::LABEL_FORMAT_A6) {
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
    }

    public static function getCoordinatesXYForSlot(string $slot, string $labelFormat)
    {
        if ($labelFormat == LabelDefaults::LABEL_FORMAT_A5) {
            switch ($slot) {
                case self::LEFT_FREE:
                    return [0, 0];
                case self::RIGHT_FREE:
                    return [148, 0];
            }
        }
        if ($labelFormat == LabelDefaults::LABEL_FORMAT_A6) {
            switch ($slot) {
                case self::TOP_LEFT_FREE:
                    return [0, 0];
                case self::TOP_RIGHT_FREE:
                    return [148, 0];
                case self::BOTTOM_LEFT_FREE:
                    return [0, 105];
                case self::BOTTOM_RIGHT_FREE:
                    return [148, 105];
            }
        }
    }

    public static function getRotatedCoordinatesXYForSlot(string $slot, string $labelFormat)
    {
        if ($labelFormat == LabelDefaults::LABEL_FORMAT_A5) {
            switch ($slot) {
                case self::LEFT_FREE:
                    return [-210, 0];
                case self::RIGHT_FREE:
                    return [-210, 148];
            }
        }
        if ($labelFormat == LabelDefaults::LABEL_FORMAT_A6) {
            switch ($slot) {
                case self::TOP_LEFT_FREE:
                    return [-105, 0];
                case self::TOP_RIGHT_FREE:
                    return [-105, 148];
                case self::BOTTOM_LEFT_FREE:
                    return [-210, 0];
                case self::BOTTOM_RIGHT_FREE:
                    return [-210, 148];
            }
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
            self::BOTTOM_RIGHT_FREE => $this->bottomRightFree,
        ];
    }

}
