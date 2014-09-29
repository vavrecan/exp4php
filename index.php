<?php

require_once("exp4php/ExpressionBuilder.php");

class CustomFunction extends exp4php\func\Func {
    public function apply($args) {
        return -1;
    }
}

// setup
$e = new exp4php\ExpressionBuilder("sum(4,2,3,avg(5,3))+custom(2,q1)");
$e->variable("q1");
$e->func(new CustomFunction("custom"));
$build = $e->build();

// set variables and evaluate
$result = $build->setVariable("q1", 7)->evaluate();

var_dump($result);