<?php
declare(strict_types=1);

namespace PostNL\Shopware6\Facade;

use DateTimeInterface;
use Exception;
use Firstred\PostNL\Entity\CutOffTime;
use Firstred\PostNL\Entity\Request\GetDeliveryDate;
use Firstred\PostNL\Entity\Request\GetTimeframes;
use Firstred\PostNL\Entity\Timeframe;
use Firstred\PostNL\Exception\InvalidArgumentException;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Service\Shopware\DeliveryDateService;
use PostNL\Shopware6\Service\Shopware\TimeframeService;
use PostNL\Shopware6\Struct\Config\ConfigStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class CheckoutFacade
{
    protected DeliveryDateService $deliveryDateService;
    private TimeframeService $timeframeService;
    private ConfigService $configService;
    private LoggerInterface $logger;

    public function __construct(
        DeliveryDateService $deliveryDateService,
        TimeframeService    $timeframeService,
        ConfigService       $configService,
        LoggerInterface     $logger
    )
    {
        $this->deliveryDateService = $deliveryDateService;
        $this->timeframeService = $timeframeService;
        $this->configService = $configService;
        $this->logger = $logger;
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getDeliveryDays(SalesChannelContext $context, CustomerAddressEntity $addressEntity): array
    {
        $this->logger->debug('Getting delivery days');

        //Get config for DeliveryDateService & Timeframe
        $config = $this->configService->getConfiguration($context->getSalesChannelId(), $context->getContext());

        //Get cutoff times
        $cutOffTimes = $this->getCutOffTimes($config);
        $deliveryOptions = $config->getDeliveryOptions();
        $shippingDuration = $config->getShippingDuration();

        //Use DeliveryDateService
        $getDeliveryDate = $this->getGetDeliveryDate(
            $addressEntity,
            $cutOffTimes,
            $deliveryOptions,
            $shippingDuration,
            $config->getAllowSundaySorting(),
            $config->getSenderAddress()->getCountrycode()
        );

//        $this->logger->debug('Getting delivery date', ['getDeliveryDate' => $getDeliveryDate]);
        $getDeliveryDateResponse = $this->deliveryDateService->getDeliveryDate($context, $getDeliveryDate);

        $deliveryDateStart = $getDeliveryDateResponse->getDeliveryDate();

        if (!$deliveryDateStart instanceof \DateTimeImmutable) {
            $this->logger->error('Could not find a start date', ['startDate' => $deliveryDateStart]);
            throw new Exception('Could not find a start date');
        }

        $getTimeframes = $this->getGetTimeframes(
            $addressEntity,
            $deliveryDateStart,
            $deliveryOptions,
            $config->getAllowSundaySorting()
        );

        //Use TimeframeService
        $timeframes = $this->timeframeService->getTimeframes($context, $getTimeframes);

        if (!$timeframes->getTimeframes()) {
            //$this->logger->error('Could not get a timeframe', ['timeframes' => $timeframes]);
            throw new Exception('Could not get a timeframe');
        }
        //Return data
        return $timeframes->getTimeframes();
    }

    /**
     * @throws Exception
     */
    private function getGetTimeframes(
        CustomerAddressEntity $addressEntity,
        \DateTimeImmutable $startDate,
        array $deliveryOptions,
        bool $sundaySorting
    ): GetTimeframes
    {

        $countryCode = $addressEntity->getCountry()->getIso();
        if (!$countryCode) {
            throw new Exception('Invalid country code');
        }

        $postalCode = $addressEntity->getZipcode();
        if (!$postalCode) {
            throw new Exception('Invalid postal code');
        }

        $timeFrameStartDate = null;
        $timeFrameEndDate = null;

        // FIXME Remove? Should always be a DateTimeImmutable as that's what the method expects.
        if ($startDate instanceof \DateTimeImmutable) {
            $timeFrameStartDate = clone $startDate;
            $timeFrameEndDate = $startDate->modify('+2 week');
        }

        /** @phpstan-ignore-next-line  */
        if (empty($timeFrameStartDate)) {
            throw new Exception('Invalid start date');
        }

        /** @phpstan-ignore-next-line  */
        if (empty($timeFrameEndDate)) {
            throw new Exception('Invalid End date');
        }

        $getTimeFrames = new GetTimeframes();
        $timeframe = new Timeframe(
            null,
            $countryCode,
            $timeFrameStartDate,
            $timeFrameEndDate,
            null,
            null,
            $deliveryOptions,
            $postalCode,
            null,
            $sundaySorting ? 'true' : 'false',
            null,
            null,
            null,
            $timeFrameStartDate,
        );
        $getTimeFrames->setTimeframe($timeframe);

        return $getTimeFrames;
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function getGetDeliveryDate(
        CustomerAddressEntity $addressEntity,
        array $cutOffTimes,
        array $deliveryOptions,
        int $shippingDuration,
        bool $sundaySorting = false,
        string $originCountryCode = 'NL'
    ): GetDeliveryDate
    {
        $city = $addressEntity->getCity();
        if (!$city) {
            throw new Exception('Invalid city');
        }

        $countryCode = $addressEntity->getCountry()->getIso();
        if (!$countryCode) {
            throw new Exception('Invalid country code');
        }

        $postalCode = $addressEntity->getZipcode();
        if (!$postalCode) {
            throw new Exception('Invalid postal code');
        }

        return new GetDeliveryDate(
            $sundaySorting,
            $city,
            $countryCode,
            $cutOffTimes,
            null,
            null,
            $deliveryOptions,
            $originCountryCode,
            $postalCode,
            (new \DateTime("now", new \DateTimeZone('Europe/Amsterdam')))->format('d-m-Y H:i:s'),
            (string)$shippingDuration,
            null,
            null
        );
    }



    private function getCutOffTimes(ConfigStruct $config): array
    {
        $cutoffTimes = [];
        $cutoffTime = $config->getCutOffTime();

        $cutoffTimes[] = new CutOffTime('00', $cutoffTime, true);

        /**
         * Conforms to the N format character for dates
         * @see https://www.php.net/manual/en/datetime.format.php
         */
        $fullWeek = range(1, 7); //Monday = 1, Tuesday = 2, etc, Sunday = 7

        $dropoffDays = $config->getDropoffDays();

        /**
         * TODO Disabled for now because the SDK doesnt care about availability, just whether the day has been specified.
         * If a cutoff time has been specified for a date, it is set to available regardless of true or false,
         * If it hasn't been specified for a date, it is always set to false by the SDK.
         */
        $unavailable = $dropoffDays;//array_diff($fullWeek, $dropoffDays);

        foreach ($unavailable as $offDay) {
            $dayCode = str_pad($offDay, 2, '0', STR_PAD_LEFT);
            $disabledCutoffTime = new CutOffTime($dayCode, $cutoffTime, false);
            $cutoffTimes[] = $disabledCutoffTime;
        }

        return $cutoffTimes;
    }
}
