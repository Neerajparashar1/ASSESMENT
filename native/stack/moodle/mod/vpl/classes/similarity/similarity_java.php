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
 * Java language similarity class
 *
 * @package mod_vpl
 * @copyright 2012 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token;
use mod_vpl\tokenizer\token_type;

/**
 * Java language similarity class.
 * Inherits expand_operator from similarity_c; replaces sintax_normalize
 * with Java-specific simplifications (no pointer arithmetic, no scope ::).
 * @codeCoverageIgnore
 */
class similarity_java extends similarity_c {
    /**
     * Compound-assignment and increment/decrement operator map.
     * Maps each compound operator to its bare form.
     */
    protected const COMPOUND_OPS = [
        '++' => '+',
        '--' => '-',
        '+=' => '+',
        '-=' => '-',
        '*=' => '*',
        '/=' => '/',
        '%=' => '%',
        '&=' => '&',
        '|=' => '|',
        '^=' => '^',
        '<<=' => '<<',
        '>>=' => '>>',
        '>>>=' => '>>>',
    ];

    /**
     * Constructor for the Java similarity.
     */
    public function __construct() {
        // Skip similarity_c constructor; call generic directly with 'java'.
        similarity_generic::__construct('java');
    }

    /**
     * Normalizes the syntax of the given tokens.
     * Operator transformations:
     *   - Compound-assignment (+=, -=, *=, /=, %=, &=, |=, ^=, <<=, >>=, >>>=) → expanded to "= <op>"
     *   - Increment/decrement (++/--) → expanded to "= <op>"
     *   - Qualified member access (this.) and scope (::) dropped
     *   - Trivial single-statement {}-blocks collapsed
     * Reserved-word normalisations:
     *   - Loop keywords  for / while / do  → canonical "while"
     *   - Exit keywords  break / continue  → canonical "break"
     * Non-operator/non-reserved tokens are passed through unchanged;
     * type-based filtering is left to get_fingerprint_types.
     *
     * @param array $tokens The tokens to normalize.
     * @return array The normalized tokens.
     */
    public function sintax_normalize(&$tokens): array {
        $openbrace = false;
        $nsemicolon = 0;
        $ret = [];
        $prev = new token(token_type::IDENTIFIER, '', 0);
        foreach ($tokens as $token) {
            if ($token->type == token_type::OPERATOR) {
                if (self::expand_compound_assignment($token, static::COMPOUND_OPS, $ret)) {
                    $prev = $token;
                    continue;
                }
                switch ($token->value) {
                    case '[':
                        // Only add ].
                        break;
                    case '(':
                        // Only add ).
                        break;
                    case '{':
                        // Only add }.
                        $nsemicolon = 0;
                        $openbrace = true;
                        break;
                    case '}':
                        // Remove unneeded {}.
                        if (!($openbrace && $nsemicolon < 2)) {
                            $ret[] = $token;
                        }
                        $openbrace = false;
                        break;
                    case ';':
                        $nsemicolon++;
                        $ret[] = $token;
                        break;
                    case '.':
                        // Drop "this." qualified access; keep all other "." operators.
                        if ($prev->value === 'this') {
                            break;
                        }
                        $ret[] = $token;
                        break;
                    case '::':
                        break;
                    default:
                        $ret[] = $token;
                }
            } else if ($token->type == token_type::RESERVED) {
                // Normalise semantically interchangeable reserved words so that
                // superficial rewrites (e.g. for → while) do not reduce similarity.
                switch ($token->value) {
                    case 'for':
                    case 'do':
                        // All loop constructs → canonical "while".
                        $token->value = 'while';
                        break;
                    case 'continue':
                        // Loop-exit variants → canonical "break".
                        $token->value = 'break';
                        break;
                }
                $ret[] = $token;
            } else {
                $ret[] = $token;
            }
            $prev = $token;
        }
        $tokens = $ret;
        return $ret;
    }
}
