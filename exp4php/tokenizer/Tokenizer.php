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
namespace exp4php\tokenizer;

use exp4php\ExpressionException;
use exp4php\func\Funcs;
use exp4php\operator\Operator;
use exp4php\operator\Operators;

class Tokenizer {

    /** @var String */
    private $expression;
    private $expressionLength;

    /** @var Array as dictionary */
    private $userFunctions;

    /** @var Array as dictionary */
    private $userOperators;

    /** @var Array */
    private $variableNames;

    /** @var int */
    private $pos = 0;

    /** @var Token */
    private $lastToken;

    public function __construct($expression, $userFunctions, $userOperators, $variableNames) {
        $this->expression = trim($expression);
        $this->expressionLength = strlen($expression);
        $this->userFunctions = $userFunctions;
        $this->userOperators = $userOperators;
        $this->variableNames = $variableNames;
    }

    public function hasNext() {
        return strlen($this->expression) > $this->pos;
    }

    public function nextToken() {
        $ch = $this->expression{$this->pos};
        while (ctype_space($ch)) {
            $ch = $this->expression{++$this->pos};
        }
        if (ctype_digit($ch) || $ch == '.') {
            if ($this->lastToken != null &&
                    ($this->lastToken->getType() != Token::TOKEN_OPERATOR
                            && $this->lastToken->getType() != Token::TOKEN_PARENTHESES_OPEN
                            && $this->lastToken->getType() != Token::TOKEN_FUNCTION
                            && $this->lastToken->getType() != Token::TOKEN_SEPARATOR)) {
                // insert an implicit multiplication token
                $this->lastToken = new OperatorToken(Operators::getBuiltinOperator('*', 2));
                return $this->lastToken;
            }
            return $this->parseNumberToken($ch);
        } else if ($this->isArgumentSeparator($ch)) {
            return $this->parseArgumentSeparatorToken($ch);
        } else if ($this->isOpenParentheses($ch)) {
            if ($this->lastToken != null &&
                    ($this->lastToken->getType() != Token::TOKEN_OPERATOR
                            && $this->lastToken->getType() != Token::TOKEN_PARENTHESES_OPEN
                            && $this->lastToken->getType() != Token::TOKEN_FUNCTION
                            && $this->lastToken->getType() != Token::TOKEN_SEPARATOR)) {
                // insert an implicit multiplication token
                $this->lastToken = new OperatorToken(Operators::getBuiltinOperator('*', 2));
                return $this->lastToken;
            }
            return $this->parseParentheses(true);
        } else if ($this->isCloseParentheses($ch)) {
            return $this->parseParentheses(false);
        } else if (Operator::isAllowedOperatorChar($ch)) {
            return $this->parseOperatorToken($ch);
        } else if (ctype_alpha($ch) || $ch == '_') {
            // parse the name which can be a setVariable or a function
            if ($this->lastToken != null &&
                    ($this->lastToken->getType() != Token::TOKEN_OPERATOR
                            && $this->lastToken->getType() != Token::TOKEN_PARENTHESES_OPEN
                            && $this->lastToken->getType() != Token::TOKEN_FUNCTION
                            && $this->lastToken->getType() != Token::TOKEN_SEPARATOR)) {
                // insert an implicit multiplication token
                $this->lastToken = new OperatorToken(Operators::getBuiltinOperator('*', 2));
                return $this->lastToken;
            }
            return $this->parseFunctionOrVariable();

        }
        throw new ExpressionException("Unable to parse char '" . $ch . "' (Code:" . (int)$ch . ") at [" . $this->pos . "]");
    }

    private function parseArgumentSeparatorToken($ch) {
        $this->pos++;
        $this->lastToken = new ArgumentSeparatorToken();
        return $this->lastToken;
    }

    private function isArgumentSeparator($ch) {
        return $ch == ',';
    }

    private function parseParentheses($open) {
        if ($open) {
            $this->lastToken = new OpenParenthesesToken();
        } else {
            $this->lastToken = new CloseParenthesesToken();
        }
        $this->pos++;
        return $this->lastToken;
    }

    private function isOpenParentheses($ch) {
        return $ch == '(' || $ch == '{' || $ch == '[';
    }

    private function isCloseParentheses($ch) {
        return $ch == ')' || $ch == '}' || $ch == ']';
    }

