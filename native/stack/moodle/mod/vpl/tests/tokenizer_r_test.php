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
 * Unit tests for the R tokenizer.
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
 * Unit tests for the R tokenizer.
 *
 * @group mod_vpl
 * @group mod_vpl_vplt
 * @group mod_vpl_tokenizer
 * @group mod_vpl_tokenizer_lang
 * @covers \mod_vpl\tokenizer\tokenizer
 * @covers \mod_vpl\tokenizer\tokenizer_factory
 */
final class tokenizer_r_test extends \advanced_testcase {
    /**
     * Test that the R tokenizer can parse a code example.
     *
     * @covers \mod_vpl\tokenizer\tokenizer_factory::get
     * @covers \mod_vpl\tokenizer\tokenizer::parse
     */
    public function test_tokenizer_parse(): void {
        $this->resetAfterTest();

        $code = '# R example
PI <- 3.14159

factorial <- function(n) {
  if (n <= 1) return(1)
  return(n * factorial(n - 1))
}

greet <- function(name) {
  cat(paste0("Hello, ", name, "!\n"))
}

# Main
x <- 42
message <- "Hello, World!"
cat(message, "\n")
greet("World")

numbers <- c(1, 2, 3, 4, 5)
squares <- numbers^2
print(squares)

for (n in numbers) {
  cat(n, "\n")
}

if (x > 0) {
  cat("Positive\n")
} else if (x < 0) {
  cat("Negative\n")
} else {
  cat("Zero\n")
}

cat(sprintf("5! = %d\n", factorial(5)))

# Apply functions
result <- sapply(numbers, function(n) n * 2)
print(result)
';

        $tokenizer = tokenizer_factory::get('r');
        $this->assertNotNull($tokenizer, 'Failed to create R tokenizer');

        $tokens = $tokenizer->parse($code, false);
        $this->assertIsArray($tokens, 'Tokens should be an array');
        $this->assertNotEmpty($tokens, 'No tokens generated for R');

        $tokentypes = array_unique(array_map(fn($t) => $t->type, $tokens));
        $expectedtypes = [token_type::RESERVED, token_type::IDENTIFIER, token_type::LITERAL, token_type::OPERATOR];
        $found = false;
        foreach ($expectedtypes as $expected) {
            if (in_array($expected, $tokentypes)) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'R tokenizer should produce at least one expected token type');
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
