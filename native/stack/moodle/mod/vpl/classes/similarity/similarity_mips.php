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
 * MIPS assembly language similarity class
 *
 * @package mod_vpl
 * @copyright 2026 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token_type;

/**
 * MIPS assembly language similarity class.
 * @codeCoverageIgnore
 */
class similarity_mips extends similarity_generic {
    /**
     * Constructor for the MIPS similarity.
     */
    public function __construct() {
        parent::__construct('mips');
    }

    /**
     * Normalizes the syntax of the given tokens.
     * Operator transformations:
     *   - Open parens (() dropped (only closers kept)
     * Reserved-word normalisations:
     *   - Conditional branches (beq/bne/blt/bgt/ble/bge/bltz/bgtz/blez/bgez/beqz/bnez) → canonical "beq"
     *   - Move instruction (move) → canonical "add"  (move is add with $zero)
     *   - Load-immediate variants (li/lui/la) → canonical "li"
     *   - Multiply/divide variants (mul/mulo/mulou/rem/remu) → canonical "mult"
     *   - Subroutine jumps (jal/jalr) → canonical "jal"
     *   - Branch-and-link variants (bgezal/bltzal) → canonical "jal"
     *
     * @param array $tokens The tokens to normalize.
     * @return array The normalized tokens.
     */
    public function sintax_normalize(&$tokens): array {
        $ret = [];
        foreach ($tokens as $token) {
            if ($token->type == token_type::OPERATOR) {
                if ($token->value !== '(') {
                    // Keep everything except open paren.
                    $ret[] = $token;
                }
            } else if ($token->type == token_type::RESERVED) {
                $value = strtolower($token->value);
                switch ($value) {
                    // Conditional branches → canonical "beq".
                    case 'bne':
                    case 'blt':
                    case 'bgt':
                    case 'ble':
                    case 'bge':
                    case 'bltz':
                    case 'bgtz':
                    case 'blez':
                    case 'bgez':
                    case 'beqz':
                    case 'bnez':
                        $value = 'beq';
                        break;
                    // Move is structurally add with $zero.
                    case 'move':
                        $value = 'add';
                        break;
                    // Load-immediate variants → canonical "li".
                    case 'lui':
                    case 'la':
                        $value = 'li';
                        break;
                    // Multiply/divide variants → canonical "mult".
                    case 'mul':
                    case 'mulo':
                    case 'mulou':
                    case 'rem':
                    case 'remu':
                        $value = 'mult';
                        break;
                    // Subroutine/branch-and-link → canonical "jal".
                    case 'jalr':
                    case 'bgezal':
                    case 'bltzal':
                        $value = 'jal';
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
