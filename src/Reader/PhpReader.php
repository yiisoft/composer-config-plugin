<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Reader;

use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Use_;
use PhpParser\ParserFactory;
use Yiisoft\Composer\Config\Util\PhpPrinter;

/**
 * PhpReader - reads PHP files.
 */
class PhpReader extends AbstractReader
{
    protected function readRaw(string $path)
    {
        $ast = $this->parsePhp($path);
        $this->extractUses($ast);
        $newCode = $this->printPhp($ast, $path);

        $newFile = $this->builder->getOutputPath(strtr($path, '/', '.'));
        file_put_contents($newFile, $newCode);
        $output = $this->requireWithParams($newFile);
        #@unlink($newFile);

        return $output;
    }

    /**
     * @return mixed
     */
    protected function requireWithParams(string $path)
    {
        $params = $this->builder->getVars()['params'] ?? [];

        $result = static function (array $params) {
            return require func_get_arg(1);
        };

        return $result($params, $path);
    }


    protected function parsePhp(string $path): array
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        return $parser->parse(file_get_contents($path));
    }

    protected function extractUses($ast)
    {
        /** @var Stmt $node */
        foreach ($ast as $node){
            if (isset($node->type) && $node->type == Use_::TYPE_NORMAL and !empty($node->uses)) {
                foreach ($node->uses as $use) {
                    $className = end($use->name->parts);
                    $this->builder->uses[$className] = 'use ' . implode('\\', $use->name->parts) . ';';
                }
            }
        }
    }

    protected function printPhp(array $ast, string $path): string
    {
        $printer = new PhpPrinter([
            //'builder' => $this->builder,
            //'context' => $path,
            'THE_DIR' => dirname($path),
            'THE_FILE' => $path,
        ]);

        return $printer->prettyPrintFile($ast);
    }
}
