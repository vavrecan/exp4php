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

/**
 * Represents an operator used in expressions
 */
class OperatorToken extends Token {
    private $operator;

    /**
     * Create a new instance
     * @param string $op the operator
     */
    public function __construct($op) {
        parent::__construct(Token::TOKEN_OPERATOR);
        if ($op == null) {
            throw new \InvalidArgumentException("Operator is unknown for token.");
        }
        $this->operator = $op;
    }

    /**
     * Get the operator for that token
     * @return string the operator
     */
    public function getOperator() {
        return $this->operator;
    }
}
