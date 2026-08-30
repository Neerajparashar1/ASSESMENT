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
 * MATLAB/Octave language similarity class
 *
 * @package mod_vpl
 * @copyright 2012 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token_type;

/**
 * MATLAB/Octave language similarity class.
 * @codeCoverageIgnore
 */
class similarity_matlab extends similarity_generic {
    /**
     * Constructor for the MATLAB/Octave similarity.
     */
    public function __construct() {
        parent::__construct('matlab');
    }

    /**
     * Normalizes the syntax of the given tokens.
     * Operator transformations:
     *   - Open brackets/braces/parens dropped (only closers kept)
     *   - Comparison direction canonicalised: < → >, <= → >=
     * Reserved-word normalisations:
     *   - Loop keywords  for / while  → canonical "while"
     *   - Exit keywords  break / continue  → canonical "break"
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
                    case '[':
                        // Only add ].
                        break;
                    case '(':
                        // Only add ).
                        break;
                    case '{':
                        break;
                    case '<': // Canonicalise: replace < with >.
                        $token->value = '>';
                        $ret[] = $token;
                        break;
                    case '<=': // Canonicalise: replace <= with >=.
                        $token->value = '>=';
                        $ret[] = $token;
                        break;
                    default:
                        $ret[] = $token;
                }
            } else if ($token->type == token_type::RESERVED) {
                // Normalise semantically interchangeable reserved words so that
                // superficial rewrites (e.g. for → while) do not reduce similarity.
                switch ($token->value) {
                    case 'for':
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
        }
        $tokens = $ret;
        return $ret;
    }
}
