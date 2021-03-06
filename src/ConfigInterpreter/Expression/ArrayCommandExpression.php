<?php

namespace SilenceDis\MultiSourceMapper\ConfigInterpreter\Expression;

use SilenceDis\MultiSourceMapper\MsmInterface\ConfigInterpreter\CommandResolver\CommandResolverInterface;
use SilenceDis\MultiSourceMapper\MsmInterface\ConfigInterpreter\CommandResolver\Exception\CommandResolverExceptionInterface;
use SilenceDis\MultiSourceMapper\MsmInterface\ConfigInterpreter\InterpreterContextInterface;

/**
 * Class ArrayCommandExpression
 *
 * @author Yurii Slobodeniuk <silencedis@gmail.com>
 */
class ArrayCommandExpression extends AbstractCommandExpression
{
    /**
     * @var array
     */
    private $expressionValue;
    
    /**
     * ArrayCommandExpression constructor.
     *
     * @param array $array
     * @param CommandResolverInterface $commandResolver
     */
    public function __construct(array $array, CommandResolverInterface $commandResolver)
    {
        $this->expressionValue = $array;
        parent::__construct($commandResolver);
    }
    
    /**
     * @param InterpreterContextInterface $context
     *
     * @throws CommandResolverExceptionInterface
     */
    public function interpret(InterpreterContextInterface $context)
    {
        $interpretedArray = [];
        
        foreach ($this->expressionValue as $key => $expression) {
            $expression->interpret($context);
            $interpretedArray[$key] = $context->lookup($expression);
        }
        
        $result = $this->runCommand($interpretedArray);
        
        $context->replace($this, $result);
    }
    
    /**
     * @param array $commandConfig
     *
     * @return mixed
     * @throws CommandResolverExceptionInterface
     */
    private function runCommand(array $commandConfig)
    {
        $command = $this->getCommandResolver()->resolve($commandConfig);
        
        return $command->execute();
    }
}
