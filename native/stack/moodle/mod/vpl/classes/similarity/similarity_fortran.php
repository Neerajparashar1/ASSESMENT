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
 * Fortran language similarity class
 *
 * @package mod_vpl
 * @copyright 2026 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token_type;

/**
 * Fortran language similarity class.
 * @codeCoverageIgnore
 */
class similarity_fortran extends similarity_generic {
    /**
     * Constructor for the Fortran similarity.
     */
    public function __construct() {
        parent::__construct('fortran');
    }

    /**
     * Normalizes the syntax of the given tokens.
     * Fortran is case-insensitive, so all values are lowercased.
     * Operator transformations:
     *   - Open bracket/paren/brace ([, (, {) dropped; closers kept
     *   - .eq. → "==", .ne. / /= → "!=", .gt. → ">", .ge. → ">="
     *   - .lt. → "<", .le. → "<="
     *   - .eqv. → "=="  (logical equivalence same structure as equality)
     *   - .neqv. → "!="  (logical non-equivalence)
     *   - .and. → "&&", .or. → "||", .not. → "!"
     * Reserved-word normalisations (case-insensitive):
     *   - Loop keywords  do / forall  → canonical "while"
     *   - Exit keywords  cycle / exit  → canonical "break"
     *   - "elseif" / "elsewhere"  → canonical "if"
     *   - "select" (select-case construct) → canonical "if"
     * Note: Fortran's CONTINUE is a no-op label marker, not a loop-control keyword.
     * Non-operator/non-reserved tokens are passed through unchanged;
     * type-based filtering is left to get_fingerprint_types.
     *
     * @param array $tokens The tokens to normalize.
     * @return array The normalized tokens.
     */
    public function sintax_normalize(&$tokens): array {
        $ret = [];
        foreach ($tokens as $token) {
            if ($token->type == token_type::OPERATOR) {
                // Fortran is case-insensitive; lowercase operator values too.
                $opval = strtolower($token->value);
                switch ($opval) {
                    case '(':
                        // Only add ).
                        break;
                    case '[':
                        // Only add ].
                        break;
                    case '{':
                        // Only add }.
                        break;
                    case '.eq.':
                        $token->value = '==';
                        $ret[] = $token;
                        break;
                    case '.ne.':
                    case '/=':
                        // Both are Fortran not-equal → canonical !=.
                        $token->value = '!=';
                        $ret[] = $token;
                        break;
                    case '.gt.':
                        $token->value = '>';
                        $ret[] = $token;
                        break;
                    case '.ge.':
                        $token->value = '>=';
                        $ret[] = $token;
                        break;
                    case '.lt.':
                        $token->value = '<';
                        $ret[] = $token;
                        break;
                    case '.le.':
                        $token->value = '<=';
                        $ret[] = $token;
                        break;
                    case '.eqv.':
                        // Logical equivalence same structure as ==.
                        $token->value = '==';
                        $ret[] = $token;
                        break;
                    case '.neqv.':
                        // Logical non-equivalence same structure as !=.
                        $token->value = '!=';
                        $ret[] = $token;
                        break;
                    case '.and.':
                        $token->value = '&&';
                        $ret[] = $token;
                        break;
                    case '.or.':
                        $token->value = '||';
                        $ret[] = $token;
                        break;
                    case '.not.':
                        $token->value = '!';
                        $ret[] = $token;
                        break;
                    default:
                        $token->value = $opval;
                        $ret[] = $token;
                }
            } else if ($token->type == token_type::RESERVED) {
                // Fortran is case-insensitive; normalise to lowercase first.
                $value = strtolower($token->value);
                switch ($value) {
                    // Loop constructs → canonical "while".
                    case 'do':
                    case 'forall':
                        $value = 'while';
                        break;
                    // Loop-exit keywords → canonical "break".
                    case 'cycle':
                    case 'exit':
                        $value = 'break';
                        break;
                    // Conditional variants → canonical "if".
                    case 'elseif':
                    case 'elsewhere':
                    case 'select':
                        $value = 'if';
                        break;
                }
                $token->value = $value;
                $ret[] = $token;
            } else {
                $ret[] = $token;
            }
        }
        $tokens = $ret;
        return $ret;
    }
}
