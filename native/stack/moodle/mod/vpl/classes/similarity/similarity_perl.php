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
 * Perl language similarity class
 *
 * @package mod_vpl
 * @copyright 2026 Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */
namespace mod_vpl\similarity;

use mod_vpl\tokenizer\token;
use mod_vpl\tokenizer\token_type;

/**
 * Perl language similarity class.
 * @codeCoverageIgnore
 */
class similarity_perl extends similarity_generic {
    /**
     * Compound-assignment and increment/decrement operator map.
     * Maps each compound operator to its bare form.
     */
    protected const COMPOUND_OPS = [
        '++' => '+',
        '--' => '-',
        '+=' => '+',
        '-=' => '-',
        '*=' => '*',
        '/=' => '/',
        '%=' => '%',
        '&=' => '&',
        '|=' => '|',
        '^=' => '^',
        '<<=' => '<<',
        '>>=' => '>>',
        '**=' => '**',
        '||=' => '||',
        '&&=' => '&&',
        '.=' => '.',
    ];

    /**
     * Constructor for the Perl similarity.
     */
    public function __construct() {
        parent::__construct('perl');
    }

    /**
     * Normalizes the syntax of the given tokens.
     * Operator transformations:
     *   - Compound-assignment (+=, -=, *=, /=, %=, &=, |=, ^=, <<=, >>=, **=) → expanded to "= <op>"
     *   - Logical-assign (||=, &&=) → expanded to "= ||"/"&&"
     *   - String-concat-assign (.=) → expanded to "= ."
     *   - Increment/decrement (++/--) → expanded to "= +"/"-"
     *   - Open brackets/parens ([, () dropped (only closers kept)
     *   - Trivial single-statement {}-blocks collapsed
     * Reserved-word normalisations:
     *   - Loop keywords  for / foreach / until  → canonical "while"
     *   - Exit keywords  last (≡ break) / next (≡ continue)  → canonical "break"
     *
     * @param array $tokens The tokens to normalize.
     * @return array The normalized tokens.
     */
    public function sintax_normalize(&$tokens): array {
        $openbrace = false;
        $nsemicolon = 0;
        $ret = [];
        $prev = new token(token_type::IDENTIFIER, '', 0);
        foreach ($tokens as $token) {
            if ($token->type == token_type::OPERATOR) {
                if (self::expand_compound_assignment($token, static::COMPOUND_OPS, $ret)) {
                    $prev = $token;
                    continue;
                }
                switch ($token->value) {
                    case '[':
                    case '(':
                        // Only add closing bracket.
                        break;
                    case '{':
                        $nsemicolon = 0;
                        $openbrace = true;
                        break;
                    case '}':
                        if (!($openbrace && $nsemicolon < 2)) {
                            $ret[] = $token;
                        }
                        $openbrace = false;
                        break;
                    case ';':
                        $nsemicolon++;
                        $ret[] = $token;
                        break;
                    default:
                        $ret[] = $token;
                }
            } else if ($token->type == token_type::RESERVED) {
                switch ($token->value) {
                    case 'for':
                    case 'foreach':
                    case 'until':
                        $token->value = 'while';
                        break;
                    case 'last':
                    case 'next':
                        $token->value = 'break';
                        break;
                }
                $ret[] = $token;
            } else {
                $ret[] = $token;
            }
            $prev = $token;
        }
        $tokens = $ret;
        return $ret;
    }
}
