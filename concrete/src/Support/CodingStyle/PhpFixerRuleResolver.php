<?php

namespace Concrete\Core\Support\CodingStyle;

use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\WhitespacesFixerConfig;

class PhpFixerRuleResolver
{
    /**
     * PHP-CS-Fixer rules that can be safely applied to all files.
     * In order to view/edit them, open https://mlocati.github.io/php-cs-fixer-configurator/#configurator and paste there this array.
     *
     * @var array
     */
    protected const INVARIANT_RULES = [
        '@PER-CS' => true,

        // Unused `use` statements must be removed.
        'no_unused_imports' => true,

        // Each element of an array must be indented exactly once.
        'array_indentation' => true,

        // An empty line feed must precede any configured statement.
        'blank_line_before_statement' => ['statements' => ['return']],

        // Class, trait and interface elements must be separated with one blank line.
        'class_attributes_separation' => true,

        // Converts implicit variables into explicit ones in double-quoted strings or heredoc syntax.
        'explicit_string_variable' => true,

        // Ensure single space between function's argument and its typehint.
        'function_typehint_space' => true,

        // Convert `heredoc` to `nowdoc` where possible.
        'heredoc_to_nowdoc' => true,

        // Function `implode` must be called with 2 arguments in the documented order.
        'implode_call' => true,

        // Include/Require and file path should be divided with a single space. File path should not be placed under brackets.
        'include' => true,

        // Pre- or post-increment and decrement operators should be used if possible.
        'increment_style' => ['style' => 'post'],
        
        // Changes spaces and semicolons in inline PHP tags.
        'ConcreteCMS/inline_tag' => true,

        // Magic constants should be referred to using the correct casing.
        'magic_constant_casing' => true,

        // Magic method definitions and calls must be using the correct casing.
        'magic_method_casing' => true,

        // Method chaining MUST be properly indented. Method chaining with different levels of indentation is not supported.
        'method_chaining_indentation' => true,

        // Replaces `intval`, `floatval`, `doubleval`, `strval` and `boolval` function calls with according type casting operator.
        'modernize_types_casting' => true,

        // DocBlocks must start with two asterisks, multiline comments must start with a single asterisk, after the opening slash. Both must end with a single asterisk before the closing slash.
        'multiline_comment_opening_closing' => true,

        // Forbid multi-line whitespace before the closing semicolon or move the semicolon to the new line for chained calls.
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],

        // Function defined by PHP should be called using the correct casing.
        'native_function_casing' => true,

        // Native type hints for functions should use the correct case.
        'native_function_type_declaration_casing' => true,

        // All instances created with new keyword must be followed by braces.
        'new_with_braces' => true,

        // Master functions shall be used instead of aliases.
        'no_alias_functions' => true,

        // Replace control structure alternative syntax to use braces.
        'no_alternative_syntax' => true,

        // There should not be a binary flag before strings.
        'no_binary_string' => true,

        // There should not be any empty comments.
        'no_empty_comment' => true,

        // There should not be empty PHPDoc blocks.
        'no_empty_phpdoc' => true,

        // Remove useless semicolon statements.
        'no_empty_statement' => true,

        // Removes extra blank lines and/or blank lines following configuration.
        'no_extra_blank_lines' => ['tokens' => ['break', 'continue', 'curly_brace_block', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'throw', 'use']],

        // Replace accidental usage of homoglyphs (non ascii characters) in names.
        'no_homoglyph_names' => true,

        // The namespace declaration line shouldn't contain leading whitespace.
        'no_leading_namespace_whitespace' => true,

        // Either language construct `print` or `echo` should be used.
        'no_mixed_echo_print' => ['use' => 'echo'],

        // Operator ` => ` should not be surrounded by multi-line whitespaces.
        'no_multiline_whitespace_around_double_arrow' => true,

        // Properties MUST not be explicitly initialized with `null` except when they have a type declaration (PHP 7.4).
        'no_null_property_initialization' => true,

