<?php
declare(strict_types=1);

namespace PostNL\Shopware6\Struct;

use DateTime;
use DateTimeImmutable;
use Exception;
use Firstred\PostNL\Entity\Timeframe;
use Firstred\PostNL\Entity\TimeframeTimeFrame;
use Shopware\Core\Framework\Struct\Struct;

class TimeframeStruct extends Struct
{
    private DateTimeImmutable $from;
    private DateTimeImmutable $to;
    private ?array $options;
    private ?array $sustainability;

    /**
     * @throws Exception
     */
    public static function createFromTimeframe(Timeframe $timeframe): TimeframeStruct
    {
        if (!$timeframe->getDate()) {
            throw new Exception('No date in timeframe');
        }
        $date = $timeframe->getDate();
        if ($date instanceof \DateTime) {
            $date = clone $date;
        } else if ($date instanceof \DateTimeImmutable) {
            $date = \DateTime::createFromImmutable($date);
        } else {
            throw new Exception('No date in timeframe');
        }

        if (!$timeframe->getTimeframes()){
            throw new Exception('No timeframes in timeframe');
        }

        if (!isset($timeframe->getTimeframes()[0])){
            throw new Exception('No time frame in timeframes');
        }
        /** @var TimeframeTimeFrame $timeframeTimeFrame */
        $timeframeTimeFrame =  $timeframe->getTimeframes()[0];

        $from = new DateTime($date->format('Y-m-d') .' ' .$timeframeTimeFrame->getFrom());
        $to = new DateTime($date->format('Y-m-d') .' ' .$timeframeTimeFrame->getTo());

        return new self(DateTimeImmutable::createFromMutable($from),DateTimeImmutable::createFromMutable($to),$timeframeTimeFrame->getOptions());
    }

    public function __construct(DateTimeImmutable $from, DateTimeImmutable $to, array $options = null, array $sustainability = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->options = $options;
        $this->sustainability = $sustainability;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getFrom(): DateTimeImmutable
    {
        return $this->from;
    }

    /**
     * @param DateTimeImmutable $from
     */
    public function setFrom(DateTimeImmutable $from): void
    {
        $this->from = $from;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getTo(): DateTimeImmutable
    {
        return $this->to;
    }

    /**
     * @param DateTimeImmutable $to
     */
    public function setTo(DateTimeImmutable $to): void
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

    /**
     * @return array|null
     */
    public function getSustainability(): ?array
    {
        return $this->sustainability;
    }

    /**
     * @param array|null $sustainability
     */
    public function setSustainability(?array $sustainability): void
    {
        $this->sustainability = $sustainability;
    }
}
