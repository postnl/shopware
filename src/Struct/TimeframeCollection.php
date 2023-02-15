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
        $interval = $config->getTransitTime();

        //foreach($this->getElements() as $element) {
            //dump($element);
        //}

        //dd($interval);
    }
}
