<?php declare(strict_types=1);

namespace PostNL\Shopware6\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\InlineHTML;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;

final class StrictTypeRule implements Rule
{

    /**
     * @return string
     */
    public function getNodeType(): string
    {
        return FileNode::class;
    }

    /**
     * @param Node  $node
     * @param Scope $scope
     * @return string[]
     * @throws ShouldNotHappenException
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof FileNode) {
            throw new ShouldNotHappenException(\sprintf(
                'Expected node to be instance of "%s", but got instance of "%s" instead.',
                FileNode::class,
                \get_class($node)
            ));
        }

        $nodes = $node->getNodes();

        if (0 === \count($nodes)) {
            return [];
        }

        $firstNode = \array_shift($nodes);

        if (
            $firstNode instanceof InlineHTML
            && 2 === $firstNode->getEndLine()
            && 0 === \mb_strpos($firstNode->value, '#!')
        ) {
            $firstNode = \array_shift($nodes);
        }

        if ($firstNode instanceof Declare_) {
            foreach ($firstNode->declares as $declare) {
                if (
                    'strict_types' === $declare->key->toLowerString()
                    && $declare->value instanceof LNumber
                    && 1 === $declare->value->value
                ) {
                    return [];
                }
            }
        }

        return [
            'File has no "declare(strict_types=1)" declaration. This is required for this project!',
        ];
    }
}
