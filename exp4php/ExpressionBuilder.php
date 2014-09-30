<?php
/*
 * Copyright 2014 Frank Asseg (original java exp4j library)
 * Copyright 2014 Marek Vavrecan (php port)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace exp4php;

require_once("Expression.php");

use exp4php\operator\Operator;
use exp4php\shuntingyard\ShuntingYard;

/**
 * Factory class for {@link Expression} instances. This class is the main API entrypoint. Users should create new
 * {@link Expression} instances using this factory class.
 */
class ExpressionBuilder {

    private $expression;
    private $userFunctions;
    private $userOperators;
    private $variableNames;

    /**
     * Create a new ExpressionBuilder instance and initialize it with a given expression string.
     * @param string $expression the expression to be parsed
     */
    public function __construct($expression) {
        if ($expression == null || strlen(trim($expression)) == 0) {
            throw new ExpressionException("Expression can not be empty");
        }
        $this->expression = $expression;
        $this->userOperators = [];
        $this->userFunctions = [];
        $this->variableNames = [];
    }

    /**
     * Add a {@link net.objecthunter.exp4j.function.Function} implementation available for use in the expression
     * @param function $function the custom {@link net.objecthunter.exp4j.function.Function} implementation that should be available for use in the expression.
     * @return self the ExpressionBuilder instance
     */
    public function func($function) {
        $this->userFunctions[$function->getName()] = $function;
        return $this;
    }

    public function funcs($functions) {
        foreach ($functions as $function)
            $this->func($function);
        return $this;
    }

    public function variable($variableName) {
        $this->variableNames[] = $variableName;
        return $this;
    }

    public function variables($variables) {
        foreach ($variables as $variableName)
            $this->variable($variableName);
        return $this;
    }

    /**
     * Add an {@link net.objecthunter.exp4j.operator.Operator} which should be available for use in the expression
     * @param string $operator the custom {@link net.objecthunter.exp4j.operator.Operator} to add
     * @return self the ExpressionBuilder instance
     */
    public function operator($operator) {
        $this->checkOperatorSymbol($operator);
        $this->userOperators[$operator->getSymbol()] = $operator;
        return $this;
    }

    public function operators($operators) {
        foreach ($operators as $operator)
            $this->operator($operator);
        return $this;
    }


    private function checkOperatorSymbol($op) {
        $name = $op->getSymbol();
        foreach (str_split($name) as $ch) {
            if (!Operator::isAllowedOperatorChar($ch)) {
                throw new ExpressionException("The operator symbol '" . $name . "' is invalid");
            }
        }
    }

    /**
     * Build the {@link Expression} instance using the custom operators and functions set.
     * @return Expression an {@link Expression} instance which can be used to evaluate the result of the expression
     */
    public function build() {
        if ($this->expression == null || strlen(trim($this->expression)) == 0) {
            throw new ExpressionException("The expression can not be empty");
        }
        return new Expression(ShuntingYard::convertToRPN($this->expression, $this->userFunctions, $this->userOperators, $this->variableNames),
                array_keys($this->userFunctions));
    }

}
