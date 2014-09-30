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

require_once("ExpressionException.php");
require_once("Stack.php");
require_once("ValidationResult.php");
require_once("operator/Operator.php");
require_once("operator/Operators.php");
require_once("func/Func.php");
require_once("func/Funcs.php");
require_once("shuntingyard/ShuntingYard.php");
require_once("tokenizer/Token.php");
require_once("tokenizer/Tokenizer.php");
require_once("tokenizer/ArgumentSeparatorToken.php");
require_once("tokenizer/CloseParenthesesToken.php");
require_once("tokenizer/FunctionToken.php");
require_once("tokenizer/NumberToken.php");
require_once("tokenizer/OpenParenthesesToken.php");
require_once("tokenizer/OperatorToken.php");
require_once("tokenizer/VariableToken.php");

use exp4php\tokenizer\FunctionToken;
use exp4php\tokenizer\Token;

class Expression {

    private $tokens;
    private $variables;
    private $userFunctionNames;

    public function __construct($tokens, $userFunctionNames = []) {
        $this->tokens = $tokens;
        $this->variables = [];
        $this->userFunctionNames = $userFunctionNames;
    }

    public function setVariable($name, $value) {
        $this->checkVariableName($name);
        $this->variables[$name] = $value;
        return $this;
    }

    public function checkVariableName($name) {
        if (array_key_exists($name, $this->userFunctionNames)) {
            throw new ExpressionException("The setVariable name '" . $name . "' is invalid. Since there exists a function with the same name");
        }
    }

    public function setVariables($variables) {
        foreach ($variables as $key => $value) {
            $this->setVariable($key, $value);
        }
        return $this;
    }

    public function validate($checkVariablesSet = true) {
        $errors = [];
        if ($checkVariablesSet) {
            /* check that all vars have a value set */
            foreach ($this->tokens as $t) {
                if ($t->getType() == Token::TOKEN_VARIABLE) {
                    $var = $t->getName();
                    if (!array_key_exists($var, $this->variables)) {
                        $errors[] = ("The setVariable '" . $var . "' has not been set");
                    }
                }
            }
        }

        /* Check if the number of operands, functions and operators match.
           The idea is to increment a counter for operands and decrease it for operators.
           When a function occurs the number of available arguments has to be greater
           than or equals to the function's expected number of arguments.
           The count has to be larger than 1 at all times and exactly 1 after all tokens
           have been processed */
        $count = 0;
        foreach ($this->tokens as $tok) {
            switch ($tok->getType()) {
                case Token::TOKEN_NUMBER:
                case Token::TOKEN_VARIABLE:
                    $count++;
                    break;
                case Token::TOKEN_FUNCTION:
                    $func = $tok->getFunction();
                    if ($func->getNumArguments() > $count) {
                        $errors[] = ("Not enough arguments for '" . $func->getName() . "'");
                    }
                    break;
                case Token::TOKEN_OPERATOR:
                    $op = $tok->getOperator();
                    if ($op->getNumOperands() == 2) {
                        $count--;
                    }
                    break;
            }
            if ($count < 1) {
                $errors[] = ("Too many operators");
                return new ValidationResult(false, $errors);
            }
        }
        if ($count > 1) {
            $errors[] = ("Too many operands");
        }
        return count($errors) == 0 ? ValidationResult::constructSuccess() : new ValidationResult(false, $errors);

    }

    public function evaluate() {
        $output = new Stack();

        for ($i = 0; $i < count($this->tokens); $i++) {
            $t = $this->tokens[$i];
            if ($t->getType() == Token::TOKEN_NUMBER) {
                $output->push($t->getValue());
            } else if ($t->getType() == Token::TOKEN_VARIABLE) {
                $name = $t->getName();

                if (!isset($this->variables[$name])) {
                    throw new ExpressionException("No value has been set for the setVariable '" . $name . "'.");
                }

                $value = $this->variables[$name];
                $output->push($value);
            } else if ($t->getType() == Token::TOKEN_OPERATOR) {
                $op = $t;
                if ($output->size() < $op->getOperator()->getNumOperands()) {
                    throw new ExpressionException("Invalid number of operands available");
                }
                if ($op->getOperator()->getNumOperands() == 2) {
                    /* pop the operands and push the result of the operation */
                    $rightArg = $output->pop();
                    $leftArg = $output->pop();
                    $output->push($op->getOperator()->apply([$leftArg, $rightArg]));
                } else if ($op->getOperator()->getNumOperands() == 1) {
                    /* pop the operand and push the result of the operation */
                    $arg = $output->pop();
                    $output->push($op->getOperator()->apply([$arg]));
                }
            } else if ($t->getType() == Token::TOKEN_FUNCTION) {
                /** @var FunctionToken $func */
                $func = $t;

                if ($func->getFunction()->getNumArguments() > $func->getPassedArgumentCount()){
                    throw new ExpressionException("Function " . $func->getFunction()->getName() . " requires at least " . $func->getFunction()->getNumArguments() . " arguments (" . $func->getPassedArgumentCount() . " passed)");
                }

                if ($output->size() < $func->getFunction()->getNumArguments()) {
                    throw new ExpressionException("Invalid number of arguments available");
                }

                /* collect the arguments from the stack */
                $args = [];
                for ($j = 0; $j < $func->getPassedArgumentCount(); $j++) {
                    $args[$j] = $output->pop();
                }
                $output->push($func->getFunction()->apply($this->reverseInPlace($args)));
            }
        }

        if ($output->size() > 1) {
            throw new ExpressionException("Invalid number of items on the output queue. Might be caused by an invalid number of arguments for a function.");
        }
        return $output->pop();
    }

    private function reverseInPlace($args) {
        return array_reverse($args);
    }
}
