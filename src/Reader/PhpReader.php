<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Reader;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use Yiisoft\Composer\Config\Env;
use PhpParser\Node\Expr\FuncCall;

/**
 * PhpReader - reads PHP files.
 */
class PhpReader extends AbstractReader
{
    protected function readRaw(string $path)
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $saves = [];
        try {
            $names = [];
            $ast = $parser->parse(file_get_contents($path));

            $traverser = new NodeTraverser;
            $visitor = new class extends NodeVisitorAbstract {
                public static $names;
                private $stack;

                public function beginTraverse(array $nodes) {
                    $this->stack = [];
                }

                public function enterNode(Node $node) {
                    $this->stack[] = $node;
                }

                public function leaveNode(Node $node)
                {
                    $this->processNode($node);
                    array_pop($this->stack);

                    return $node;
                }

                private function processNode(Node $node)
                {
                    if (! $node instanceof ArrayDimFetch) {
                        return;
                    }
                    if (! $node->var instanceof Variable) {
                        return;
                    }
                    if (! $node->var->name === '_ENV') {
                        return;
                    }
                    if (! $node->dim instanceof String_) {
                        return;
                    }
                    if ($this->inDefine($node)) {
                        return;
                    }
                    $name = $node->dim->value;
                    self::$names[$name] = $name;
                }

                private function inDefine(): bool
                {
                    foreach ($this->stack as $node) {
                        if ($node instanceof FuncCall) {
                            return true;
                        }
                    }

                    return false;
                }
            };
            $traverser->addVisitor($visitor);
            $ast = $traverser->traverse($ast);
            foreach ($visitor::$names as $name) {
                $saves[$name] = $_ENV[$name];
                $_ENV[$name] = Env::get($name);
            }
        } catch (\Exception $e) {
            // do nothing
        }

        $params = $this->builder->getVars()['params'] ?? [];

        $result = static function (array $params) {
            return require func_get_arg(1);
        };

        $res = $result($params, $path);
        foreach ($saves as $key => $value) {
            $_ENV[$key] = $value;
        }

        return $res;
    }
}
