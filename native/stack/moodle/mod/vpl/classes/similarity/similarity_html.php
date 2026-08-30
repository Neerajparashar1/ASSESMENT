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
 * HTML similarity class
 *
 * @package mod_vpl
 * @copyright 2012 onwards Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token_type;

/**
 * HTML similarity class.
 * @codeCoverageIgnore
 */
class similarity_html extends similarity_generic {
    /**
     * Constructor for the HTML similarity.
     */
    public function __construct() {
        parent::__construct('html');
    }

    /**
     * Get the token types used for fingerprinting.
     * HTML uses RESERVED (tag names) and IDENTIFIER (attribute names).
     *
     * @return array Array of token_type constants
     */
    protected function get_fingerprint_types(): array {
        return [token_type::RESERVED, token_type::IDENTIFIER];
    }

    /**
     * Normalizes the syntax of the given tokens.
     * Collapses consecutive LITERAL tokens into a single token.
     *
     * @param array $tokens The tokens to normalize.
     * @return array The normalized tokens.
     */
    public function sintax_normalize(&$tokens): array {
        $normalized = [];
        $previous = null;
        foreach ($tokens as $token) {
            if ($token->type === token_type::LITERAL) {
                if ($previous !== token_type::LITERAL) {
                    $normalized[] = $token;
                }
            } else {
                $normalized[] = $token;
            }
            $previous = $token->type;
        }
        $tokens = $normalized;
        return $tokens;
    }
}
