## GIOTES MANUAL

**GIOTES** (General Input/Output Test Evaluation System) is an evaluator subplugin for **VPL** designed to replace **BIOTES**, the default evaluator.
This system allows teachers to automatically assess students’ programs by defining test cases that specify the program’s input and the expected output.

### What is GIOTES?

GIOTES is a general framework for evaluating programming submissions written in almost any language.
It runs as a VPL evaluator sub-plugin for Moodle ([VPL][1]) and generates reports and grades for that environment.

The framework’s goals are:

* **Integration with VPL.** Plug-and-play inside the familiar VPL for Moodle.
* **Easy to use.** Write test cases in a simple, readable format.
* **Report-oriented.** Fully customizable reports.
* **Compatibility with BIOTES.** Runs the same `vpl_evaluate.cases` files used by the previous default VPL framework.

GIOTES keeps the plain-text `statement = value` language teachers already know from **BIOTES** and runs the same *`vpl_evaluate.cases`* files.
Statements are case-insensitive, and spacing flexible.

It adds:

* Customizable marks for pass, fail, timeout, and error cases.
* Customizable messages for pass, fail (output), fail (exit code), and timeout cases.
* A richer set of placeholders you can embed in your messages.
* A per-case **Case title format** you can redefine.
* **Multiline end**, which lets you stop a multiline value at any token you choose.
* Per-case time limits.
* Exit-code matching that can be **required** (AND) or **sufficient** (OR) to pass a test case.

---

### Quick start


```
# vpl_evaluate.cases (first steps). This is a comment

Case = Sum of two integers
Input =3 4
Output = 7
Output = "The result is 7"
```

In a VPL activity, select GIOTES as the evaluator and enable automatic evaluation in Execution options. Upload this file as **Test cases**.
When the student or the teacher use the evaluate action GIOTES will execute the learner’s program, feed it the input `3 4`, compare the output with both expected possibilities, and grade automatically.

---

## The language

The **GIOTES language** defines how test cases are written, organized, and interpreted.
It is a lightweight, plain-text format designed to be **human-readable** for teachers and **machine-readable** for the evaluator.
Using simple `statement = value` rules, you can describe program inputs, expected outputs, time limits, grading rules, and report customization.
This section explains the **structure**, **statements**, and **placeholders** available in `vpl_evaluate.cases` files, with examples showing how to build reliable and flexible test definitions.

### General structure of test definitions (`vpl_evaluate.cases`)

The `vpl_evaluate.cases` file may contain:

* **Global defaults** (optional) — apply to all cases unless overridden.
* **Case blocks** — each begins with `case =` and describes a test case.  
    All settings inside a case override the global defaults, except for `output =`, which **adds** additional passing possibilities.

*Format overview*

```text
  ├─── General statements and Defaults  (global scope, optional)
  │    • Set before the first 'case =' block.
  │    • Define default values for all cases.
  │    • Common examples:
  │        ├─ Grade reduction = 1
  │        ├─ Time limit = 3
  │        ├─ Fail mark = 🔴
  │        ├─ Pass mark = 🟢
  │        └─ Case title format = 🧪 <<<case_title>>> — <<<test_result_mark>>>
  │
  ├─── # Cases sequence  (one or more "case = ..." blocks)
  ├───  Example case 1:
  │     ├─ case = test case 1
  │     ├─ input = 6 3
  │     └─ output = 2
  │
  ├───  Example case 2:
  │     ├─ case = test case 2
  │     ├─ input = 16 4
  │     └─ output = 4
  │
  ├───  Example case 3:
  │     ├─ case = test case 3
  │     ├─ input = 1 0
  │     └─ output = Zero division
  │
  └───  Example case N
        ├─ case = test case N
        ├─ input = -4 2
        └─ output = Negative number
```

**Notes**

* Each `case =` block can locally override global defaults.
* Each `output =` adds a **new** passing criterion (it does **not** replace previous ones).
* Cases are evaluated sequentially, in the written order.
* If an statement is repeated, the **last** value wins (except for `output =`).

---

#### Basic statements

* **Case =** one line with the case description (**required**)

  Example:
  >`Case = First test case for sum of n numbers`

* **Input =** text sent to `stdin` (can span multiple lines)

  Example:

  >```
  Input =3
  1
  2
  5
  ```

* **Output =** the expected result. You may set multiple `output =` lines to accept alternative valid answers.

  Example:

  >```
  Output = 8
  Output = Sum is eight
  ```

There are different output types; the type is **inferred from the value’s format**:

*If the `output` value is …*

