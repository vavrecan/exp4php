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
namespace exp4php\func;
use exp4php\ExpressionException;

/**
 * A class representing a Function which can be used in an expression
 */
abstract class Func {

    protected $name;
    protected $numArguments;

    /**
     * Create a new Function with a given name and number of arguments
     * @param string $name the name of the Function
     * @param int $numArguments the number of arguments the function takes
     */
    public function __construct($name, $numArguments = 1) {
        if ($numArguments < 0) {
            throw new ExpressionException("The number of function arguments can not be less than 0 for '" . $name . "'");
        }

        if (!$this->isValidFunctionName($name)) {
            throw new ExpressionException("The function name '" . $name  . "' is invalid");
        }

        $this->name = $name;
        $this->numArguments = $numArguments;
    }

    /**
     * Get the name of the Function
     * @return string the name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the number of arguments for this function
     * @return int the number of arguments
     */
    public function getNumArguments() {
        return $this->numArguments;
    }

    /**
     * Method that does the actual calculation of the function value given the arguments
     * @param mixed $args the set of arguments used for calculating the function
     * @return mixed the result of the function evaluation
     */
    public abstract function apply($args);

    /**
     * Get the set of characters which are allowed for use in Function names.
     * @return array the set of characters allowed
     */
    public static function getAllowedFunctionCharacters() {
        $chars = [];
        $count = 0;
        for ($i = 65; $i < 91; $i++) {
            $chars[$count++] = chr($i);
        }
        for ($i = 97; $i < 123; $i++) {
            $chars[$count++] = chr($i);
        }
        $chars[$count] = '_';
        return $chars;
    }

    public static function isValidFunctionName($name) {
        if ($name == null)  {
            return false;
        }

        $size = strlen($name);

        if ($size == 0) {
            return false;
        }

        for ($i = 0; $i < $size; $i++) {
            $c = ord($name{$i});
            if ($c == 95) {
                continue;
            }
            if ($c > 47 && $c < 58) {
                if ($i == 0) {
                    return false;
                }else {
                    continue;
                }
            }
            if ($c > 96 && $c < 123) {
                continue;
            }
            if ($c > 64 && $c < 91) {
                continue;
            }
            return false;
        }
        return true;
    }
}
