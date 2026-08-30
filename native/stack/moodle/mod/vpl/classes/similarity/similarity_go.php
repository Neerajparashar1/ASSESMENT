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
 * Go language similarity class
 *
 * @package mod_vpl
 * @copyright 2026 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token;
use mod_vpl\tokenizer\token_type;

/**
 * Go language similarity class.
 * @codeCoverageIgnore
 */
class similarity_go extends similarity_generic {
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
        '&^=' => '&^',
    ];

    /**
     * Constructor for the Go similarity.
     */
    public function __construct() {
        parent::__construct('golang');
    }

    /**
     * Normalizes the syntax of the given tokens.
     * Operator transformations:
     *   - Compound-assignment (+=, -=, *=, /=, %=, &=, |=, ^=) → expanded to "= <op>"
     *   - Shift-assignment (<<=, >>=) → expanded to "= <op>"
     *   - Bit-clear assignment (&^=) → expanded to "= &^"
     *   - Increment/decrement (++/--) → expanded to "= +"/"-"
     *   - Short variable declaration (:=) → canonical "="
     *   - Open brackets/parens ([, () dropped (only closers kept)
     *   - Trivial single-statement {}-blocks collapsed
     * Reserved-word normalisations:
     *   - Exit keyword  continue  → canonical "break"
     *   - "fallthrough" → canonical "break"  (both transfer control unconditionally)
     *   - "select" (channel-select construct) → canonical "if"
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
                    case ':=':
                        // Short variable declaration has the same structure as =.
                        $token->value = '=';
                        $ret[] = $token;
                        break;
                    default:
                        $ret[] = $token;
                }
            } else if ($token->type == token_type::RESERVED) {
                // Normalise semantically interchangeable reserved words so that
                // superficial rewrites do not reduce similarity.
                switch ($token->value) {
                    case 'continue':
                        // Loop-exit → canonical "break".
                        $token->value = 'break';
                        break;
                    case 'fallthrough':
                        // Unconditional control transfer → canonical "break".
                        $token->value = 'break';
                        break;
                    case 'select':
                        // Channel-select is structurally like switch/if.
                        $token->value = 'if';
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