* **Only numbers** → Then the "**numbers**" check applies. “To use this check type, ensure that you write only numbers, with nothing else. Numbers can be integers, or float in dot or scientific notation.
  When checking, non-numeric characters in the program output are ignored. For floating-point numbers, equality is determined using relative tolerance: `abs((expected - actual) / expected) < 0.0001` if expected == 0 then `abs(actual) < 0.0001` is used. Note that tolerance at this moment is a fixed value. For integers, exact equality is required. For integer defined in the "output=" statement expect integer in the program output. For float defined in the "output=" statement expect float or integer in the program output.

  Example:
  >`Output = 2 3.00001`

  *Matches:*

  * `Result is 2 and 3`
  * `Result is:`  
      `2`  
      `3`
  * `2 3.00001`
  * `2 - 3`
  * `2 3`

  *Does **not** match:*

  * `Result is 1, 2 and 3`
  * `2.0 3`
  * `2.3`
  * `Result is 2, 3 and 4`  
      `2 3`

* **Text in double quotes** → Then the "**exact text**" check applies.
  If the expected text does not end with new line, a trailing newline in the program output end is accepted, but trailing spaces are not.

  Example:  
  > `Output = "All·right"`

  *Matches:*

  * `All·right`  
  * `All·right↵`

  *Does **not** match:*
  
  * `all·right`
  * `all·right·`
  * `All··right↵`
  * `All·right·↵`

  Note that in these examples "·" means a space and "↵" a new line.

* **Plain text** → If the value does not match any other check type, then the "**text**" word-by-word check applies, GIOTES ignores punctuation, case, and line breaks, and matches the last sequence of words. This check type aims to be flexible with student output while still remaining testable.

  Example:
  >`Output = All right with 10 points`

  *Matches:*

  * `All right with 10 points.`
  * `My answer is: All right with 10 points.`
  * `All right with (10) points.`
  * `all right, with 10 POINTS!!!`
  * `  ALL "right" with ===>>>`  
      `  -10- points`

  *Does **not** match:*

  * `All right with 11 points`
  * `All right with 10 point`
  * `All right with points: 10`
  * `All right with 10 points, what else`

* **`/regex/[flags]`** → If output match this format then POSIX-C extended "**regular expression**" check applies (note: POSIX syntax differs from PCRE).

  Flags:

  * `i` → case-insensitive
  * `m` → multi-line (a correct **line** is enough to pass)

  Use escapes `\n`, `\r`, `\t`, and `\\` for newline, carriage return, tab, and backslash.
  Use `^` and `$` for full-content (or full-line with `m` flag) matches.

  Example:
  >`Output = /^(regex|no +regex|1{3,20})\n?$/i`

  *Matches:*

  * `regeX`
  * `no     regex`
  * `1111↵`
  * `11111111111111111`

  ❌ *Does **not** match:*

  * `egex`
  * `noregex`
  * `11`
  * `anything`  
      `no regex`
      `regex`

* **Wildcard `*`** for **numbers** and **exact text** check types you can use a starting wildcard — If the value starts with `*`, the case passes when the **end** of the program output matches. Note that **text** checks the output since it already behaves like a start wildcard; for **regular expressions**, you can use ".*" as wildcard inside the regular expression.

  Example:
  >`Output = * 2 3.00001`

  *Matches:*

  * `Result is 2 and 3`
  * `Result is:`  
      `1`  
      `2`  
      `3`
  * `0 1 2 2 2 3.00001`

  *Does **not** match:*

  * `Result is 2, 3 and 4`
  * `Result is 2, 3`  
      `2 3`  
      `3`

---

#### Statements to add pass conditions and penalties

* **Grade reduction =** *value* | *percent%* — Overrides the default penalty `grade_range / number_of_cases`. If grade reduction is greater than or equal to double of `grade_range` and the case fails the tests stop. This allows setting cases to stop the test process.

  Examples:
  >`Grade reduction = 1.5`  
  >`Grade reduction = 5%`  
  >`Grade reduction = 300%`

* **Time limit =** *seconds* — Per-case execution time limit.
  Overrides the default `global_time_limit / number_of_cases`.

  Example:
  >`Time limit = 2.5`

* **Expected exit code =** *integer* — Sets the expected program exit code. By default, exit code is ignored.

  * If **positive**: the case **passes if exit code matches OR output matches**.
  * If **negative** (the absolute value is used): the case **passes only if exit code matches AND output matches**.
  * If **0**: keeps the OR/AND mode previously selected in the case.

  Examples:
  >`Expected exit code = 3`  
  >`Expected exit code = -5`  
  >`Expected exit code = 0`

  **How output checks and exit code checks combine to determine whether a test case passes:**

    | Condition                          | Output (match) | Output (no match)|
    |------------------------------------|:--------------:|:----------------:|
    | **Exit code not set**              | ✅             | ❌              |
    | **Exit code positive (match)**     | ✅             | ✅              |
    | **Exit code positive (no match)**  | ✅             | ❌              |
    | **Exit code negative (match)**     | ✅             | ❌              |
    | **Exit code negative (no match)**  | ❌             | ❌              |

