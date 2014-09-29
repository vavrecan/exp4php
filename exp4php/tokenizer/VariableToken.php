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
 * represents a setVariable used in an expression
 */
class VariableToken extends Token {
    private $name;

    /**
     * Get the name of the setVariable
     * @return string the name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Create a new instance
     * @param $name string name the name of the setVariable
     */
    public function __construct($name) {
        parent::__construct(self::TOKEN_VARIABLE);
        $this->name = $name;
    }
}
