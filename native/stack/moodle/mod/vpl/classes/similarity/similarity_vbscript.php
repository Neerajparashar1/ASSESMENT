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
 * VBScript language similarity class
 *
 * @package mod_vpl
 * @copyright 2026 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token_type;

/**
 * VBScript language similarity class.
 * @codeCoverageIgnore
 */
class similarity_vbscript extends similarity_generic {
    /**
     * Constructor for the VBScript similarity.
     */
    public function __construct() {
        parent::__construct('vbscript');
    }

    /**
     * Normalizes the syntax of the given tokens.
     * Reserved-word normalisations (case-insensitive):
     *   - Loop keywords  For / Do / Until  → canonical "while"
     *   - Exit keywords  Exit (≡ break)  → canonical "break"
     *
     * @param array $tokens The tokens to normalize.
     * @return array The normalized tokens.
     */
    public function sintax_normalize(&$tokens): array {
        foreach ($tokens as $token) {
            if ($token->type == token_type::RESERVED) {
                switch (strtolower($token->value)) {
                    case 'for':
                    case 'do':
                    case 'until':
                        $token->value = 'while';
                        break;
                    case 'exit':
                        $token->value = 'break';
                        break;
                }
            }
        }
        return $tokens;
    }
}
