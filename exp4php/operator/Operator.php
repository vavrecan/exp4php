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
namespace exp4php\operator;

/**
 * Class representing operators that can be used in an expression
 */
abstract class Operator {
    /**
     * The precedence value for the addition operation
     */
    const PRECEDENCE_ADDITION = 500;
    /**
     * The precedence value for the subtraction operation
     */
    const PRECEDENCE_SUBTRACTION = 500;
    /**
     * The precedence value for the multiplication operation
     */
    const PRECEDENCE_MULTIPLICATION = 1000;
    /**
     * The precedence value for the division operation
     */
    const PRECEDENCE_DIVISION = 1000;
    /**
     * The precedence value for the modulo operation
     */
    const PRECEDENCE_MODULO = 1000;
    /**
     * The precedence value for the power operation
     */
    const PRECEDENCE_POWER = 10000;
    /**
     * The precedence value for the unary minus operation
     */
    const PRECEDENCE_UNARY_MINUS = 5000;
    /**
     * The precedence value for the unary plus operation
     */
    const PRECEDENCE_UNARY_PLUS = 5000;

    /**
     * The set of allowed operator chars
     */
    public static $ALLOWED_OPERATOR_CHARS = [ '+', '-', '*', '/',
            '%', '^', '!', '#', '§', '$', '&', ';', ':', '~', '<', '>', '|',
            '='];

    protected $numOperands;
    protected $leftAssociative;
    protected $symbol;
    protected $precedence;

    /**
     * Create a new operator for use in expressions
     * @param string $symbol the symbol of the operator
     * @param int $numberOfOperands the number of operands the operator takes (1 or 2)
     * @param boolean $leftAssociative set to true if the operator is left associative, false if it is right associative
     * @param string $precedence the precedence value of the operator
     */
    public function __construct($symbol, $numberOfOperands, $leftAssociative, $precedence) {
        // parent::__construct();
        $this->numOperands = $numberOfOperands;
        $this->leftAssociative = $leftAssociative;
        $this->symbol = $symbol;
        $this->precedence = $precedence;
    }

    /**
     * Check if a character is an allowed operator char
     * @param string $ch the char to check
     * @return true if the char is allowed an an operator symbol, false otherwise
     */
    public static  function isAllowedOperatorChar($ch) {
        return (in_array($ch, self::$ALLOWED_OPERATOR_CHARS));
    }

    /**
     * Check if the operator is left associative
     * @return true os the operator is left associative, false otherwise
     */
    public function isLeftAssociative() {
        return $this->leftAssociative;
    }

    /**
     * Check the precedence value for the operator
     * @return string the precedence value
     */
    public function getPrecedence() {
        return $this->precedence;
    }

    /**
     * Apply the operation on the given operands
     * @param array $args the operands for the operation
     * @return mixed the calculated result of the operation
     */
    public abstract function apply($args);

    /**
     * Get the operator symbol
     * @return string the symbol
     */
    public function getSymbol() {
        return $this->symbol;
    }

    /**
     * Get the number of operands
     * @return int the number of operands
     */
    public function getNumOperands() {
        return $this->numOperands;
    }
}