Note: Program exit codes themselves cannot be negative; a negative value here is only used to indicate the "AND" behavior.

---

#### Other control statements

* **Program to run =** *path* — Replaces the executable to test by the program at *path*.

  Example:
  >`Program to run = /usr/bin/cat`

* **Program args =** *arg1 arg2 …* — Arguments passed to the executable to test (or to **Program to run** if set).

  Example:
  >`Program args = output.txt`

* **Variation =** *variation\_id* — The case is considered only if the environment variable `VPL_VARIATION` equals *variation\_id* (case-insesitive).
  Otherwise, it is treated as if it does not exist.

  Example:
  >`Variation = variation_one`

---

#### 🖋️ Statements to customize the report

These statements are commonly set **globally** at the start of the file to standardize the report.
They can also be set **per case** to customize individual cases.

* **Fail message =** and **Fail output message =** — Custom text shown when the case fails (can span lines).

  Example:

  >```
  Fail output message=Executing your code with this input:
  <<<input>>>
  We expect: <<<expected_output_inline>>>
  But we get: <<<program_output_inline>>>
  ```

* **Pass message =** — Custom text shown when the case passes (default is no message).

  Example:

  >```
  Pass message=Great! Executing your code with this input:
  <<<input>>>
  We get the correct answer: <<<program_output_inline>>>
  ```

* **Timeout message =** — Custom text shown when the test case times out.

  Example:

  >```
  Timeout message=Your code may contain an infinite loop.
  Check that loop conditions change and you don’t have circular links in a linked list.
  ```

* **Fail exit code message =** — Custom text shown when the exit code does not match and the test case fails.

  Example:

  >```
  Fail exit code message=For this input your program exit code was wrong:
  <<<input>>>
  We expected: <<<expected_exit_code>>>
  But we got: <<<exit_code>>>
  ```

* **Case title format =** — Custom title format used when reporting each case.
  Default: `Test <<<case_id>>>: <<<case_title>>>`

  Example:
  >`Case title format = Prueba <<<case_id>>>/<<<num_tests>>>: <<<case_title>>> <<<test_result_mark>>>`

* **Multiline end =** *TOKEN* — The **next** multiline value statement expands until a line that exactly equals *TOKEN*. 
  This allows you include lines that would otherwise be parsed as new statements. This behavior applies only for the next statement.

  Example:

  >```
  Multiline end = END_OF_TEXT
  Input = this is an input
  that contains anything
  output= this line is part of the input
  next line ends the input
  END_OF_TEXT
  ```

---

#### Global-only statements

* **Fail mark / Pass mark / Timeout mark / Error mark** —
  These are commonly referenced via the `<<<test_result_mark>>>` placeholder.
  The mark expands according to the test result: *fail*, *pass*, *timeout*, or *error*.

  Example:

  >```
  Fail mark = [❌ wrong result]
  Pass mark = [✅ test passed]
  Error mark = [🛑 unexpected error]
  Timeout mark = [⏰ timeout]
  ```

* **Final report message =** — Message appended at the end of the test report.

  Example:

  >```
  Final report message = - Summary
  ✅ Tests passed <<<num_tests_passed>>>
  ❌ Tests failed <<<num_tests_failed>>>
  ```

* When the same statement appears more than once in the global setting or inside a case definition, the **last** one wins.

---

#### Placeholders

The placeholders have the format `<<<place_holder_name>>>` 🔖 and may be used in title (**T**), custom test case messages (**M**) and final report (**F**). The next table shows all placeholders, where are available and you can use it, and a description of what it expand.

