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
namespace exp4php\shuntingyard;

use exp4php\ExpressionException;
use exp4php\Stack;
use exp4php\tokenizer\Token;
use exp4php\tokenizer\Tokenizer;

/**
 * Shunting yard implementation to convert infix to reverse polish notation
 */
class ShuntingYard {

    /**
     * Convert a Set of tokens from infix to reverse polish notation
     * @param string $expression the expression to convert
     * @param array $userFunctions the custom functions used
     * @param array $userOperators the custom operators used
     * @param array $variableNames
     * @return array of tokens containing the result
     */
    public static function convertToRPN($expression, $userFunctions, $userOperators, $variableNames){
        $stack = new Stack();
        $functionStack = new Stack();

        $output = [];

        $tokenizer = new Tokenizer($expression, $userFunctions, $userOperators, $variableNames);
        while ($tokenizer->hasNext()) {
            $token = $tokenizer->nextToken();

            switch ($token->getType()) {
            case Token::TOKEN_NUMBER:
            case Token::TOKEN_VARIABLE:
                $output[] = ($token);
                break;
            case Token::TOKEN_FUNCTION:
                $stack->add($token);
                $functionStack->add($token);
                break;
            case Token::TOKEN_SEPARATOR:
                // store argument count, easier than easy
                if (!$functionStack->isEmpty()) {
                    $functionToken = $functionStack->peek();
                    $functionToken->incPassedArgumentCount();
                }

                while (!$stack->isEmpty() && $stack->peek()->getType() != Token::TOKEN_PARENTHESES_OPEN) {
                    $output[] = $stack->pop();
                }
                if ($stack->isEmpty() || $stack->peek()->getType() != Token::TOKEN_PARENTHESES_OPEN) {
                    throw new ExpressionException("Misplaced function separator ',' or mismatched parentheses");
                }
                break;
            case Token::TOKEN_OPERATOR:
                while (!$stack->isEmpty() && $stack->peek()->getType() == Token::TOKEN_OPERATOR) {
                    $o1 = $token;
                    $o2 = $stack->peek();
                    if ($o1->getOperator()->getNumOperands() == 1 && $o2->getOperator()->getNumOperands() == 2) {
                        break;
                    } else if (($o1->getOperator()->isLeftAssociative() && $o1->getOperator()->getPrecedence() <= $o2->getOperator()->getPrecedence())
                            || ($o1->getOperator()->getPrecedence() < $o2->getOperator()->getPrecedence())) {
                        $output[] = $stack->pop();
                    }else {
                        break;
                    }
                }
                $stack->push($token);
                break;
            case Token::TOKEN_PARENTHESES_OPEN:
                $stack->push($token);
                break;
            case Token::TOKEN_PARENTHESES_CLOSE:
                while ($stack->peek()->getType() != Token::TOKEN_PARENTHESES_OPEN) {
                    $output[] = $stack->pop();
                }
                $stack->pop();
                if (!$stack->isEmpty() && $stack->peek()->getType() == Token::TOKEN_FUNCTION) {
                    $output[] = $stack->pop();
                    $functionStack->pop();
                }
                break;
            default:
                throw new ExpressionException("Unknown Token type encountered. This should not happen");
            }
        }
        while (!$stack->isEmpty()) {
            $t = $stack->pop();
            if ($t->getType() == Token::TOKEN_PARENTHESES_CLOSE || $t->getType() == Token::TOKEN_PARENTHESES_OPEN) {
                throw new ExpressionException("Mismatched parentheses detected. Please check the expression");
            } else {
                $output[] = $t;
            }
        }

        return $output;
    }
}