        // Short cast `bool` using double exclamation mark should not be used.
        'no_short_bool_cast' => true,

        // Single-line whitespace before closing semicolon are prohibited.
        'no_singleline_whitespace_before_semicolons' => true,

        // There MUST NOT be spaces around offset braces.
        'no_spaces_around_offset' => true,

        // There MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis.
        'no_spaces_inside_parenthesis' => true,

        // Replaces superfluous `elseif` with `if`.
        'no_superfluous_elseif' => true,

        // Remove trailing commas in list function calls.
        'no_trailing_comma_in_singleline' => true,

        // PHP single-line arrays should not have trailing comma.
        'no_trailing_comma_in_singleline_array' => true,

        // Removes unneeded parentheses around control statements.
        'no_unneeded_control_parentheses' => true,

        // Removes unneeded curly braces that are superfluous and aren't part of a control structure's body.
        'no_unneeded_curly_braces' => true,

        // A final class must not have final methods.
        'no_unneeded_final_method' => true,

        // Variables must be set `null` instead of using `(unset)` casting.
        'no_unset_cast' => true,

        // There should not be useless `else` cases.
        'no_useless_else' => true,

        // There should not be an empty `return` statement at the end of a function.
        'no_useless_return' => true,

        // In array declaration, there MUST NOT be a whitespace before each comma.
        'no_whitespace_before_comma_in_array' => true,

        // Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols.
        'non_printable_character' => ['use_escape_sequences_in_strings' => true],

        // Array index should always be written by using square braces.
        'normalize_index_brace' => true,

        // There should not be space before or after object `T_OBJECT_OPERATOR` `->`.
        'object_operator_without_whitespace' => true,

        // PHPDoc should contain `@param` for all params.
        'phpdoc_add_missing_param_annotation' => true,

        // All items of the given phpdoc tags must be either left-aligned or (by default) aligned vertically.
        'phpdoc_align' => ['align' => 'left'],

        // PHPDoc annotation descriptions should not be a sentence.
        'phpdoc_annotation_without_dot' => true,

        // Docblocks should have the same indentation as the documented subject.
        'phpdoc_indent' => true,

        // Fix PHPDoc inline tags, make `@inheritdoc` always inline.
        'phpdoc_inline_tag' => true,

        // Changes doc blocks from single to multi line, or reversed. Works for class constants, properties and methods only.
        'phpdoc_line_span' => true,

        // `@access` annotations should be omitted from PHPDoc.
        'phpdoc_no_access' => true,

        // No alias PHPDoc tags should be used.
        'phpdoc_no_alias_tag' => true,

        // Classy that does not inherit must not have `@inheritdoc` tags.
        'phpdoc_no_useless_inheritdoc' => true,

        // Annotations in PHPDoc should be ordered so that `@param` annotations come first, then `@throws` annotations, then `@return` annotations.
        'phpdoc_order' => true,

        // The type of `@return` annotations of methods returning a reference to itself must the configured one.
        'phpdoc_return_self_reference' => true,

        // Scalar types should always be written in the same form. `int` not `integer`, `bool` not `boolean`, `float` not `real` or `double`.
        'phpdoc_scalar' => true,

        // Annotations in PHPDoc should be grouped together so that annotations of the same type immediately follow each other, and annotations of a different type are separated by a single blank line.
        'phpdoc_separation' => true,

        // Single line `@var` PHPDoc should have proper spacing.
        'phpdoc_single_line_var_spacing' => true,

        // PHPDoc summary should end in either a full stop, exclamation mark, or question mark.
        'phpdoc_summary' => true,

        // PHPDoc should start and end with content, excluding the very first and last line of the docblocks.
        'phpdoc_trim' => true,

        // Removes extra blank lines after summary and after description in PHPDoc.
        'phpdoc_trim_consecutive_blank_line_separation' => true,

        // The correct case must be used for standard PHP types in PHPDoc.
        'phpdoc_types' => true,

        // Sorts PHPDoc types.
        'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],

