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
 * Unit tests for the HTML tokenizer.
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
 * Unit tests for the HTML tokenizer.
 *
 * @group mod_vpl
 * @group mod_vpl_vplt
 * @group mod_vpl_tokenizer
 * @group mod_vpl_tokenizer_lang
 * @covers \mod_vpl\tokenizer\tokenizer
 * @covers \mod_vpl\tokenizer\tokenizer_factory
 */
final class tokenizer_html_test extends \advanced_testcase {
    /**
     * Test that the HTML tokenizer can parse a code example.
     *
     * @covers \mod_vpl\tokenizer\tokenizer_factory::get
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_parse(): void {
        $this->resetAfterTest();

        $code = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Page</title>
    <style>
        body { color: #333; font-size: 16px; }
        .container { margin: 0 auto; }
    </style>
</head>
<body>
    <!-- This is a comment -->
    <div class="container" id="main">
        <h1>Hello, World!</h1>
        <p>Paragraph with <strong>bold</strong> and <em>italic</em>.</p>
        <a href="https://example.com">Link</a>
        <img src="image.png" alt="An image" />
        <ul>
            <li>Item 1</li>
            <li>Item 2</li>
        </ul>
        <input type="text" value="test" disabled>
        &amp; &lt; &gt; &#169;
    </div>
    <script>
        var x = 42;
        var name = "hello";
        if (x > 0) {
            console.log(name);
        }
    </script>
</body>
</html>';

        $tokenizer = tokenizer_factory::get('html');
        $this->assertNotNull($tokenizer, 'Failed to create HTML tokenizer');

        $tokens = $tokenizer->parse($code, false);
        $this->assertIsArray($tokens, 'Tokens should be an array');
        $this->assertNotEmpty($tokens, 'No tokens generated for HTML');

        $tokentypes = array_unique(array_map(fn($t) => $t->type, $tokens));
        $expectedtypes = [token_type::RESERVED, token_type::IDENTIFIER, token_type::LITERAL];
        $found = false;
        foreach ($expectedtypes as $expected) {
            if (in_array($expected, $tokentypes)) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'HTML tokenizer should produce at least one expected token type');
    }

    /**
     * Test HTML tokenizer with tag names, attributes, and content.
     * Tag names should be RESERVED; attribute names should be IDENTIFIER.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_html_tags(): void {
        $this->resetAfterTest();

        $htmltokenizer = tokenizer_factory::get('html');

        $html = '<div class="main">Hello</div>';
        $tokens = $htmltokenizer->parse($html, false);
        $this->assertNotEmpty($tokens, 'HTML tokenizer should parse basic tags');

        $tokenvalues = array_map(fn($t) => $t->value, $tokens);
        $tokentypes = [];
        foreach ($tokens as $t) {
            $tokentypes[$t->value] = $t->type;
        }

        $this->assertContains('div', $tokenvalues, 'Should identify tag name "div"');
        $this->assertEquals(token_type::RESERVED, $tokentypes['div'], '"div" should be RESERVED');
        $this->assertContains('class', $tokenvalues, 'Should identify attribute name "class"');
        $this->assertEquals(token_type::IDENTIFIER, $tokentypes['class'], '"class" should be IDENTIFIER');

        // Tag delimiters (<, >) should be filtered out as vpl_null.
        $this->assertNotContains('<', $tokenvalues, '"<" should be filtered as vpl_null');
        $this->assertNotContains('>', $tokenvalues, '">" should be filtered as vpl_null');
    }

    /**
     * Test HTML tokenizer filters comments as vpl_null.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_html_comments(): void {
        $this->resetAfterTest();

        $htmltokenizer = tokenizer_factory::get('html');

        $html = '<!-- comment --><p>visible</p>';
        $tokens = $htmltokenizer->parse($html, false);
        $tokenvalues = array_map(fn($t) => $t->value, $tokens);

        $this->assertContains('p', $tokenvalues, 'Should parse tag after comment');
        $this->assertContains('visible', $tokenvalues, 'Should parse content after comment');
        $this->assertNotContains('comment', $tokenvalues, 'Comment text should be filtered');
    }

    /**
     * Test HTML tokenizer with attribute values.
     * Attribute names should be IDENTIFIER; quoted values should be LITERAL.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_html_attributes(): void {
        $this->resetAfterTest();

        $htmltokenizer = tokenizer_factory::get('html');

        $html = '<a href="https://example.com" target="_blank">link</a>';
        $tokens = $htmltokenizer->parse($html, false);
        $tokentypes = [];
        foreach ($tokens as $t) {
            $tokentypes[$t->value] = $t->type;
        }

        $this->assertEquals(token_type::RESERVED, $tokentypes['a'], '"a" tag should be RESERVED');
        $this->assertEquals(token_type::IDENTIFIER, $tokentypes['href'], '"href" should be IDENTIFIER');
        $this->assertEquals(token_type::IDENTIFIER, $tokentypes['target'], '"target" should be IDENTIFIER');

        // Quoted attribute values should be LITERAL.
        $hasattrvalue = false;
        foreach ($tokens as $t) {
            if ($t->type === token_type::LITERAL && strpos($t->value, 'example.com') !== false) {
                $hasattrvalue = true;
                break;
            }
        }
        $this->assertTrue($hasattrvalue, 'Attribute value should be parsed as LITERAL');
    }

    /**
     * Test HTML tokenizer with self-closing tags.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_html_self_closing(): void {
        $this->resetAfterTest();

        $htmltokenizer = tokenizer_factory::get('html');

        $html = '<img src="photo.jpg" alt="Photo" /><br/><hr>';
        $tokens = $htmltokenizer->parse($html, false);
        $tokenvalues = array_map(fn($t) => $t->value, $tokens);

        $this->assertContains('img', $tokenvalues, 'Should identify "img" self-closing tag');
        $this->assertContains('src', $tokenvalues, 'Should identify "src" attribute');
        $this->assertContains('alt', $tokenvalues, 'Should identify "alt" attribute');
        $this->assertContains('br', $tokenvalues, 'Should identify "br" tag');
        $this->assertContains('hr', $tokenvalues, 'Should identify "hr" tag');
    }

    /**
     * Test HTML tokenizer with HTML entities.
     * Entities should be parsed as LITERAL tokens.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_html_entities(): void {
        $this->resetAfterTest();

        $htmltokenizer = tokenizer_factory::get('html');

        $html = '<p>&amp; &lt; &gt; &#169; &#x00A9;</p>';
        $tokens = $htmltokenizer->parse($html, false);

        $hasentity = false;
        foreach ($tokens as $t) {
            if ($t->type === token_type::LITERAL && strpos($t->value, '&') === 0) {
                $hasentity = true;
                break;
            }
        }
        $this->assertTrue($hasentity, 'HTML entities should be parsed as LITERAL');
    }

    /**
     * Test HTML tokenizer with embedded JavaScript in a script tag.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_html_embedded_script(): void {
        $this->resetAfterTest();

        $htmltokenizer = tokenizer_factory::get('html');

        $html = '<script>var x = 42; if (x > 0) { console.log(x); }</script>';
        $tokens = $htmltokenizer->parse($html, false);
        $tokenvalues = array_map(fn($t) => $t->value, $tokens);

        $this->assertContains('x', $tokenvalues, 'Should parse JS identifier in script');
        $this->assertContains('42', $tokenvalues, 'Should parse JS literal in script');
        $this->assertContains('console', $tokenvalues, 'Should parse JS identifier "console"');
    }

    /**
     * Test HTML tokenizer with embedded CSS in a style tag.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_html_embedded_style(): void {
        $this->resetAfterTest();

        $htmltokenizer = tokenizer_factory::get('html');

        $html = '<style>body { color: #333; margin: 10px; }</style>';
        $tokens = $htmltokenizer->parse($html, false);
        $tokenvalues = array_map(fn($t) => $t->value, $tokens);

        $this->assertContains('body', $tokenvalues, 'Should parse CSS selector');
        $this->assertContains('color', $tokenvalues, 'Should parse CSS property');
        $this->assertContains('margin', $tokenvalues, 'Should parse CSS property "margin"');
    }

    /**
     * Test HTML tokenizer with a DOCTYPE declaration.
     *
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_html_doctype(): void {
        $this->resetAfterTest();

        $htmltokenizer = tokenizer_factory::get('html');

        $html = '<!DOCTYPE html><html><body>Hello</body></html>';
        $tokens = $htmltokenizer->parse($html, false);
        $tokenvalues = array_map(fn($t) => $t->value, $tokens);

        $this->assertNotEmpty($tokens, 'Should parse HTML with DOCTYPE');
        $this->assertContains('html', $tokenvalues, 'Should identify "html" tag');
        $this->assertContains('body', $tokenvalues, 'Should identify "body" tag');
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
