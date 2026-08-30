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
 * Clojure language similarity class
 *
 * @package mod_vpl
 * @copyright 2026 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token_type;

/**
 * Clojure language similarity class.
 * @codeCoverageIgnore
 */
class similarity_clojure extends similarity_generic {
    /**
     * Constructor for the Clojure similarity.
     */
    public function __construct() {
        parent::__construct('clojure');
    }

    /**
     * Normalizes the syntax of the given tokens.
     * Operator transformations:
     *   - Open paren/bracket (( and [) dropped; closers kept
     *   - not= → canonical "!="
     * Reserved-word normalisations:
     *   - Iteration forms  for / doseq / dotimes / doall / dorun / loop  → canonical "while"
     *   - Tail-call  recur  → canonical "break"
     *   - Conditional forms  when / when-not / if-not / if-let / when-let /
     *     when-first / cond / condp  → canonical "if"
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
                    case 'not=':
                        // Clojure not= is identical in structure to !=.
                        $token->value = '!=';
                        $ret[] = $token;
                        break;
                    default:
                        $ret[] = $token;
                }
            } else if ($token->type == token_type::RESERVED) {
                switch ($token->value) {
                    // Iteration forms → canonical "while".
                    case 'for':
                    case 'doseq':
                    case 'dotimes':
                    case 'doall':
                    case 'dorun':
                    case 'loop':
                        $token->value = 'while';
                        break;
                    // Tail-call back to top → canonical "break".
                    case 'recur':
                        $token->value = 'break';
                        break;
                    // Single/multi-branch conditional forms → canonical "if".
                    case 'when':
                    case 'when-not':
                    case 'if-not':
                    case 'if-let':
                    case 'when-let':
                    case 'when-first':
                    case 'cond':
                    case 'condp':
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