| Placeholder                  | Avail  | Description                           |
| ------------------------------ |:------:| ---------------------------------------------- |
| `<<<case_id>>>`                | T M| The 1-based index of the test case.|
| `<<<case_title>>>`             | T M| The case title set with `case =`.|
| `<<<test_result_mark>>>`       | T M| Expands to one of the marks set by `Fail mark =`, `Pass mark =`, `Timeout mark =`, or `Error mark =`, depending on the test case result. |
| `<<<fail_mark>>>`              | T M| The text set by `Fail mark =`. |
| `<<<pass_mark>>>`              | T M| The text set by `Pass mark =`. |
| `<<<timeout_mark>>>`           | T M| The text set by `Timeout mark =`. |
| `<<<error_mark>>>`             | T M| The text set by `Error mark =`. |
| `<<<input>>>`                  | M| The text set by `Input =` (multiline, preformatted). |
| `<<<input_inline>>>`           | M| The `Input =` text in inline form; control codes and spaces are replaced (e.g., newline `↵`, space `␣`). |
| `<<<expected_output>>>`        | M| The text set in the **first** `Output =` of the case (multiline, preformatted). |
| `<<<expected_output_inline>>>` | M| The first `Output =` text in inline form; control codes and spaces are replaced (e.g., newline `↵`, space `␣`). |
| `<<<check_type>>>`             | M| The check type for the first `Output =` (one of: `numbers`, `text`, `exact text`, `regular expression`). |
| `<<<program_output>>>`         | M| The program output (multiline, preformatted). |
| `<<<program_output_inline>>>`  | M| The program output text in inline form; control codes and spaces are replaced (e.g., newline `↵`, space `␣`) |
| `<<<expected_exit_code>>>`     | M| The expected exit code set by `Expected exit code =`. |
| `<<<exit_code>>>`              | M| The actual exit code of the program run. |
| `<<<time_limit>>>`             | M| The time limit applied to the current test case. |
| `<<<grade_reduction>>>`        | M| The penalty applied if the case does not pass. |
| `<<<num_tests>>>`              | T M F| Total number of test cases (after filtering by variation). |
| `<<<num_tests_run>>>`          | F| Number of test cases actually run (may be less than `<<<num_tests>>>` if stopped by global timeout or an explicit stop). |
| `<<<num_tests_passed>>>`       | F| Number of run cases that passed. |
| `<<<num_tests_failed>>>`       | F| Number of run cases that failed due output mismatch or wrong exit code. |
| `<<<num_tests_timeout>>>`      | F| Number of run cases that timed out. |
| `<<<num_tests_error>>>`        | F| Number of run cases that ended with unexpected errors. |

Avail legend: T = Case title format, M = Custom messages, F = Final report

#### How the grade is calculated

1. `grade_range = VPL_GRADEMAX − VPL_GRADEMIN` (defaults are 10 − 0 = 10).
2. For each **not passed** case, GIOTES subtracts a penalty from the grade.
   By default the penalty is `grade_range / number_of_cases`.
3. A per-case **Grade reduction** replaces that default penalty (it can be absolute or a percent of `grade_range`).
4. The final grade is clamped to the activity’s grade range.

**Formula**

```
minimum_grade = VPL_GRADEMIN           (default 0)
maximum_grade = VPL_GRADEMAX           (default 10)
grade_range   = maximum_grade - minimum_grade

total_penalties = Σ(grade_reduction of each not-passed case)

final_grade = minimum_grade + (grade_range - total_penalties)
```

---

#### Environment variables recognized

* `VPL_GRADEMIN` (default `0`)
* `VPL_GRADEMAX` (default `10`)
* `VPL_MAXTIME` — total seconds for **all** cases (default `20`)
* `VPL_VARIATION` — current variation id (empty by default)

#### Example `vpl_evaluate.cases`

```
# Global defaults
Case title format = Test <<<case_id>>>: <<<case_title>>> <<<test_result_mark>>>
Fail output message = For input "<<<input_inline>>>":
Expected <<<expected_output_inline>>>, got <<<program_output_inline>>>
Timeout message = Your program took too long.
Final report message =
-Summary:
✅ Passed: <<<num_tests_passed>>>
❌ Failed: <<<num_tests_failed>>>
⏰ Timeouts: <<<num_tests_timeout>>>
🛑 Errors: <<<num_tests_error>>>

Fail mark = ❌
Pass mark = ✅
Timeout mark = ⏰
Error mark = 🛑
Grade reduction = 1
Time limit = 2

# --- Test cases ---

Case = Sum of two integers
Input = + 3 4
Output = 7
Output = "The result is 7"

Case = Division
Input = / 10 2
Output = 5
Pass message = Correct division!

Case = Division by zero
Input = / 1 0
Output = Zero division
Expected exit code = -1
# must match output AND exit code

Case = Slow execution
Input = loop
Output = Done
Time limit = 0.5

```

## License & authorship

© Copyright 2026, Juan Carlos Rodríguez-del-Pino [jc.rodriguezdelpino@ulpgc.es](mailto:jc.rodriguezdelpino@ulpgc.es).

This documentation is licensed under a 
[Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License](https://creativecommons.org/licenses/by-nc-nd/4.0/).

[![CC BY-NC-ND 4.0 License](https://licensebuttons.net/l/by-nc-nd/4.0/88x31.png)](https://creativecommons.org/licenses/by-nc-nd/4.0/)

---

*Enjoy automated grading with GIOTES!*

[1]: https://vpl.dis.ulpgc.es "Virtual Programming Lab for Moodle (VPL) documentation"
