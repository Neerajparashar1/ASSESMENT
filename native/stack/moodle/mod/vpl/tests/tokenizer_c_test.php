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
 * Unit tests for the C tokenizer.
 *
 * @package mod_vpl
 * @copyright 2026 Juan Carlos Rodríguez-del-Pino <jc.rodriguezdelpino@ulpgc.es>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_vpl;

use mod_vpl\util\assertf;
use mod_vpl\tokenizer\token_type;
use mod_vpl\tokenizer\tokenizer_factory;

/**
 * Unit tests for the C tokenizer.
 *
 * @group mod_vpl
 * @group mod_vpl_vplt
 * @group mod_vpl_tokenizer
 * @group mod_vpl_tokenizer_lang
 * @covers \mod_vpl\tokenizer\tokenizer
 * @covers \mod_vpl\tokenizer\tokenizer_factory
 */
final class tokenizer_c_test extends \advanced_testcase {
    /**
     * Test that the C tokenizer can parse a code example.
     *
     * @covers \mod_vpl\tokenizer\tokenizer_factory::get
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_parse(): void {
        $this->resetAfterTest();

        $code = '// C language example
#include <stdio.h>

int main() {
    int x = 42;
    float pi = 3.14159;
    char str[] = "Hello, World!";

    // Print output
    printf("%s\\n", str);

    /* Multi-line
       comment */
    if (x > 0) {
        return 0;
    }
    return 1;
}';

        $tokenizer = tokenizer_factory::get('c');
        $this->assertNotNull($tokenizer, 'Failed to create C tokenizer');

        $tokens = $tokenizer->parse($code, false);
        $this->assertIsArray($tokens, 'Tokens should be an array');
        $this->assertNotEmpty($tokens, 'No tokens generated for C');

        $tokentypes = array_unique(array_map(fn($t) => $t->type, $tokens));
        $expectedtypes = [token_type::RESERVED, token_type::IDENTIFIER, token_type::LITERAL, token_type::OPERATOR];
        $found = false;
        foreach ($expectedtypes as $expected) {
            if (in_array($expected, $tokentypes)) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'C tokenizer should produce at least one expected token type');
    }

    /**
     * Test specific keyword and identifier recognition in C.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_keywords(): void {
        $this->resetAfterTest();

        $ctokenizer = tokenizer_factory::get('c');
        $ccode = 'int main() { return 0; }';
        $ctokens = $ctokenizer->parse($ccode, false);
        $ctokenvalues = array_map(fn($t) => $t->value, $ctokens);

        $this->assertContains('int', $ctokenvalues, 'C tokenizer should identify "int" keyword');
        $this->assertContains('main', $ctokenvalues, 'C tokenizer should identify "main" identifier');
        $this->assertContains('return', $ctokenvalues, 'C tokenizer should identify "return" keyword');
        $this->assertContains('0', $ctokenvalues, 'C tokenizer should identify "0" literal');
    }

    /**
     * Test tokenizer with various C comment styles.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_comments(): void {
        $this->resetAfterTest();

        $ctokenizer = tokenizer_factory::get('c');

        // Test single-line comments.
        $ccode = '// This is a comment
int x = 5;';
        $ctokens = $ctokenizer->parse($ccode, false);
        $ctokenvalues = array_map(fn($t) => $t->value, $ctokens);

        $this->assertContains('int', $ctokenvalues, 'Should parse code after comment');
        $this->assertContains('x', $ctokenvalues, 'Should parse identifier after comment');

        // Test multi-line comments.
        $cppcode = '/* Multi-line
comment */
int y = 10;';
        $cpptokens = $ctokenizer->parse($cppcode, false);
        $cpptokenvalues = array_map(fn($t) => $t->value, $cpptokens);

        $this->assertContains('int', $cpptokenvalues, 'Should parse code after multi-line comment');
        $this->assertContains('y', $cpptokenvalues, 'Should parse identifier after multi-line comment');
    }

    /**
     * Test tokenizer with C string literals containing escape sequences.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_string_literals(): void {
        $this->resetAfterTest();

        $ctokenizer = tokenizer_factory::get('c');
        $ccode = 'char* s = "Line 1\\nLine 2";';
        $ctokens = $ctokenizer->parse($ccode, false);

        $this->assertNotEmpty($ctokens, 'Should parse C string with escape sequences');
    }

    /**
     * Test tokenizer with various C numeric literal formats.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_numeric_literals(): void {
        $this->resetAfterTest();

        $ctokenizer = tokenizer_factory::get('c');
        $ccode = 'int a = 42; float b = 3.14; int c = 0xFF; double d = 1.5e-10;';
        $ctokens = $ctokenizer->parse($ccode, false);
        $ctokenvalues = array_map(fn($t) => $t->value, $ctokens);

        $this->assertContains('42', $ctokenvalues, 'Should parse integer literal');
        $this->assertNotEmpty($ctokens, 'Should parse various numeric formats');
    }

    /**
     * Test tokenizer with C arithmetic and logical operators.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_operators(): void {
        $this->resetAfterTest();

        $ctokenizer = tokenizer_factory::get('c');

        $ccode = 'x = a + b - c * d / e % f;';
        $ctokens = $ctokenizer->parse($ccode, false);
        $this->assertNotEmpty($ctokens, 'Should parse arithmetic operators');

        $ccode2 = 'if (x > 0 && y < 10 || z == 5) { }';
        $ctokens2 = $ctokenizer->parse($ccode2, false);
        $this->assertNotEmpty($ctokens2, 'Should parse comparison and logical operators');
    }

    /**
     * Test tokenizer error handling with empty and whitespace-only C input.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_empty_input(): void {
        $this->resetAfterTest();

        $ctokenizer = tokenizer_factory::get('c');

        $emptytokens = $ctokenizer->parse('', false);
        $this->assertIsArray($emptytokens, 'Should return array for empty input');

        $whitespacetokens = $ctokenizer->parse("   \n\t  \n  ", false);
        $this->assertIsArray($whitespacetokens, 'Should return array for whitespace-only input');
    }

    /**
     * Test tokenizer with nested control structures in C.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_nested_structures(): void {
        $this->resetAfterTest();

        $ctokenizer = tokenizer_factory::get('c');
        $ccode = 'if (a > 0) { if (b > 0) { c = 1; } else { c = 2; } }';
        $ctokens = $ctokenizer->parse($ccode, false);

        $this->assertNotEmpty($ctokens, 'Should parse nested control structures');
    }

    /**
     * Test that the C tokenizer tracks line numbers correctly.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_line_numbers(): void {
        $this->resetAfterTest();

        $ctokenizer = tokenizer_factory::get('c');
        $ccode = "int x = 5;\nint y = 10;\nint z = 15;";
        $ctokens = $ctokenizer->parse($ccode, false);

        $this->assertNotEmpty($ctokens, 'Should parse multi-line code');

        $maxline = 0;
        foreach ($ctokens as $token) {
            $this->assertIsInt($token->line, 'Line number should be an integer');
            $this->assertGreaterThanOrEqual(1, $token->line, 'Line number should be at least 1');
            if ($token->line > $maxline) {
                $maxline = $token->line;
            }
        }

        $this->assertGreaterThan(1, $maxline, 'Should track multiple lines');
    }

    /**
     * Prepare test cases before the execution.
     */
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        assertf::set_enable();
    }

    /**
     * Clean up after the execution of test cases.
     */
    public static function tearDownAfterClass(): void {
        assertf::set_disable();
        parent::tearDownAfterClass();
    }
}
