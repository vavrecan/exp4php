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

use exp4php\ExpressionException;

class Operators {
    const INDEX_ADDITION = 0;
    const INDEX_SUBTRACTION = 1;
    const INDEX_MUTLIPLICATION = 2;
    const INDEX_DIVISION = 3;
    const INDEX_POWER = 4;
    const INDEX_MODULO = 5;
    const INDEX_UNARYMINUS = 6;
    const INDEX_UNARYPLUS = 7;

    private static $builtinOperators = null;

    private static function initOperators() {
        self::$builtinOperators[self::INDEX_ADDITION] = new AdditionOperator("+", 2, true, Operator::PRECEDENCE_ADDITION);
        self::$builtinOperators[self::INDEX_SUBTRACTION] = new SubtractionOperator("-", 2, true, Operator::PRECEDENCE_SUBTRACTION);
        self::$builtinOperators[self::INDEX_UNARYMINUS] = new UnaryMinusOperator("-", 1, false, Operator::PRECEDENCE_UNARY_MINUS);
        self::$builtinOperators[self::INDEX_UNARYPLUS] = new UnaryPlusOperator("+", 1, false, Operator::PRECEDENCE_UNARY_PLUS);
        self::$builtinOperators[self::INDEX_MUTLIPLICATION] = new MutliplicationOperator("*", 2, true, Operator::PRECEDENCE_MULTIPLICATION);
        self::$builtinOperators[self::INDEX_DIVISION] = new DivisionOperator("/", 2, true, Operator::PRECEDENCE_DIVISION);
        self::$builtinOperators[self::INDEX_POWER] = new PowerOperator("^", 2, false, Operator::PRECEDENCE_POWER);
        self::$builtinOperators[self::INDEX_MODULO] = new ModuloOperator("%", 2, true, Operator::PRECEDENCE_MODULO);
    }

    public static function getBuiltinOperator($symbol, $numArguments) {
        if (is_null(self::$builtinOperators))
            self::initOperators();

        switch($symbol) {
            case '+':
                if ($numArguments != 1) {
                    return self::$builtinOperators[self::INDEX_ADDITION];
                }else{
                    return self::$builtinOperators[self::INDEX_UNARYPLUS];
                }
            case '-':
                if ($numArguments != 1) {
                    return self::$builtinOperators[self::INDEX_SUBTRACTION];
                }else{
                    return self::$builtinOperators[self::INDEX_UNARYMINUS];
                }
            case '*':
                return self::$builtinOperators[self::INDEX_MUTLIPLICATION];
            case '/':
                return self::$builtinOperators[self::INDEX_DIVISION];
            case '^':
                return self::$builtinOperators[self::INDEX_POWER];
            case '%':
                return self::$builtinOperators[self::INDEX_MODULO];
            default:
                return null;
        }
    }
}

class AdditionOperator extends Operator {
    public function apply($args) {
        return $args[0] + $args[1];
    }
}

class SubtractionOperator extends Operator {
    public function apply($args) {
        return $args[0] - $args[1];
    }
}

class UnaryMinusOperator extends Operator {
    public function apply($args) {
        return -$args[0];
    }
}

class UnaryPlusOperator extends Operator {
    public function apply($args) {
        return $args[0];
    }
}

class MutliplicationOperator extends Operator {
    public function apply($args) {
        return $args[0] * $args[1];
    }
}

class DivisionOperator extends Operator {
    public function apply($args) {
        if ($args[1] == 0)
            throw new ExpressionException("Division by zero!");
        return $args[0] / $args[1];
    }
}

class PowerOperator extends Operator {
    public function apply($args) {
        return pow($args[0], $args[1]);
    }
}

class ModuloOperator extends Operator {
    public function apply($args) {
        if ($args[1] == 0)
            throw new ExpressionException("Division by zero!");
        return $args[0] % $args[1];
    }
}

