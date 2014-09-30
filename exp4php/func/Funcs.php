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

class Funcs {
    const INDEX_ABS = 5;
    const INDEX_SQRT = 13;
    const INDEX_AVG = 21;
    const INDEX_SUM = 22;

    private static $builtinFunctions = null;

    private static function initFunctions()
    {
        self::$builtinFunctions[self::INDEX_ABS] = new AbsFunc("abs");
        self::$builtinFunctions[self::INDEX_SQRT] = new SqrtFunc("sqrt");
        self::$builtinFunctions[self::INDEX_SUM] = new SumFunc("sum");
        self::$builtinFunctions[self::INDEX_AVG] = new AvgFunc("avg");
    }

    /**
     * Get the builtin function for a given name
     * @param string $name the name of the function
     * @return mixed Function instance
     */
    public static function getBuiltinFunction($name) {
        if (is_null(self::$builtinFunctions))
            self::initFunctions();

        switch($name) {
            case "abs":
                return self::$builtinFunctions[self::INDEX_ABS];
            case "sqrt":
                return self::$builtinFunctions[self::INDEX_SQRT];
            case "avg":
                return self::$builtinFunctions[self::INDEX_AVG];
            case "sum":
                return self::$builtinFunctions[self::INDEX_SUM];
            default:
                return null;
        }
    }
}

class AbsFunc extends Func {
    public function apply($args) {
        return abs($args[0]);
    }
}

class SqrtFunc extends Func {
    public function apply($args) {
        return sqrt($args[0]);
    }
}

class AvgFunc extends Func {
    public function apply($args) {
        return array_sum($args) / count($args);
    }
}

class SumFunc extends Func {
    public function apply($args) {
        return array_sum($args);
    }
}