        // `@var` and `@type` annotations must have type and name in the correct order.
        'phpdoc_var_annotation_correct_order' => true,

        // Local, dynamic and directly referenced variables should not be assigned and directly returned by a function or method.
        'return_assignment' => true,

        // Inside class or interface element `self` should be preferred to the class name itself.
        'self_accessor' => true,

        // Cast shall be used, not `settype`.
        'set_type_to_cast' => true,

        // Converts explicit variables in double-quoted strings and heredoc syntax from simple to complex format (`${` to `{$`).
        'simple_to_complex_string_variable' => true,

        // Single-line comments and multi-line comments with only one line of actual content should use the `//` syntax.
        'single_line_comment_style' => true,

        // Convert double quotes to single quotes for simple strings.
        'single_quote' => true,

        // Fix whitespace after a semicolon.
        'space_after_semicolon' => ['remove_in_empty_for_expressions' => true],

        // Replace all `<>` with `!=`.
        'standardize_not_equals' => true,

        // PHP multi-line arrays should have a trailing comma.
        'trailing_comma_in_multiline_array' => true,

        // Arrays should be formatted like function/method arguments, without leading or trailing single line space.
        'trim_array_spaces' => true,

        // In array declaration, there MUST be a whitespace after each comma.
        'whitespace_after_comma_in_array' => true,

