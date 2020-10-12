<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Reader;

use PhpParser\Error;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Use_;
use PhpParser\ParserFactory;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\Util\PhpRender;

/**
 * PhpReader - reads PHP files.
 */
class PhpReader extends AbstractReader
{
    protected function readRaw(string $path)
    {
        $params = $this->builder->getVars()['params'] ?? [];

        $builder = $this->builder;

        /** @var Builder $result */
        $result = static function (array $params) use ($builder) {
            $fileName = func_get_arg(1);

            $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
            try {

                $code = file_get_contents($fileName);
                $ast = $parser->parse($code);

                /** @var Stmt $node */
                foreach ($ast as $node){
                    if(isset($node->type) && $node->type == Use_::TYPE_NORMAL and !empty($node->uses)){
                        foreach ($node->uses as $use) {
                            $className = end($use->name->parts);
                            $builder->uses[ $className ] = 'use ' . implode('\\', $use->name->parts) . ';';
                        }
                    }
                }

                /**
                 * Everything is not evaluated at compile time by default except Buildtime::* calls.
                 * @see https://gist.github.com/samdark/86f2b9ff01a96892efbbf254eca8482d
                 */
                $prettyPrinter = new PhpRender(['builder' => $builder, 'context' => $fileName]);
                $newCode = $prettyPrinter->prettyPrintFile($ast);
                $outputPathFile = $builder->getOutputPath(basename($fileName) . '.' .md5($newCode) . '.ast');
                file_put_contents($outputPathFile, $newCode);
                $output = require $outputPathFile;
                @unlink($outputPathFile);
                return $output;

            } catch (Error $e) {
                throw $e;
            }
        };

        return $result($params, $path);
    }
}
