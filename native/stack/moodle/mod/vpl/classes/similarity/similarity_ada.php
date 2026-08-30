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

namespace mod_vpl\similarity;

use mod_vpl\similarity\similarity_base;
use mod_vpl\tokenizer\token_type;
use mod_vpl\tokenizer\tokenizer_factory;

/**
 * Ada similarity class based on a tokenizer
 *
 * @package mod_vpl
 * @copyright 2026 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */

/**
 * Ada language similarity class.
 * @codeCoverageIgnore
 */
class similarity_ada extends similarity_generic {
    /**
     * Constructor for the Ada similarity.
     */
    public function __construct() {
        parent::__construct('ada');
    }
    /**
     * Get the token types used for fingerprinting.
     * Ada uses RESERVED (keywords) and OPERATOR (operators)
     * since the structure is defined by keywords and operators.
     *
     * @return array Array of token_type constants
     */
    protected function get_fingerprint_types(): array {
        return [token_type::OPERATOR, token_type::RESERVED];
    }


    /**
     * Normalizes the syntax of the given tokens.
     * Expands identifier-list declarations (e.g. "a, b, c : Integer") into
     * separate declarations, and drops open-bracket tokens.
     * Reserved-word normalisations:
     *   - Loop keywords  for / loop  → canonical "while"
     *   - Exit keyword  exit  → canonical "break"
     *   - Conditional variant  elsif  → canonical "if"
     * Non-operator and non-reserved tokens are passed through unchanged;
     * type-based filtering is left to get_fingerprint_types.
     *
     * @param array $tokens The tokens to normalize.
     * @return array The normalized tokens.
     */
    public function sintax_normalize(&$tokens) {
        $identifierlist = false;
        $nidentifiers = 0;
        $identifierdefpos = 0;
        $bracketlevel = 0;
        $ret = [];
        foreach ($tokens as $token) {
            if ($token->type == token_type::OPERATOR) {
                switch ($token->value) {
                    case '[':
                        // Only add ].
                        break;
                    case '(':
                        // Only add ).
                        $bracketlevel++;
                        break;
                    case '{':
                        // Only add }.
                        break;
                    case ')':
                        $bracketlevel--;
                        $ret[] = $token;
                        break;
                    case ';':
                        $ret[] = $token;
                        // End of identifier list declaration?
                        if ($identifierlist) {
                            if ($identifierdefpos > 0) {
                                $rep = array_slice($ret, $identifierdefpos);
                                for ($i = 0; $i < $nidentifiers; $i++) {
                                    foreach ($rep as $data) {
                                        $ret[] = $data;
                                    }
                                }
                            } else {
                                for ($i = 0; $i < $nidentifiers; $i++) {
                                    $ret[] = $token;
                                }
                            }
                        }
                        $identifierlist = false;
                        break;
                    case ',':
                        // Posible identifier list.
                        if ($bracketlevel == 0) {
                            if ($identifierlist) {
                                $identifierlist = true;
                                $identifierdefpos = 0;
                                $nidentifiers = 1;
                            } else {
                                $nidentifiers++;
                            }
                        } else {
                            $ret[] = $token;
                        }
                        break;
                    case ':':
                        if ($identifierlist) {
                            $identifierdefpos = count($ret);
                        }
                        $ret[] = $token;
                        break;
                    default:
                        $ret[] = $token;
                }
            } else if ($token->type == token_type::RESERVED) {
                $lower = strtolower($token->value);
                if ($lower == 'is') {
                    $identifierlist = true;
                    $identifierdefpos = 0;
                    $nidentifiers = 0;
                }
                // Normalise semantically interchangeable reserved words so that
                // superficial rewrites (e.g. for → while) do not reduce similarity.
                switch ($lower) {
                    case 'for':
                    case 'loop':
                        // All loop constructs → canonical "while".
                        $token->value = 'while';
                        break;
                    case 'exit':
                        // Ada's break equivalent → canonical "break".
                        $token->value = 'break';
                        break;
                    case 'elsif':
                        // Conditional variant → canonical "if".
                        $token->value = 'if';
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
