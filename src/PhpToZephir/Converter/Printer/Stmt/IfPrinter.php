<?php

namespace PhpToZephir\Converter\Printer\Stmt;

use PhpToZephir\Converter\Dispatcher;
use PhpToZephir\Logger;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;
use PhpParser\Node\Scalar\String;
use PhpToZephir\converter\Manipulator\AssignManipulator;

class IfPrinter
{
    /**
     * @var Dispatcher
     */
    private $dispatcher = null;
    /**
     * @var Logger
     */
    private $logger = null;
    /**
     * @var AssignManipulator
     */
    private $assignManipulator = null;

    /**
     * @param Dispatcher $dispatcher
     * @param Logger $logger
     */
    public function __construct(Dispatcher $dispatcher, Logger $logger, AssignManipulator $assignManipulator)
    {
        $this->dispatcher = $dispatcher;
        $this->logger     = $logger;
        $this->assignManipulator = $assignManipulator;
    }

    public static function getType()
    {
        return "pStmt_If";
    }

    public function convert(Stmt\If_ $node)
    {
        $this->logger->trace(__METHOD__ . ' ' . __LINE__, $node, $this->dispatcher->getMetadata()->getFullQualifiedNameClass());
        $collected = $this->assignManipulator->collectAssignInCondition($node->cond);
        $node->cond = $this->assignManipulator->transformAssignInConditionTest($node->cond);

        if (empty($node->stmts)) {
            $node->stmts = array(new Stmt\Echo_(array(new Scalar\String("not allowed"))));
            $this->logger->logNode('Empty if not allowed, add "echo not allowed"', $node, $this->fullClass);
        }

        return implode(";\n", $collected['extracted']) . "\n" .
               'if ' . $this->dispatcher->p($node->cond) . ' {'
             . $this->dispatcher->pStmts($node->stmts) . "\n" . '}'
             . $this->dispatcher->implodeElseIfs($node);
    }
}