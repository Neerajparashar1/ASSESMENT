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
 * SQL language similarity class
 *
 * @package mod_vpl
 * @copyright 2026 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token_type;

/**
 * SQL language similarity class.
 * @codeCoverageIgnore
 */
class similarity_sql extends similarity_generic {
    /**
     * Constructor for the SQL similarity.
     */
    public function __construct() {
        parent::__construct('sql');
    }

    /**
     * Normalizes the syntax of the given tokens.
     * Operator transformations:
     *   - Not-equal alias (<>) → canonical "!="
     *   - Open parens (() dropped (only closers kept)
     * Reserved-word normalisations:
     *   - Logical keywords  and → "&&",  or → "||"
     *   - Conditional keyword  case  → canonical "if"
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
                        // Drop open paren; only closer kept.
                        break;
                    case '<>':
                        // SQL not-equal → canonical !=.
                        $token->value = '!=';
                        $ret[] = $token;
                        break;
                    default:
                        $ret[] = $token;
                }
            } else if ($token->type == token_type::RESERVED) {
                switch ($token->value) {
                    case 'and':
                        $token->value = '&&';
                        break;
                    case 'or':
                        $token->value = '||';
                        break;
                    case 'case':
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
