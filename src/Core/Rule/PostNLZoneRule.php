<?php declare(strict_types=1);

namespace PostNL\Shopware6\Core\Rule;

use PostNL\Shopware6\Service\PostNL\Delivery\Zone\ZoneService;
use Shopware\Core\Checkout\CheckoutRuleScope;
use Shopware\Core\Framework\Rule\Exception\UnsupportedOperatorException;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Rule\RuleScope;
use Shopware\Core\Framework\Validation\Constraint\ArrayOfType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

//TODO Move class out of Core folder
class PostNLZoneRule extends Rule
{
    /**
     * @var string[]|null
     */
    protected $postNLZones;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @param string        $operator
     * @param string[]|null $postNLZones
     */
    public function __construct(string $operator = self::OPERATOR_EQ, ?array $postNLZones = null)
    {
        parent::__construct();
        $this->postNLZones = $postNLZones;
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'postnlZone';
    }

    /**
     * @param RuleScope $scope
     * @return bool
     * @throws UnsupportedOperatorException
     * @throws \Exception
     */
    public function match(RuleScope $scope): bool
    {
        if (!$scope instanceof CheckoutRuleScope) {
            return false;
        }

        if ($this->operator === self::OPERATOR_EMPTY) {
            return false;
        }

        if (empty($this->postNLZones)) {
            return false;
        }

        $destinationCountryIso = $scope->getSalesChannelContext()
            ->getShippingLocation()
            ->getCountry()
            ->getIso();

        $sourceCountryIso = 'NL';

        $destinationZone = ZoneService::getDestinationZone($sourceCountryIso, $destinationCountryIso);

        switch ($this->operator) {
            case self::OPERATOR_EQ:
                return \in_array($destinationZone, $this->postNLZones, true);

            case self::OPERATOR_NEQ:
                return !\in_array($destinationZone, $this->postNLZones, true);

            default:
                throw new UnsupportedOperatorException($this->operator, self::class);
        }
    }

    /**
     * @return array<string, Constraint[]>
     */
    public function getConstraints(): array
    {
        $constraints = [
            'operator' => [
                new NotBlank(),
                new Choice([
                    self::OPERATOR_EQ,
                    self::OPERATOR_NEQ,
                    self::OPERATOR_EMPTY,
                ]),
            ],
        ];

        if ($this->operator === self::OPERATOR_EMPTY) {
            return $constraints;
        }

        $constraints['postNLZones'] = [
            new NotBlank(),
            new ArrayOfType('string'),
        ];

        return $constraints;
    }
}