        // Write conditions in Yoda style (`true`), non-Yoda style (`false`) or ignore those conditions (`null`) based on configuration.
        'yoda_style' => ['always_move_variable' => false, 'equal' => false, 'identical' => false, 'less_and_greater' => null],
    ];

    /**
     * @var \PhpCsFixer\FixerFactory
     */
    private $fixerFactory;

    /**
     * @param \PhpCsFixer\FixerFactory $fixerFactory
     */
    public function __construct(FixerFactory $fixerFactory)
    {
        $this->fixerFactory = $fixerFactory;
    }

    /**
     * Get a description of the specified flags.
     *
     * @param int $flags A combination of CodingStyle::FLAG_... flags.
     *
     * @return string
     */
    public function describeFlags($flags)
    {
        $flags = (int) $flags;
        if (($flags & PhpFixer::FLAG_PSR4CLASS) === PhpFixer::FLAG_PSR4CLASS) {
            $result = 'PHP-only files with the same name as the class it implements';
        } elseif (($flags & PhpFixer::FLAG_BOOTSTRAP) === PhpFixer::FLAG_BOOTSTRAP) {
            $result = 'PHP-only files with syntax compatible with old PHP versions';
        } elseif ($flags === 0) {
            $result = 'files with mixed contents';
        } else {
            $words = [];
            if (($flags & PhpFixer::FLAG_OLDPHP) === PhpFixer::FLAG_OLDPHP) {
                $words[] = 'files with syntax compatible with old PHP versions';
            }
            if (($flags & PhpFixer::FLAG_PHPONLY) === PhpFixer::FLAG_PHPONLY) {
                $words[] = 'PHP-only files';
            }
            $result = implode(', ', $words);
        }

        return $result;
    }

    /**
     * Get the rules associated to specific flags.
     *
     * @param int $flags A combination of PhpFixer::FLAG_... flags.
     * @param bool $onlyAvailableOnes return only the available rules
     * @param string $minimumPhpVersion the minimum PHP version that the files to be checked/fixed must be compatible with
     *
     * @return array
     */
    public function getRules($flags, $onlyAvailableOnes, $minimumPhpVersion = '')
    {
        $flags = (int) $flags;

        $hasFlag = function ($flag) use ($flags) {
            return ($flags & $flag) === $flag;
        };

        $rules = [
            // PHP arrays should be declared using the configured syntax.
            'array_syntax' => [
                'syntax' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '5.4') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? 'long' : 'short',
            ],

            // Ensure there is no code on the same line as the PHP open tag and it is followed by a blank line.
            'blank_line_after_opening_tag' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? true : false,

            // The body of each structure MUST be enclosed by braces.
            // Braces should be properly placed.
            // Body of braces should be properly indented.
            'braces' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? ['allow_single_line_closure' => true] : false,

            // Replace multiple nested calls of `dirname` by only one call with second `$level` parameter. Requires PHP >= 7.0.
            'combine_nested_dirname' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '7.0') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? false : true,

            // Replaces `dirname(__FILE__)` expression with equivalent `__DIR__` constant.
            'dir_constant' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '5.3') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? false : true,

            // Replaces short-echo `<?=` with long format `<?php echo`/`<?php print` syntax, or vice-versa.
            'echo_tag_syntax' => [
                'format' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '5.4') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? 'long' : 'short',
                'long_function' => 'echo',
            ],

            // Add curly braces to indirect variables to make them clear to understand. Requires PHP >= 7.0.
            'explicit_indirect_variable' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '7.0') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? false : true,

            // Code MUST use configured indentation type.
            'indentation_type' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? true : false,

            // Ensure there is no code on the same line as the PHP open tag.
            'linebreak_after_opening_tag' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? true : false,

            // List (`array` destructuring) assignment should be declared using the configured syntax. Requires PHP >= 7.1.
            'list_syntax' => [
                'syntax' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '7.1') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? 'long' : 'short',
            ],

            // There should not be blank lines between docblock and the documented element.
            'no_blank_lines_after_phpdoc' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? true : false,

            // Replace short-echo `<?=` with long format `<?php echo` syntax.
            'no_short_echo_tag' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '5.4') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? true : false,

            // Adds or removes `?` before type declarations for parameters with a default `null` value.
            'nullable_type_declaration_for_default_null_value' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '7.1') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? false : true,

            // `@var` and `@type` annotations should not contain the variable name.
            'phpdoc_var_without_name' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? true : false,

            // Converts `pow` to the `**` operator.
            'pow_to_exponentiation' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '5.6') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? false : true,

            // Class names should match the file name.
            'psr4' => $hasFlag(PhpFixer::FLAG_PSR4CLASS) ? true : false,

            // Instructions must be terminated with a semicolon.
            'semicolon_after_instruction' => $hasFlag(PhpFixer::FLAG_PHPONLY) ? true : false,

            // Use `null` coalescing operator `??` where possible. Requires PHP >= 7.0.
            'ternary_to_null_coalescing' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '7.0') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? false : true,

            // Visibility MUST be declared on all properties and methods; `abstract` and `final` MUST be declared before the visibility; `static` MUST be declared after the visibility.
            'visibility_required' => ['elements' => ($minimumPhpVersion !== '' && version_compare($minimumPhpVersion, '7.1') < 0) || $hasFlag(PhpFixer::FLAG_OLDPHP) ? ['property', 'method'] : ['const', 'property', 'method']],
        ] + static::INVARIANT_RULES;

        if ($onlyAvailableOnes) {
            $fixerFactory = $this->getFixerFactory();
            foreach (array_keys($rules) as $ruleName) {
                if ($ruleName[0] !== '@' && !$fixerFactory->hasRule($ruleName)) {
                    unset($rules[$ruleName]);
                }
            }
        }

        return $rules;
    }

    /**
     * Resolve the fixers associated to the rules.
     *
     * @param array $rules the list of the rules (as returned by the getRules method)
     *
     * @return \PhpCsFixer\Fixer\FixerInterface[]
     */
    public function getFixers(array $rules)
    {
        return $this->getFixerFactory()
            ->useRuleSet(new RuleSet($rules))
            ->setWhitespacesConfig(new WhitespacesFixerConfig())
            ->getFixers()
        ;
    }

    /**
     * @return \PhpCsFixer\FixerFactory
     */
    protected function getFixerFactory()
    {
        if (empty($this->fixerFactory->getFixers())) {
            $this->fixerFactory
                ->registerBuiltInFixers()
                ->registerCustomFixers([
                    new Fixer\InlineTagFixer(),
                ])
            ;
        }

        return clone $this->fixerFactory;
    }
}
