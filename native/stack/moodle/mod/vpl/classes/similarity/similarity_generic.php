<?php
// This file is part of VPL for Moodle - http://vpl.dis.ulpgc.es/
//
// VPL for Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// VPL for Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with VPL for Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Generic similarity class based on a tokenizer
 *
 * @package mod_vpl
 * @copyright 2022 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\tokenizer_factory;

/**
 * Generic similarity class based on a tokenizer.
 * @codeCoverageIgnore
 */
class similarity_generic extends similarity_base {
    /**
     * @var int $lasttypenumber Last type number assigned to a tokenizer dynamicaly.
     * This is used to ensure that each tokenizer class has a unique type number.
     */
    private static int $lasttypenumber = 50;

    /**
     * @var array $typenumbers Cache for type numbers of language names
     */
    private static array $typenumbers = [];

    /**
     * @var string Language name used to obtain the tokenizer for this similarity class.
     */
    private string $langname;

    /**
     * @var int $typenumber Type number for this similarity class.
     * This is used to identify the similarity type.
     */
    private int $typenumber;

    /**
     * Constructor for the similarity_generic class.
     * It initializes the language name and assigns a type number.
     * @param string $langname The language name used to obtain the tokenizer.
     */
    public function __construct(string $langname) {
        $this->langname = $langname;
        $this->typenumber = self::get_type_number($langname);
    }

    /**
     * Get the type number for this similarity class.
     * This number is used to identify the similarity type.
     */
    public function get_type() {
        return $this->typenumber;
    }

    /**
     * Get the tokenizer instance for this similarity class.
     * This method uses the tokenizer factory to create
     * an instance of the specified tokenizer class.
     *
     * @return \mod_vpl\tokenizer\tokenizer_base
     */
    public function get_tokenizer() {
        return tokenizer_factory::get($this->langname);
    }

    /**
     * Get the type number for a given language name.
     * This method ensures that each language name
     * has a unique type number.
     * @param string $langname The language name.
     * @return int The type number for the language.
     */
    private static function get_type_number($langname) {
        if (!isset(self::$typenumbers[$langname])) {
            self::$typenumbers[$langname] = self::$lasttypenumber++;
        }

        return self::$typenumbers[$langname];
    }

    /**
     * Expand the operator by replicating the LHS expression.
     *
     * Used by C/C++ normalization to transform compound-assignment operators
     * like `x += y` into `x = x + y`. Copies all tokens from $from to the
     * current end of $array (the instruction so far) so that the LHS expression
     * is duplicated after the injected '=' token.
     *
     * @param array $array The array of tokens being built (already contains '=' token).
     * @param int $from Index of the start of the current instruction; updated to
     *                  point past the end of the expanded tokens on return.
     */
    public static function expand_operator(array &$array, int &$from): void {
        $last = count($array) - 1; // Array already has the '=' token appended.
        for ($i = $from; $i < $last; $i++) { // Replicate from instruction start to '='.
            $array[] = $array[$i];
        }
        $from = count($array) + 1;
    }

    /**
     * Try to expand a compound-assignment or increment/decrement operator.
     *
     * If the token's value matches a key in $compoundops, the method:
     * 1. Appends a clone of the token with value '=' to $ret.
     * 2. If $posiniinst is not null, calls expand_operator() to replicate
     *    the LHS expression (C/C++ style full expansion).
     * 3. Sets the token's value to the bare operator from $compoundops.
     * 4. Appends the modified token to $ret.
     *
     * @param token $token The current operator token (may be modified in place).
     * @param array $compoundops Map of compound operator => bare operator,
     *                           e.g. ['+=' => '+', '-=' => '-', '++' => '+'].
     * @param array $ret The output token array (passed by reference).
     * @param int $posiniinst Position of the start of the current instruction
     *                        in $ret; when not -1, expand_operator is called
     *                        to replicate the LHS (C/C++ mode). Pass -1 to
     *                        skip LHS replication (simple mode).
     * @return bool True if the token was handled (compound-assignment expanded),
     *              false if the token's value was not found in $compoundops.
     */
    public static function expand_compound_assignment(
        $token,
        array $compoundops,
        array &$ret,
        int &$posiniinst = -1
    ): bool {
        if (!isset($compoundops[$token->value])) {
            return false;
        }
        $ret[] = self::clone_token($token, '=');
        if ($posiniinst !== -1) {
            self::expand_operator($ret, $posiniinst);
        }
        $token->value = $compoundops[$token->value];
        $ret[] = $token;
        return true;
    }
}