    private function parseFunctionOrVariable() {
        $offset = $this->pos;
        $lastValidLen = 1;
        $lastValidToken = null;
        $len = 1;
        if ($this->isEndOfExpression($offset)) {
            $this->pos++;
        }
        while (!$this->isEndOfExpression($offset + $len - 1) &&
                (ctype_alpha($this->expression{$offset + $len - 1}) ||
                        ctype_digit($this->expression{$offset + $len - 1}) ||
                        $this->expression{$offset + $len - 1} == '_')) {
            $name = substr($this->expression, $offset, $len);
            if ($this->variableNames != null && in_array($name, $this->variableNames)) {
                $lastValidLen = $len;
                $lastValidToken = new VariableToken($name);
            } else {
                $f = $this->getFunction($name);
                if ($f != null) {
                    $lastValidLen = $len;
                    $lastValidToken = new FunctionToken($f);
                }
            }
            $len++;
        }
        if ($lastValidToken == null) {
            throw new ExpressionException("Unable to parse setVariable or function starting at pos " . $this->pos . " in expression '" . $this->expression . "'");
        }
        $this->pos += $lastValidLen;
        $this->lastToken = $lastValidToken;
        return $this->lastToken;
    }

    private function getFunction($name) {
        $f = null;
        if ($this->userFunctions != null && array_key_exists($name, $this->userFunctions)) {
            $f = $this->userFunctions[$name];
        }
        if ($f == null) {
            $f = Funcs::getBuiltinFunction($name);
        }
        return $f;
    }

    private function parseName() {
        // parse the name of a function or a setVariable
        $offset = $this->pos;
        $len = 1;
        if ($this->isEndOfExpression($offset)) {
            $this->pos++;
        }
        while (!$this->isEndOfExpression($offset + $len) &&
                (ctype_alpha($this->expression{$offset + $len - 1}) ||
                    ctype_digit($this->expression{$offset + $len - 1}) ||
                    $this->expression{$offset + $len - 1} == '_')) {
            $len++;
        }
        $this->pos += $len;
        return substr($this->expression, $offset, $len);
    }

    private function parseOperatorToken($firstChar) {
        $offset = $this->pos;
        $len = 1;

        $symbol = "";
        $lastValid = null;
        $symbol .= $firstChar;

        while (!$this->isEndOfExpression($offset + $len)  && Operator::isAllowedOperatorChar($this->expression{$offset + $len})) {
            $symbol .= $this->expression{$offset + $len++};
        }

        while (strlen($symbol) > 0) {
            $op = $this->getOperator($symbol);
            if ($op == null) {
                $symbol = substr($symbol, 0, strlen($symbol) - 1);
            }else{
                $lastValid = $op;
                break;
            }
        }

        $this->pos += strlen($symbol);
        $this->lastToken = new OperatorToken($lastValid);
        return $this->lastToken;
    }

    private function getOperator($symbol) {
        $op = null;
        if ($this->userOperators != null && array_key_exists($symbol, $this->userOperators)) {
            $op = $this->userOperators[$symbol];
        }
        if ($op == null && strlen($symbol) == 1) {
            $argc = ($this->lastToken == null ||
                        $this->lastToken->getType() == Token::TOKEN_OPERATOR ||
                        $this->lastToken->getType() == Token::TOKEN_PARENTHESES_OPEN) ? 1 : 2;
            $op = Operators::getBuiltinOperator($symbol{0}, $argc);
        }
        return $op;
    }

    private function parseNumberToken($firstChar) {
        $offset = $this->pos;
        $len = 1;
        $this->pos++;

        if ($this->isEndOfExpression($offset + $len)) {
            $this->lastToken = new NumberToken($firstChar);
            return $this->lastToken;
        }
        while (!$this->isEndOfExpression($offset + $len) &&
            $this->isNumeric($this->expression{$offset + $len}, $this->expression{$offset + $len - 1} == 'e' ||
                $this->expression{$offset + $len - 1} == 'E')) {
            $len++;
            $this->pos++;
        }
        // check if the e is at the end
        if ($this->expression{$offset + $len - 1} == 'e' || $this->expression{$offset + $len - 1} == 'E') {
            // since the e is at the end it's not part of the number and a rollback is necessary
            $len--;
            $this->pos--;
        }

        $number = substr($this->expression, $offset, $len);
        $this->lastToken = new NumberToken($number);
        return $this->lastToken;
    }

    private function isNumeric($ch, $lastCharE) {
        return ctype_digit($ch) || $ch == '.' || $ch == 'e' || $ch == 'E' ||
                ($lastCharE && ($ch == '-' || $ch == '+'));
    }

    private function isEndOfExpression($offset) {
        return $this->expressionLength <= $offset;
    }
}
