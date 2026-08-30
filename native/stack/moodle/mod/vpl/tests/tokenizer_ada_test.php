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
 * Unit tests for the Ada tokenizer.
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
 * Unit tests for the Ada tokenizer.
 *
 * @group mod_vpl
 * @group mod_vpl_vplt
 * @group mod_vpl_tokenizer
 * @group mod_vpl_tokenizer_lang
 * @covers \mod_vpl\tokenizer\tokenizer
 * @covers \mod_vpl\tokenizer\tokenizer_factory
 */
final class tokenizer_ada_test extends \advanced_testcase {
    /**
     * Test that the Ada tokenizer can parse a code example.
     *
     * @covers \mod_vpl\tokenizer\tokenizer_factory::get
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_parse(): void {
        $this->resetAfterTest();

        $code = '-- Ada language example
with Ada.Text_IO; use Ada.Text_IO;

procedure Hello is
   X : Integer := 42;
   Y : Float := 3.14159;
   Message : String := "Hello, World!";
begin
   Put_Line(Message);

   -- Conditional statement
   if X > 0 then
      Put_Line("Positive");
   else
      Put_Line("Non-positive");
   end if;

   -- Loop example
   for I in 1 .. 10 loop
      Put_Line(Integer\'Image(I));
   end loop;
end Hello;';

        $tokenizer = tokenizer_factory::get('ada');
        $this->assertNotNull($tokenizer, 'Failed to create Ada tokenizer');

        $tokens = $tokenizer->parse($code, false);
        $this->assertIsArray($tokens, 'Tokens should be an array');
        $this->assertNotEmpty($tokens, 'No tokens generated for Ada');

        $tokentypes = array_unique(array_map(fn($t) => $t->type, $tokens));
        $expectedtypes = [token_type::RESERVED, token_type::IDENTIFIER, token_type::LITERAL];
        $found = false;
        foreach ($expectedtypes as $expected) {
            if (in_array($expected, $tokentypes)) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Ada tokenizer should produce at least one expected token type');
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
