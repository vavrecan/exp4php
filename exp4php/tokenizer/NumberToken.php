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
 * Represents a number in the expression
 */
class NumberToken extends Token {
    private $value;

    /**
     * Create a new instance
     * @param double value the value of the number
     */
    public function __construct($value) {
        parent::__construct(Token::TOKEN_NUMBER);
        $this->value = $value;
    }

    /**
     * Get the value of the number
     * @return double the value
     */
    public function getValue() {
        return $this->value;
    }
}
