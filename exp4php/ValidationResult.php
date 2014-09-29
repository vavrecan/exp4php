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
namespace exp4php;

/**
 * Contains the validation result for a given {@link Expression}
 */
class ValidationResult {
    private $valid;
    private $errors;

    /**
     * Create a new instance
     * @param $valid boolean Whether the validation of the expression was successful
     * @param $errors array The list of errors returned if the validation was unsuccessful
     */
    public function __construct($valid, $errors) {
        $this->valid = $valid;
        $this->errors = $errors;
    }

    /**
     * Check if an expression has been validated successfully
     * @return true if the validation was successful, false otherwise
     */
    public function isValid() {
        return $this->valid;
    }

    /**
     * Get the list of errors describing the issues while validating the expression
     * @return array The List of errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * A static class representing a successful validation result
     */
    public static function constructSuccess() {
        return new ValidationResult(true, null);
    }
}
