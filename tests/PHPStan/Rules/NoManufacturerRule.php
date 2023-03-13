<?php declare(strict_types=1);

namespace PostNL\Shopware6\PHPStan\Rules;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Node\InClassNode;
use PHPStan\Node\InClosureNode;
use PHPStan\Node\InFunctionNode;
use PHPStan\Rules\Rule;


final class NoManufacturerRule implements Rule
{

    /**
     * @var array
     */
    private $manufacturers;


    /**
     */
    public function __construct()
    {
        $this->manufacturers = [
            'memo'
        ];
    }


    /**
     * @return string
     */
    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @param Node $node
     * @param Scope $scope
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (
            !$node instanceof InClassNode &&
            !$node instanceof InClassMethodNode &&
            !$node instanceof InClosureNode &&
            !$node instanceof InFunctionNode
        ) {
            return [];
        }


        foreach ($this->manufacturers as $manufacturer) {
            if ($this->hasNodeManufacturer($manufacturer, $node)) {
                return [
                    'Found Plugin Manufacturer: "' . $manufacturer . '"! Please remove this and keep PostNL branding!',
                ];
            }
        }

        return [];
    }

    /**
     * @param $manufacturer
     * @param Node $node
     * @return bool
     */
    private function hasNodeManufacturer($manufacturer, Node $node)
    {
        if ($node->getDocComment() !== null) {
            $comment = $node->getDocComment()->getText();

            if (str_contains(strtolower($comment), strtolower($manufacturer))) {
                return true;
            }
        }

        /** @var Doc $comment */
        foreach ($node->getComments() as $comment) {
            if (str_contains(strtolower($comment->getText()), strtolower($manufacturer))) {
                return true;
            }
        }

        return false;
    }
}
