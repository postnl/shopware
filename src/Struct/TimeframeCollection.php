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

    public function filterByConfig(ConfigStruct $config)
    {
        $now = new \DateTimeImmutable("now", new \DateTimeZone('Europe/Amsterdam'));

        $timeParts = explode(':', $config->getCutOffTime());
        $cutoff = $now->setTime(...array_map('intval', $timeParts));

        $interval = $config->getTransitTime();

        // If now is later than cutoff, invert will be 1, otherwise 0.
        if($now->diff($cutoff)->invert) {
            $interval += 1;
        }

        $weekdays = [];

        foreach($config->getHandoverDays() as $day) {
            switch($day) {
                case 'sunday': $dayOfWeek = 0; break;
                case 'monday': $dayOfWeek = 1; break;
                case 'tuesday': $dayOfWeek = 2; break;
                case 'wednesday': $dayOfWeek = 3; break;
                case 'thursday': $dayOfWeek = 4; break;
                case 'friday': $dayOfWeek = 5; break;
                case 'saturday': $dayOfWeek = 6; break;
            }

            $weekdays[] = ($dayOfWeek + $interval) % 7;
        }

        dd($weekdays);



        dd($now, $cutoff);
    }
}
