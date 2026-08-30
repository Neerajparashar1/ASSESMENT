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
 * Eiffel language similarity class
 *
 * @package mod_vpl
 * @copyright 2026 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token_type;

/**
 * Eiffel language similarity class.
 * @codeCoverageIgnore
 */
class similarity_eiffel extends similarity_generic {
    /**
     * Constructor for the Eiffel similarity.
     */
    public function __construct() {
        parent::__construct('eiffel');
    }

    /**
     * Normalizes the syntax of the given tokens.
     * Eiffel is case-insensitive, so all reserved-word values are lowercased.
     * Operator transformations:
     *   - Open paren/bracket/brace/(, [, {, <<, |() dropped; closers kept
     * Reserved-word normalisations:
     *   - Loop openers  from / across  → canonical "while"
     *   - "elseif"  → canonical "if"
     *   - "inspect" (case/switch construct) → canonical "if"
     *   - "when" (inspect-case label) → canonical "if"
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
                switch ($token->value) {
                    case '(':
                        // Only add ).
                        break;
                    case '[':
                        // Only add ].
                        break;
                    case '{':
                        // Only add }.
                        break;
                    case '<<':
                        // Manifest array/tuple opener; only add >>.
                        break;
                    case '|(':
                        // Iteration structure opener; only add |).
                        break;
                    default:
                        $ret[] = $token;
                }
            } else if ($token->type == token_type::RESERVED) {
                // Eiffel is case-insensitive; normalise to lowercase first.
                $value = strtolower($token->value);
                switch ($value) {
                    // Loop openers → canonical "while".
                    case 'from':
                    case 'across':
                        $value = 'while';
                        break;
                    // Conditional variants → canonical "if".
                    case 'elseif':
                    case 'inspect':
                    case 'when':
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
