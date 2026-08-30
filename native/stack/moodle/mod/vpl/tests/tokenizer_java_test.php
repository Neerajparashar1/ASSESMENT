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
 * Unit tests for the Java tokenizer.
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
 * Unit tests for the Java tokenizer.
 *
 * @group mod_vpl
 * @group mod_vpl_vplt
 * @group mod_vpl_tokenizer
 * @group mod_vpl_tokenizer_lang
 * @covers \mod_vpl\tokenizer\tokenizer
 * @covers \mod_vpl\tokenizer\tokenizer_factory
 */
final class tokenizer_java_test extends \advanced_testcase {
    /**
     * Test that the Java tokenizer can parse a code example.
     *
     * @covers \mod_vpl\tokenizer\tokenizer_factory::get
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_parse(): void {
        $this->resetAfterTest();

        $code = '// Java language example
package com.example;

import java.util.*;

public class Example {
    private int value;
    private String name;

    public Example(int value, String name) {
        this.value = value;
        this.name = name;
    }

    public void display() {
        System.out.println("Name: " + name + ", Value: " + value);
    }

    public static void main(String[] args) {
        Example obj = new Example(42, "Test");
        obj.display();

        // Loop example
        for (int i = 0; i < 10; i++) {
            System.out.println(i);
        }
    }
}';

        $tokenizer = tokenizer_factory::get('java');
        $this->assertNotNull($tokenizer, 'Failed to create Java tokenizer');

        $tokens = $tokenizer->parse($code, false);
        $this->assertIsArray($tokens, 'Tokens should be an array');
        $this->assertNotEmpty($tokens, 'No tokens generated for Java');

        $tokentypes = array_unique(array_map(fn($t) => $t->type, $tokens));
        $expectedtypes = [token_type::RESERVED, token_type::IDENTIFIER, token_type::LITERAL, token_type::OPERATOR];
        $found = false;
        foreach ($expectedtypes as $expected) {
            if (in_array($expected, $tokentypes)) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Java tokenizer should produce at least one expected token type');
    }

    /**
     * Test keyword and class identifier recognition in Java.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_keywords(): void {
        $this->resetAfterTest();

        $javatokenizer = tokenizer_factory::get('java');
        $javacode = 'public class Test { String name = "hello"; }';
        $javatokens = $javatokenizer->parse($javacode, false);
        $javatokenvalues = array_map(fn($t) => $t->value, $javatokens);

        $this->assertContains('public', $javatokenvalues, 'Java tokenizer should identify "public" keyword');
        $this->assertContains('class', $javatokenvalues, 'Java tokenizer should identify "class" keyword');
    }

    /**
     * Test Java string literal parsing.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_string_literals(): void {
        $this->resetAfterTest();

        $javatokenizer = tokenizer_factory::get('java');
        $javacode = 'String s = "Hello, World!";';
        $javatokens = $javatokenizer->parse($javacode, false);

        $this->assertNotEmpty($javatokens, 'Should parse Java string literal');
    }

    /**
     * Test tokenizer with nested function calls in Java.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_nested_structures(): void {
        $this->resetAfterTest();

        $javatokenizer = tokenizer_factory::get('java');
        $javacode = 'result = func1(func2(a, b), func3(c + d, e * f));';
        $javatokens = $javatokenizer->parse($javacode, false);

        $this->assertNotEmpty($javatokens, 'Should parse nested function calls');
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
