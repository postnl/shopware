<?php
declare(strict_types=1);

namespace PostNL\Shopware6\Struct;

use Exception;
use Firstred\PostNL\Entity\Timeframe;
use PostNL\Shopware6\Struct\Config\ConfigStruct;
use Shopware\Core\Framework\Struct\Collection;

/**
 * @method TimeframeStruct[]    getIterator()
 * @method TimeframeStruct[]    getElements()
 * @method TimeframeStruct|null get(string $key)
 * @method TimeframeStruct|null first()
 * @method TimeframeStruct|null last()
 */
class TimeframeCollection extends Collection
{
    /**
     * @param Timeframe[] $timeframes
     * @return TimeframeCollection
     * @throws Exception
     */
    public static function createFromTimeframes(array $timeframes): TimeframeCollection
    {
        try {
            /** @var TimeframeCollection $self */
            $self = (new \ReflectionClass(static::class))
                ->newInstanceWithoutConstructor();
        }
        catch (\ReflectionException $exception) {
            throw new \InvalidArgumentException($exception->getMessage());
        }

        foreach ($timeframes as $timeframe) {
            $newTimeFrames = TimeframeStruct::createFromTimeframes($timeframe);
            foreach ($newTimeFrames as $newTimeFrame) {
                $self->add($newTimeFrame);
            }
        }

        return $self;
    }

    public function filterByDropoffDays(ConfigStruct $config): self
    {
        $dropoffDays = $config->getDropoffDays();

        if(empty($dropoffDays)) {
            $dropoffDays = range(1, 6);
        }

        return $this->filter(function(TimeframeStruct $timeframe) use ($dropoffDays) {
            return in_array($timeframe->getFrom()->sub(new \DateInterval('P1D'))->format('N'), $dropoffDays);
        });
    }

    public function filterByMaximumDaysShown(ConfigStruct $config): self
    {
        $maximumDays = max(5, 1); // TODO Make config option?

        $shownDates = [];

        foreach($this->getElements() as $timeframe) {
            if(!in_array($timeframe->getFrom()->format('Ymd'), $shownDates)) {
                $shownDates[] = $timeframe->getFrom()->format('Ymd');
            }

            if(count($shownDates) === $maximumDays) {
                break;
            }
        }

        return $this->filter(function(TimeframeStruct $timeframe) use ($shownDates) {
            return in_array($timeframe->getFrom()->format('Ymd'), $shownDates);
        });
    }
}
