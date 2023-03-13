<?php
declare(strict_types=1);

namespace PostNL\Shopware6\Struct;

use Exception;
use Firstred\PostNL\Entity\Timeframe;
use Firstred\PostNL\Entity\TimeframeTimeFrame;
use Shopware\Core\Framework\Struct\Struct;

class TimeframeStruct extends Struct
{
    protected \DateTimeImmutable $from;
    protected \DateTimeImmutable $to;
    protected ?array $options;
    protected bool $sustainability;

    /**
     * @returns TimeframeStruct[]
     * @throws Exception
     */
    public static function createFromTimeframes(Timeframe $timeframe): array
    {
        if (!$timeframe->getDate()) {
            throw new Exception('No date in timeframe');
        }

        $date = $timeframe->getDate();

        if (!$date instanceof \DateTimeImmutable) {
            throw new Exception('No date in timeframe');
        }

        $timeFramesArray = [];

        /** @var TimeframeTimeFrame $timeframeTimeFrame */
        foreach ($timeframe->getTimeframes() as $timeframeTimeFrame) {
            $timeFramesArray[] = new self(
                $date->add(self::convertTimeToDateInterval($timeframeTimeFrame->getFrom())),
                $date->add(self::convertTimeToDateInterval($timeframeTimeFrame->getTo())),
                $timeframeTimeFrame->getOptions(),
                false // SDK doesn't return sustainability information...
            );
        }
        return $timeFramesArray;
    }

    public static function createFromJson(string $json): self
    {
        $timeframeData = json_decode($json);
        return new self(
            new \DateTimeImmutable($timeframeData->from),
            new \DateTimeImmutable($timeframeData->to),
            $timeframeData->options,
            $timeframeData->sustainability
        );
    }

    public static function convertTimeToDateInterval(string $time): \DateInterval
    {
        $parts = array_combine(['H', 'M', 'S'], explode(':', $time));

        $interval = 'PT';
        foreach($parts as $period => $part) {
            if($part === '00') {
                continue;
            }

            $interval .= $part . $period;
        }

        return new \DateInterval($interval);
    }

    public function __construct(\DateTimeImmutable $from, \DateTimeImmutable $to, array $options = null, bool $sustainability = false)
    {
        $this->from = $from;
        $this->to = $to;
        $this->options = $options;
        $this->sustainability = $sustainability;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getFrom(): \DateTimeImmutable
    {
        return $this->from;
    }

    /**
     * @param \DateTimeImmutable $from
     */
    public function setFrom(\DateTimeImmutable $from): void
    {
        $this->from = $from;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getTo(): \DateTimeImmutable
    {
        return $this->to;
    }

    /**
     * @param \DateTimeImmutable $to
     */
    public function setTo(\DateTimeImmutable $to): void
    {
        $this->to = $to;
    }

    /**
     * @return array|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    /**
     * @param array|null $options
     */
    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }

    public function hasOption(string $option): bool
    {
        return in_array($option, $this->options);
    }

    /**
     * @return bool
     */
    public function getSustainability(): bool
    {
        return $this->sustainability;
    }

    /**
     * @param bool$sustainability
     */
    public function setSustainability(bool $sustainability): void
    {
        $this->sustainability = $sustainability;
    }
}
