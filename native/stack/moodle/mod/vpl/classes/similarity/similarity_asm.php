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
 * Assembly language similarity class
 *
 * @package mod_vpl
 * @copyright 2026 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token_type;

/**
 * Assembly language similarity class.
 * @codeCoverageIgnore
 */
class similarity_asm extends similarity_generic {
    /**
     * Constructor for the Assembly similarity.
     */
    public function __construct() {
        parent::__construct('asm');
    }

    /**
     * Normalizes the syntax of the given tokens.
     * NASM mnemonics are case-insensitive, so all reserved-word values are
     * lowercased first, then semantically equivalent instructions are unified:
     *   - Conditional jumps (ja/jae/jb … jz, jcxz, jecxz, jrcxz) → "jmp"
     *   - Loop variants (loope/loopne/loopnz/loopz) → "loop"
     *   - inc → "add", dec → "sub"  (special cases of add/sub with implicit 1)
     *   - sal → "shl"  (identical operation)
     *   - sysenter/sysexit/sysret → "syscall"  (all enter-kernel-mode variants)
     *   - set* conditional byte-set variants → "set"
     *   - cmov* conditional move variants → "cmov"
     * Operator transformations:
     *   - Open bracket/paren ([ and () dropped; closers ] and ) kept
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
                    default:
                        $ret[] = $token;
                }
            } else if ($token->type == token_type::RESERVED) {
                // NASM is case-insensitive; normalise to lowercase first.
                $value = strtolower($token->value);
                switch ($value) {
                    // Conditional jumps → canonical "jmp".
                    case 'ja':
                    case 'jae':
                    case 'jb':
                    case 'jbe':
                    case 'jc':
                    case 'jcxz':
                    case 'jecxz':
                    case 'jrcxz':
                    case 'je':
                    case 'jg':
                    case 'jge':
                    case 'jl':
                    case 'jle':
                    case 'jna':
                    case 'jnae':
                    case 'jnb':
                    case 'jnbe':
                    case 'jnc':
                    case 'jne':
                    case 'jng':
                    case 'jnge':
                    case 'jnl':
                    case 'jnle':
                    case 'jno':
                    case 'jnp':
                    case 'jns':
                    case 'jnz':
                    case 'jo':
                    case 'jp':
                    case 'jpe':
                    case 'jpo':
                    case 'js':
                    case 'jz':
                        $value = 'jmp';
                        break;
                    // Loop variants → canonical "loop".
                    case 'loope':
                    case 'loopne':
                    case 'loopnz':
                    case 'loopz':
                        $value = 'loop';
                        break;
                    // Increment/decrement → canonical add/sub.
                    case 'inc':
                        $value = 'add';
                        break;
                    case 'dec':
                        $value = 'sub';
                        break;
                    // Sal is bit-for-bit identical to shl.
                    case 'sal':
                        $value = 'shl';
                        break;
                    // Kernel-entry variants → canonical "syscall".
                    case 'sysenter':
                    case 'sysexit':
                    case 'sysret':
                        $value = 'syscall';
                        break;
                    // Set* conditional byte-set → canonical "set".
                    case 'seta':
                    case 'setae':
                    case 'setb':
                    case 'setbe':
                    case 'setc':
                    case 'sete':
                    case 'setg':
                    case 'setge':
                    case 'setl':
                    case 'setle':
                    case 'setna':
                    case 'setnae':
                    case 'setnb':
                    case 'setnbe':
                    case 'setnc':
                    case 'setne':
                    case 'setng':
                    case 'setnge':
                    case 'setnl':
                    case 'setnle':
                    case 'setno':
                    case 'setnp':
                    case 'setns':
                    case 'setnz':
                    case 'seto':
                    case 'setp':
                    case 'setpe':
                    case 'setpo':
                    case 'sets':
                    case 'setz':
                        $value = 'set';
                        break;
                    // Cmov* conditional move → canonical "cmov".
                    case 'cmova':
                    case 'cmovae':
                    case 'cmovb':
                    case 'cmovbe':
                    case 'cmovc':
                    case 'cmove':
                    case 'cmovg':
                    case 'cmovge':
                    case 'cmovl':
                    case 'cmovle':
                    case 'cmovna':
                    case 'cmovnae':
                    case 'cmovnb':
                    case 'cmovnbe':
                    case 'cmovnc':
                    case 'cmovne':
                    case 'cmovng':
                    case 'cmovnge':
                    case 'cmovnl':
                    case 'cmovnle':
                    case 'cmovno':
                    case 'cmovnp':
                    case 'cmovns':
                    case 'cmovnz':
                    case 'cmovo':
                    case 'cmovp':
                    case 'cmovpe':
                    case 'cmovpo':
                    case 'cmovs':
                    case 'cmovz':
                        $value = 'cmov';
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
