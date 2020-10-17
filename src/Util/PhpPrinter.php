<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Util;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\MagicConst;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;

class PhpPrinter extends Standard
{
    private bool $isBuildtime = false;

    protected function pExpr_MethodCall(Expr\MethodCall $node)
    {
        return $this->tokenize(parent::pExpr_MethodCall($node));
    }

    protected function pExpr_StaticCall(Expr\StaticCall $node)
    {
        $class = $this->pDereferenceLhs($node->class);
        if ('Buildtime' === $class) {
            $this->isBuildtime = true;
            $res = $this->my_pMaybeMultiline($node->args);
            $this->isBuildtime = false;

            return $res;
        }

        return $this->tokenize(parent::pExpr_StaticCall($node));
    }

    protected function pExpr_Eval(Expr\Eval_ $node)
    {
        return $this->tokenize(parent::pExpr_Eval($node));
    }

    protected function pExpr_Include(Expr\Include_ $node)
    {
        return $this->tokenize(parent::pExpr_Include($node));
    }

    // XXX Did not find it useful. What is it for?
    //protected function pExpr_ArrayItem(Expr\ArrayItem $node)
    //{
    //    $code = ($node->byRef ? '&' : '')
    //        . ($node->unpack ? '...' : '')
    //        . $this->p($node->value);

    //    if (isset($node->value) && $node->value instanceof Expr\FuncCall) {
    //        $code = $this->tokenize($code);
    //    }

    //    return (null !== $node->key ? $this->p($node->key) . ' => ' : '')
    //        . $code;
    //}

    protected function pExpr_Closure(Expr\Closure $node)
    {
        return $this->tokenize(parent::pExpr_Closure($node));
    }

    protected function pExpr_ArrowFunction(Expr\ArrowFunction $node)
    {
        return $this->tokenize(parent::pExpr_ArrowFunction($node));
    }

    protected function pExpr_New(Expr\New_ $node)
    {
        return $this->tokenize(parent::pExpr_New($node));
    }

    // XXX Can we get rid of `ReverseBlockMerge`
    //protected function pExpr_New(Expr\New_ $node) {
    //    if ($node->class instanceof Stmt\Class_) {
    //        $args = $node->args ? '(' . $this->pMaybeMultiline($node->args) . ')' : '';
    //        $code = 'new ' . $this->pClassCommon($node->class, $args);
    //        $token = '\'__' . md5($code) . '__\'';
    //        $this->options['builder']->closures[$token] = $code;
    //        return $token;
    //    }
    //    $code = 'new ' . $this->pNewVariable($node->class)
    //        . '(' . $this->pMaybeMultiline($node->args) . ')';
    //    if($this->pNewVariable($node->class) == 'ReverseBlockMerge'){
    //        return $code;
    //    }
    //    $token = '\'__' . md5($code) . '__\'';
    //    $this->options['builder']->closures[$token] = $code;
    //    return $token;
    //}

    protected function pStmt_ClassMethod(Stmt\ClassMethod $node)
    {
        return $this->tokenize(parent::pStmt_ClassMethod($node));
    }

    protected function pStmt_Function(Stmt\Function_ $node)
    {
        return $this->tokenize(parent::pStmt_Function($node));
    }

    protected function pScalar_MagicConst_Dir(MagicConst\Dir $node) {
        return "'{$this->options['THE_DIR']}'";
    }

    protected function pScalar_MagicConst_File(MagicConst\File $node) {
        return "'{$this->options['THE_FILE']}'";
    }

    private static array $tokens = [];

    protected function tokenize(string $code): string
    {
        if ($this->isBuildtime) {
            return $code;
        }

        $token = "'__" . md5($code) . "__'";
        static::$tokens[$token] = $code;

        return $token;
    }

    public static function resolveTokens(string $output): string
    {
        $limit = 10;
        while (preg_match('~\'__(\w+)__\'~', $output)) {
            // TODO will be fixed soon
            if (--$limit<1) {
                throw new \Exception('too much');
            }
            $output = strtr($output, static::$tokens);
        }

        return $output;
    }

    /**
     * XXX could these methods be converted to protected in php-parser?
     */
    protected function my_pMaybeMultiline(array $nodes, bool $trailingComma = false) {
        if (!$this->my_hasNodeWithComments($nodes)) {
            return $this->pCommaSeparated($nodes);
        } else {
            return $this->pCommaSeparatedMultiline($nodes, $trailingComma) . $this->nl;
        }
    }

    protected function my_hasNodeWithComments(array $nodes) {
        foreach ($nodes as $node) {
            if ($node && $node->getComments()) {
                return true;
            }
        }
        return false;
    }
}
