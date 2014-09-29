<?php

require_once("exp4php/ExpressionBuilder.php");
require_once("exp4php/func/Func.php");

use exp4php\ExpressionBuilder;
use exp4php\func\Func;

class CustomFunction extends Func {
    public function apply($args) {
        return -1;
    }
}

// setup
$e = new ExpressionBuilder("sum(4,2,3,avg(5,3))+custom(2,q1)");
$e->variable("q1");
$e->func(new CustomFunction("custom"));
$build = $e->build();

// set variables and evaluate
$result = $build->setVariable("q1", 7)->evaluate();

var_dump($result);