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
 * Abstract class for tokens used by exp4j to tokenize expressions
 */
abstract class Token {
    const TOKEN_NUMBER = 1;
    const TOKEN_OPERATOR = 2;
    const TOKEN_FUNCTION = 3;
    const TOKEN_PARENTHESES_OPEN = 4;
    const TOKEN_PARENTHESES_CLOSE = 5;
    const TOKEN_VARIABLE = 6;
    const TOKEN_SEPARATOR = 7;

    protected $type;

    public function __construct($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

}
