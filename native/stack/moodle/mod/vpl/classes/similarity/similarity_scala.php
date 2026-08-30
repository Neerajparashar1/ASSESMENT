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
 * Scala language similarity class
 *
 * @package mod_vpl
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Lang Michael <michael.lang.ima10@fh-joanneum.at>
 * @author Lückl Bernd <bernd.lueckl.ima10@fh-joanneum.at>
 * @author Lang Johannes <johannes.lang.ima10@fh-joanneum.at>
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 * @copyright all authors
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token_type;

/**
 * Scala language similarity class.
 * Inherits Java normalisation (JVM-style syntax: no pointer arithmetic,
 * no scope ::, same compound-assignment and brace collapsing rules).
 * @codeCoverageIgnore
 */
class similarity_scala extends similarity_java {
    /**
     * Constructor for the Scala similarity.
     */
    public function __construct() {
        // Skip similarity_java / similarity_c constructors; call generic directly with 'scala'.
        similarity_generic::__construct('scala');
    }

    /**
     * Normalizes the syntax of the given tokens.
     * Extends Java normalization with Scala-specific rules:
     * Operator transformations:
     *   - Strict equality (===) → canonical "=="
     *   - Generator arrow (<-) → canonical "=" (same structure as assignment)
     *
     * @param array $tokens The tokens to normalize.
     * @return array The normalized tokens.
     */
    public function sintax_normalize(&$tokens): array {
        // First apply all Java-level normalisations.
        parent::sintax_normalize($tokens);
        // Then apply Scala-specific fixes.
        foreach ($tokens as $token) {
            if ($token->type == token_type::OPERATOR) {
                if ($token->value === '===') {
                    $token->value = '==';
                } else if ($token->value === '<-') {
                    // Generator arrow → canonical =.
                    $token->value = '=';
                }
            }
        }
        return $tokens;
    }
}
