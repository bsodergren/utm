<?php

/**
 * This file is part of the UTM package. (c) Bjorn
 */

namespace UTM\Rules;

use UTM\Rules;

trait FixerRules
{
    private static function getHeaderRules(): array
    {
        return [
            'header_comment' => [
                'header'       => Rules::$fileHeaderComment,
                'comment_type' => 'PHPDoc',
                'location'     => 'after_open',
                'separate'     => 'bottom',
            ],
        ];
    }

    public static function getCommonRules(): array
    {
        return [
            'cast_spaces'                                      => true,

            'array_indentation'                                => true,
            'array_syntax'                                     => ['syntax' => 'short'],
            'binary_operator_spaces'                           => [
                'operators' => [
                    '=>' => 'align_single_space_minimal_by_scope',
                    '='  => 'align_single_space_minimal',
                ],
            ],
            'blank_line_after_namespace'                       => true,
            'blank_line_after_opening_tag'                     => true,
            'blank_line_before_statement'                      => [
                'statements' => [
                    'continue',
                    'return',
                ],
            ],
            'blank_line_between_import_groups'                 => true,
            'blank_lines_before_namespace'                     => true,
            'assign_null_coalescing_to_coalesce_equal' => true,

        ];
    }

    public static function getRulesetOne(): array
    {
        return [

            'braces_position'                                  => [
                'control_structures_opening_brace'          => 'same_line',
                'functions_opening_brace'                   => 'next_line_unless_newline_at_signature_end',
                'anonymous_functions_opening_brace'         => 'same_line',
                'classes_opening_brace'                     => 'next_line_unless_newline_at_signature_end',
                'anonymous_classes_opening_brace'           => 'next_line_unless_newline_at_signature_end',
                'allow_single_line_empty_anonymous_classes' => false,
                'allow_single_line_anonymous_functions'     => false,
            ],
            'cast_spaces'                                      => true,
            'class_attributes_separation'                      => [
                'elements' => [
                    'const'        => 'one',
                    'method'       => 'one',
                    'property'     => 'one',
                    'trait_import' => 'none',
                    'case'         => 'none',
                ],
            ],
            'ordered_class_elements'                           => [
                'order' => [
                    'use_trait',
                ],
            ],
            'class_definition'                                 => [
                'multi_line_extends_each_single_line' => true,
                'single_item_single_line'             => true,
                'single_line'                         => true,
            ],
            'clean_namespace'                                  => true,
            'compact_nullable_type_declaration'                => true,
            'concat_space'                                     => [
                'spacing' => 'one',
            ],
            'constant_case'                                    => ['case' => 'lower'],
            'control_structure_braces'                         => true,
            'control_structure_continuation_position'          => [
                'position' => 'same_line',
            ],
            'declare_equal_normalize'                          => true,
            'declare_parentheses'                              => true,
            'elseif'                                           => true,
            'encoding'                                         => true,
            'full_opening_tag'                                 => true,
            'fully_qualified_strict_types'                     => false,
            'function_declaration'                             => true,
            'general_phpdoc_tag_rename'                        => true,
            'heredoc_to_nowdoc'                                => true,
            'include'                                          => true,
            'increment_style'                                  => ['style' => 'post'],
            'indentation_type'                                 => true,
            'integer_literal_case'                             => true,
            'lambda_not_used_import'                           => true,
            'line_ending'                                      => true,
            'linebreak_after_opening_tag'                      => true,
            'list_syntax'                                      => true,
            'lowercase_cast'                                   => true,
            'lowercase_keywords'                               => true,
            'lowercase_static_reference'                       => true,
            'magic_constant_casing'                            => true,
            'magic_method_casing'                              => true,
            'method_argument_space'                            => [
                'on_multiline' => 'ignore',
            ],
            'method_chaining_indentation'                      => true,
            'multiline_whitespace_before_semicolons'           => [
                'strategy' => 'no_multi_line',
            ],
            'native_function_casing'                           => true,
            'native_type_declaration_casing'                   => true,
            'new_with_parentheses'                             => [
                'named_class'     => false,
                'anonymous_class' => false,
            ],
            'no_alias_functions'                               => true,
            'no_alias_language_construct_call'                 => true,
            'no_alternative_syntax'                            => true,
            'no_binary_string'                                 => true,
            'no_blank_lines_after_class_opening'               => true,
            'no_blank_lines_after_phpdoc'                      => true,
            'no_closing_tag'                                   => true,
            'no_empty_phpdoc'                                  => true,
            'no_empty_statement'                               => true,
            'no_extra_blank_lines'                             => [
                'tokens' => [
                    'extra',
                    'throw',
                    'use',
                    'curly_brace_block',
                ],
            ],
            'no_leading_import_slash'                          => true,
            'no_leading_namespace_whitespace'                  => true,
            'no_mixed_echo_print'                              => [
                'use' => 'echo',
            ],
            'no_multiline_whitespace_around_double_arrow'      => true,
            'no_multiple_statements_per_line'                  => true,
            'no_short_bool_cast'                               => true,
            'no_singleline_whitespace_before_semicolons'       => true,
            'no_space_around_double_colon'                     => true,
            'no_spaces_after_function_name'                    => true,
            'no_spaces_around_offset'                          => [
                'positions' => ['inside', 'outside'],
            ],
            'no_superfluous_phpdoc_tags'                       => [
                'allow_mixed'         => true,
                'allow_unused_params' => true,
            ],
            'no_trailing_comma_in_singleline'                  => true,
            'no_trailing_whitespace'                           => true,
            'no_trailing_whitespace_in_comment'                => true,
            'no_unneeded_control_parentheses'                  => [
                'statements' => ['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield'],
            ],
            'no_unneeded_braces'                               => true,
            'no_unreachable_default_argument_value'            => true,
            'no_unset_cast'                                    => true,
            'no_unused_imports'                                => false,
            'spaces_inside_parentheses'                        => true,
            'single_trait_insert_per_statement'                => true,
            'no_useless_return'                                => true,
            'no_whitespace_before_comma_in_array'              => true,
            'no_whitespace_in_blank_line'                      => true,
            'normalize_index_brace'                            => true,
            'not_operator_with_successor_space'                => true,
            'nullable_type_declaration'                        => true,
            'nullable_type_declaration_for_default_null_value' => true,
            'object_operator_without_whitespace'               => true,
            'ordered_imports'                                  => ['sort_algorithm' => 'alpha', 'imports_order' => ['const', 'class', 'function']],
            'ordered_interfaces'                               => true,
            'ordered_traits'                                   => true,
            'phpdoc_align'                                     => [
                'align'   => 'left',
                'spacing' => [
                    'param' => 2,
                ],
            ],
            'phpdoc_indent'                                    => true,
            'phpdoc_inline_tag_normalizer'                     => true,
            'phpdoc_no_access'                                 => true,
            'phpdoc_no_package'                                => true,
            'phpdoc_no_useless_inheritdoc'                     => true,
            'phpdoc_order'                                     => [
                'order' => ['param', 'return', 'throws'],
            ],
            'phpdoc_scalar'                                    => true,
            'phpdoc_separation'                                => [
                'groups' => [
                    ['deprecated', 'link', 'see', 'since'],
                    ['author', 'copyright', 'license'],
                    ['category', 'package', 'subpackage'],
                    ['property', 'property-read', 'property-write'],
                    ['param', 'return'],
                ],
            ],
            'phpdoc_single_line_var_spacing'                   => true,
            'phpdoc_summary'                                   => false,
            'phpdoc_tag_type'                                  => [
                'tags' => [
                    'inheritdoc' => 'inline',
                ],
            ],
            'phpdoc_to_comment'                                => false,
            'phpdoc_trim'                                      => true,
            'phpdoc_types'                                     => true,
            'phpdoc_var_without_name'                          => true,
            'psr_autoloading'                                  => false,
            'return_type_declaration'                          => ['space_before' => 'none'],
            'self_accessor'                                    => false,
            'self_static_accessor'                             => true,
            'short_scalar_cast'                                => true,
            'simplified_null_return'                           => false,
            'single_blank_line_at_eof'                         => true,
            'single_class_element_per_statement'               => [
                'elements' => ['const', 'property'],
            ],
            'single_import_per_statement'                      => true,
            'single_line_after_imports'                        => true,
            'single_line_comment_style'                        => [
                'comment_types' => ['hash'],
            ],
            'single_line_empty_body'                           => true,
            'single_quote'                                     => true,
            'single_space_around_construct'                    => true,
            'space_after_semicolon'                            => true,
            'standardize_not_equals'                           => true,
            'statement_indentation'                            => true,
            'switch_case_semicolon_to_colon'                   => true,
            'switch_case_space'                                => true,
            'ternary_operator_spaces'                          => true,
            'trailing_comma_in_multiline'                      => ['elements' => ['arrays']],
            'trim_array_spaces'                                => true,
            'type_declaration_spaces'                          => true,
            'types_spaces'                                     => true,
            'unary_operator_spaces'                            => true,
            'modifier_keywords'                                => [
                'elements' => ['method', 'property'],
            ],
            'whitespace_after_comma_in_array'                  => true,
            'yoda_style'                                       => [
                'always_move_variable' => false,
                'equal'                => false,
                'identical'            => false,
                'less_and_greater'     => false,
            ],
        ];
    }

    public static function getRulesetTwo(): array
    {
        return [
            '@PER-CS2.0'                               => true,
            '@PER-CS2.0:risky'                         => true,
        ];
    }

    public static function getRulesetThree(): array
    {
        return
        [
            // Each line of multi-line DocComments must have an asterisk [PSR-5] and must be aligned with the first one.
            'align_multiline_comment'                          => true,
            // Each element of an array must be indented exactly once.
            // Converts simple usages of `array_push($x, $y);` to `$x[] = $y;`.
            'array_push'                                       => true,
            // PHP arrays should be declared using the configured syntax.
            // Use the null coalescing assignment operator `??=` where possible.
            // Converts backtick operators to `shell_exec` calls.
            'backtick_to_shell_exec'                           => true,
            // Binary operators should be surrounded by space as configured.
            // There MUST be one blank line after the namespace declaration.
            // Ensure there is no code on the same line as the PHP open tag and it is followed by a blank line.
            // An empty line feed must precede any configured statement.
            // Putting blank lines between `use` statement groups.
            // Controls blank lines before a namespace declaration.
            // Braces must be placed as configured.
            'braces_position'                                  => ['allow_single_line_anonymous_functions' => true, 'allow_single_line_empty_anonymous_classes' => true],
            // A single space or none should be between cast and variable.
            'cast_spaces'                                      => true,
            // Class, trait and interface elements must be separated with one or none blank line.
            'class_attributes_separation'                      => ['elements' => ['method' => 'one']],
            // Whitespace around the keywords of a class, trait, enum or interfaces definition should be one space.
            'class_definition'                                 => ['single_line' => true],
            // When referencing an internal class it must be written using the correct casing.
            'class_reference_name_casing'                      => true,
            // Namespace must not contain spacing, comments or PHPDoc.
            'clean_namespace'                                  => true,
            // Replace multiple nested calls of `dirname` by only one call with second `$level` parameter. Requires PHP >= 7.0.
            'combine_nested_dirname'                           => true,
            // Remove extra spaces in a nullable type declaration.
            'compact_nullable_type_declaration'                => true,
            // Concatenation should be spaced according to configuration.
            'concat_space'                                     => true,
            // The PHP constants `true`, `false`, and `null` MUST be written using the correct casing.
            'constant_case'                                    => true,
            // The body of each control structure MUST be enclosed within braces.
            'control_structure_braces'                         => true,
            // Control structure continuation keyword must be on the configured line.
            'control_structure_continuation_position'          => true,
            // Equal sign in declare statement should be surrounded by spaces or not following configuration.
            'declare_equal_normalize'                          => true,
            // There must not be spaces around `declare` statement parentheses.
            'declare_parentheses'                              => true,
            // Replaces `dirname(__FILE__)` expression with equivalent `__DIR__` constant.
            'dir_constant'                                     => true,
            // Replaces short-echo `<?=` with long format `<?php echo`/`<?php print` syntax, or vice-versa.
            'echo_tag_syntax'                                  => true,
            // The keyword `elseif` should be used instead of `else if` so that all control keywords look like single words.
            'elseif'                                           => true,
            // Empty loop-body must be in configured style.
            'empty_loop_body'                                  => ['style' => 'braces'],
            // Empty loop-condition must be in configured style.
            'empty_loop_condition'                             => true,
            // PHP code MUST use only UTF-8 without BOM (remove BOM).
            'encoding'                                         => true,
            // Replace deprecated `ereg` regular expression functions with `preg`.
            'ereg_to_preg'                                     => true,
            // Error control operator should be added to deprecation notices and/or removed from other cases.
            'error_suppression'                                => true,
            // Order the flags in `fopen` calls, `b` and `t` must be last.
            'fopen_flag_order'                                 => true,
            // The flags in `fopen` calls must omit `t`, and `b` must be omitted or included consistently.
            'fopen_flags'                                      => ['b_mode' => false],
            // PHP code must use the long `<?php` tags or short-echo `<?=` tags and not other tag variations.
            'full_opening_tag'                                 => true,
            // Removes the leading part of fully qualified symbol references if a given symbol is imported or belongs to the current namespace.
            'fully_qualified_strict_types'                     => true,
            // Spaces should be properly placed in a function declaration.
            'function_declaration'                             => true,
            // Replace core functions calls returning constants with the constants.
            'function_to_constant'                             => true,
            // Renames PHPDoc tags.
            'general_phpdoc_tag_rename'                        => ['replacements' => ['inheritDocs' => 'inheritDoc']],
            // Replace `get_class` calls on object variables with class keyword syntax.
            'get_class_to_class_keyword'                       => true,
            // Imports or fully qualifies global classes/functions/constants.
            'global_namespace_import'                          => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],
            // Add, replace or remove header comment.
            'header_comment'                                   => ['header' => self::$fileHeaderComment, 'comment_type' => 'PHPDoc', 'location' => 'after_open', 'separate' => 'bottom'],
            // Function `implode` must be called with 2 arguments in the documented order.
            'implode_call'                                     => true,
            // Include/Require and file path should be divided with a single space. File path should not be placed within parentheses.
            'include'                                          => true,
            // Pre- or post-increment and decrement operators should be used if possible.
            'increment_style'                                  => true,
            // Code MUST use configured indentation type.
            'indentation_type'                                 => true,
            // Integer literals must be in correct case.
            'integer_literal_case'                             => true,
            // Replaces `is_null($var)` expression with `null === $var`.
            'is_null'                                          => true,
            // Lambda must not import variables it doesn't use.
            'lambda_not_used_import'                           => true,
            // All PHP files must use same line ending.
            'line_ending'                                      => true,
            // Ensure there is no code on the same line as the PHP open tag.
            'linebreak_after_opening_tag'                      => true,
            // Use `&&` and `||` logical operators instead of `and` and `or`.
            'logical_operators'                                => true,
            // Shorthand notation for operators should be used if possible.
            'long_to_shorthand_operator'                       => true,
            // Cast should be written in lower case.
            'lowercase_cast'                                   => true,
            // PHP keywords MUST be in lower case.
            'lowercase_keywords'                               => true,
            // Class static references `self`, `static` and `parent` MUST be in lower case.
            'lowercase_static_reference'                       => true,
            // Magic constants should be referred to using the correct casing.
            'magic_constant_casing'                            => true,
            // Magic method definitions and calls must be using the correct casing.
            'magic_method_casing'                              => true,
            // In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma. Argument lists MAY be split across multiple lines, where each subsequent line is indented once. When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.
            'method_argument_space'                            => ['on_multiline' => 'ignore'],
            // Replace `strpos()` calls with `str_starts_with()` or `str_contains()` if possible.
            'modernize_strpos'                                 => true,
            // Replaces `intval`, `floatval`, `doubleval`, `strval` and `boolval` function calls with according type casting operator.
            'modernize_types_casting'                          => true,
            // Add leading `\` before constant invocation of internal constant to speed up resolving. Constant name match is case-sensitive, except for `null`, `false` and `true`.
            'native_constant_invocation'                       => ['strict' => false],
            // Function defined by PHP should be called using the correct casing.
            'native_function_casing'                           => true,
            // Add leading `\` before function invocation to speed up resolving.
            'native_function_invocation'                       => ['include' => ['@compiler_optimized'], 'scope' => 'namespaced', 'strict' => true],
            // Native type declarations should be used in the correct case.
            'native_type_declaration_casing'                   => true,
            // All instances created with `new` keyword must (not) be followed by parentheses.
            'new_with_parentheses'                             => ['anonymous_class' => false],
            // Master functions shall be used instead of aliases.
            'no_alias_functions'                               => true,
            // Master language constructs shall be used instead of aliases.
            'no_alias_language_construct_call'                 => true,
            // Replace control structure alternative syntax to use braces.
            'no_alternative_syntax'                            => true,
            // There should not be a binary flag before strings.
            'no_binary_string'                                 => true,
            // There should be no empty lines after class opening brace.
            'no_blank_lines_after_class_opening'               => true,
            // There should not be blank lines between docblock and the documented element.
            'no_blank_lines_after_phpdoc'                      => true,
            // There must be a comment when fall-through is intentional in a non-empty case body.
            'no_break_comment'                                 => true,
            // The closing `? >` tag MUST be omitted from files containing only PHP.
            'no_closing_tag'                                   => true,
            // There should not be any empty comments.
            'no_empty_comment'                                 => true,
            // There should not be empty PHPDoc blocks.
            'no_empty_phpdoc'                                  => true,
            // Remove useless (semicolon) statements.
            'no_empty_statement'                               => true,
            // Removes extra blank lines and/or blank lines following configuration.
            'no_extra_blank_lines'                             => ['tokens' => ['attribute', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'switch', 'throw', 'use']],
            // Replace accidental usage of homoglyphs (non ascii characters) in names.
            'no_homoglyph_names'                               => true,
            // Remove leading slashes in `use` clauses.
            'no_leading_import_slash'                          => true,
            // The namespace declaration line shouldn't contain leading whitespace.
            'no_leading_namespace_whitespace'                  => true,
            // Either language construct `print` or `echo` should be used.
            'no_mixed_echo_print'                              => true,
            // Operator `=>` should not be surrounded by multi-line whitespaces.
            'no_multiline_whitespace_around_double_arrow'      => true,
            // There must not be more than one statement per line.
            'no_multiple_statements_per_line'                  => true,
            // Properties MUST not be explicitly initialized with `null` except when they have a type declaration (PHP 7.4).
            'no_null_property_initialization'                  => true,
            // Convert PHP4-style constructors to `__construct`.
            'no_php4_constructor'                              => true,
            // Short cast `bool` using double exclamation mark should not be used.
            'no_short_bool_cast'                               => true,
            // Single-line whitespace before closing semicolon are prohibited.
            'no_singleline_whitespace_before_semicolons'       => true,
            // There must be no space around double colons (also called Scope Resolution Operator or Paamayim Nekudotayim).
            'no_space_around_double_colon'                     => true,
            // When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.
            'no_spaces_after_function_name'                    => true,
            // There MUST NOT be spaces around offset braces.
            'no_spaces_around_offset'                          => true,
            // Removes `@param`, `@return` and `@var` tags that don't provide any useful information.
            'no_superfluous_phpdoc_tags'                       => ['allow_hidden_params' => true, 'remove_inheritdoc' => true],
            // If a list of values separated by a comma is contained on a single line, then the last item MUST NOT have a trailing comma.
            'no_trailing_comma_in_singleline'                  => true,
            // Remove trailing whitespace at the end of non-blank lines.
            'no_trailing_whitespace'                           => true,
            // There MUST be no trailing spaces inside comment or PHPDoc.
            'no_trailing_whitespace_in_comment'                => true,
            // There must be no trailing whitespace in strings.
            'no_trailing_whitespace_in_string'                 => true,
            // Removes unneeded braces that are superfluous and aren't part of a control structure's body.
            'no_unneeded_braces'                               => ['namespaces' => true],
            // Removes unneeded parentheses around control statements.
            'no_unneeded_control_parentheses'                  => ['statements' => ['break', 'clone', 'continue', 'echo_print', 'others', 'return', 'switch_case', 'yield', 'yield_from']],
            // Removes `final` from methods where possible.
            'no_unneeded_final_method'                         => true,
            // Imports should not be aliased as the same name.
            'no_unneeded_import_alias'                         => true,
            // In function arguments there must not be arguments with default values before non-default ones.
            'no_unreachable_default_argument_value'            => true,
            // Variables must be set `null` instead of using `(unset)` casting.
            'no_unset_cast'                                    => true,
            // Unused `use` statements must be removed.
            'no_unused_imports'                                => true,
            // There should not be useless concat operations.
            'no_useless_concat_operator'                       => true,
            // There should not be useless Null-safe operator `?->` used.
            'no_useless_nullsafe_operator'                     => true,
            // There must be no `sprintf` calls with only the first argument.
            'no_useless_sprintf'                               => true,
            // In array declaration, there MUST NOT be a whitespace before each comma.
            'no_whitespace_before_comma_in_array'              => true,
            // Remove trailing whitespace at the end of blank lines.
            'no_whitespace_in_blank_line'                      => true,
            // Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols.
            'non_printable_character'                          => true,
            // Array index should always be written by using square braces.
            'normalize_index_brace'                            => true,
            // Nullable single type declaration should be standardised using configured syntax.
            'nullable_type_declaration'                        => true,
            // Adds or removes `?` before single type declarations or `|null` at the end of union types when parameters have a default `null` value.
            'nullable_type_declaration_for_default_null_value' => true,
            // There should not be space before or after object operators `->` and `?->`.
            'object_operator_without_whitespace'               => true,
            // Operators - when multiline - must always be at the beginning or at the end of the line.
            'operator_linebreak'                               => ['only_booleans' => true],
            // Orders the elements of classes/interfaces/traits/enums.
            'ordered_class_elements'                           => ['order' => ['use_trait']],
            // Ordering `use` statements.
            'ordered_imports'                                  => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
            // Trait `use` statements must be sorted alphabetically.
            'ordered_traits'                                   => true,
            // Sort union types and intersection types using configured order.
            'ordered_types'                                    => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
            // PHPUnit assertion method calls like `->assertSame(true, $foo)` should be written with dedicated method like `->assertTrue($foo)`.
            'php_unit_construct'                               => true,
            // PHPUnit annotations should be a FQCNs including a root namespace.
            'php_unit_fqcn_annotation'                         => true,
            // Enforce camel (or snake) case for PHPUnit test methods, following configuration.
            'php_unit_method_casing'                           => true,
            // Usage of PHPUnit's mock e.g. `->will($this->returnValue(..))` must be replaced by its shorter equivalent such as `->willReturn(...)`.
            'php_unit_mock_short_will_return'                  => true,
            // Changes the visibility of the `setUp()` and `tearDown()` functions of PHPUnit to `protected`, to match the PHPUnit TestCase.
            'php_unit_set_up_tear_down_visibility'             => true,
            // Adds or removes @test annotations from tests, following configuration.
            'php_unit_test_annotation'                         => true,
            // All items of the given PHPDoc tags must be either left-aligned or (by default) aligned vertically.
            'phpdoc_align'                                     => true,
            // PHPDoc annotation descriptions should not be a sentence.
            'phpdoc_annotation_without_dot'                    => true,
            // Docblocks should have the same indentation as the documented subject.
            'phpdoc_indent'                                    => true,
            // Fixes PHPDoc inline tags.
            'phpdoc_inline_tag_normalizer'                     => true,
            // `@access` annotations should be omitted from PHPDoc.
            'phpdoc_no_access'                                 => true,
            // No alias PHPDoc tags should be used.
            'phpdoc_no_alias_tag'                              => true,
            // `@package` and `@subpackage` annotations should be omitted from PHPDoc.
            'phpdoc_no_package'                                => true,
            // Classy that does not inherit must not have `@inheritdoc` tags.
            'phpdoc_no_useless_inheritdoc'                     => true,
            // Annotations in PHPDoc should be ordered in defined sequence.
            'phpdoc_order'                                     => ['order' => ['param', 'return', 'throws']],
            // The type of `@return` annotations of methods returning a reference to itself must the configured one.
            'phpdoc_return_self_reference'                     => true,
            // Scalar types should always be written in the same form. `int` not `integer`, `bool` not `boolean`, `float` not `real` or `double`.
            'phpdoc_scalar'                                    => true,
            // Annotations in PHPDoc should be grouped together so that annotations of the same type immediately follow each other. Annotations of a different type are separated by a single blank line.
            'phpdoc_separation'                                => ['groups' => [['Annotation', 'NamedArgumentConstructor', 'Target'], ['author', 'copyright', 'license'], ['category', 'package', 'subpackage'], ['property', 'property-read', 'property-write'], ['deprecated', 'link', 'see', 'since']]],
            // Single line `@var` PHPDoc should have proper spacing.
            'phpdoc_single_line_var_spacing'                   => true,
            // PHPDoc summary should end in either a full stop, exclamation mark, or question mark.
            'phpdoc_summary'                                   => true,
            // Forces PHPDoc tags to be either regular annotations or inline.
            'phpdoc_tag_type'                                  => ['tags' => ['inheritDoc' => 'inline']],
            // Docblocks should only be used on structural elements.
            'phpdoc_to_comment'                                => true,
            // PHPDoc should start and end with content, excluding the very first and last line of the docblocks.
            'phpdoc_trim'                                      => true,
            // Removes extra blank lines after summary and after description in PHPDoc.
            'phpdoc_trim_consecutive_blank_line_separation'    => true,
            // The correct case must be used for standard PHP types in PHPDoc.
            'phpdoc_types'                                     => true,
            // Sorts PHPDoc types.
            'phpdoc_types_order'                               => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
            // `@var` and `@type` annotations of classy properties should not contain the name.
            'phpdoc_var_without_name'                          => true,
            // Converts `pow` to the `**` operator.
            'pow_to_exponentiation'                            => true,
            // Classes must be in a path that matches their namespace, be at least one namespace deep and the class name should match the file name.
            'psr_autoloading'                                  => true,
            // Adjust spacing around colon in return type declarations and backed enum types.
            'return_type_declaration'                          => true,
            // Inside class or interface element `self` should be preferred to the class name itself.
            'self_accessor'                                    => true,
            // Instructions must be terminated with a semicolon.
            'semicolon_after_instruction'                      => true,
            // Cast shall be used, not `settype`.
            'set_type_to_cast'                                 => true,
            // Cast `(boolean)` and `(integer)` should be written as `(bool)` and `(int)`, `(double)` and `(real)` as `(float)`, `(binary)` as `(string)`.
            'short_scalar_cast'                                => true,
            // Converts explicit variables in double-quoted strings and heredoc syntax from simple to complex format (`${` to `{$`).
            'simple_to_complex_string_variable'                => true,
            // A PHP file without end tag must always end with a single empty line feed.
            'single_blank_line_at_eof'                         => true,
            // There MUST NOT be more than one property or constant declared per statement.
            'single_class_element_per_statement'               => true,
            // There MUST be one use keyword per declaration.
            'single_import_per_statement'                      => true,
            // Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block.
            'single_line_after_imports'                        => true,
            // Single-line comments must have proper spacing.
            'single_line_comment_spacing'                      => true,
            // Single-line comments and multi-line comments with only one line of actual content should use the `//` syntax.
            'single_line_comment_style'                        => ['comment_types' => ['hash']],
            // Empty body of class, interface, trait, enum or function must be abbreviated as `{}` and placed on the same line as the previous symbol, separated by a single space.
            'single_line_empty_body'                           => false,
            // Throwing exception must be done in single line.
            'single_line_throw'                                => true,
            // Convert double quotes to single quotes for simple strings.
            'single_quote'                                     => true,
            // Ensures a single space after language constructs.
            'single_space_around_construct'                    => true,
            // Each trait `use` must be done as single statement.
            'single_trait_insert_per_statement'                => true,
            // Fix whitespace after a semicolon.
            'space_after_semicolon'                            => ['remove_in_empty_for_expressions' => true],
            // Parentheses must be declared using the configured whitespace.
            'spaces_inside_parentheses'                        => true,
            // Increment and decrement operators should be used if possible.
            'standardize_increment'                            => true,
            // Replace all `<>` with `!=`.
            'standardize_not_equals'                           => true,
            // Each statement must be indented.
            'statement_indentation'                            => ['stick_comment_to_next_continuous_control_statement' => true],
            // String tests for empty must be done against `''`, not with `strlen`.
            'string_length_to_empty'                           => true,
            // All multi-line strings must use correct line ending.
            'string_line_ending'                               => true,
            // A case should be followed by a colon and not a semicolon.
            'switch_case_semicolon_to_colon'                   => true,
            // Removes extra spaces between colon and case value.
            'switch_case_space'                                => true,
            // Switch case must not be ended with `continue` but with `break`.
            'switch_continue_to_break'                         => true,
            // Standardize spaces around ternary operator.
            'ternary_operator_spaces'                          => true,
            // Use the Elvis operator `?:` where possible.
            'ternary_to_elvis_operator'                        => true,
            // Arguments lists, array destructuring lists, arrays that are multi-line, `match`-lines and parameters lists must have a trailing comma.
            'trailing_comma_in_multiline'                      => ['after_heredoc' => true, 'elements' => ['array_destructuring', 'arrays', 'match', 'parameters']],
            // Arrays should be formatted like function/method arguments, without leading or trailing single line space.
            'trim_array_spaces'                                => true,
            // Ensure single space between a variable and its type declaration in function arguments and properties.
            'type_declaration_spaces'                          => true,
            // A single space or none should be around union type and intersection type operators.
            'types_spaces'                                     => true,
            // Unary operators should be placed adjacent to their operands.
            'unary_operator_spaces'                            => true,
            // Visibility MUST be declared on all properties and methods; `abstract` and `final` MUST be declared before the visibility; `static` MUST be declared after the visibility.
            'visibility_required'                              => true,
            // In array declaration, there MUST be a whitespace after each comma.
            'whitespace_after_comma_in_array'                  => true,
            // Write conditions in Yoda style (`true`), non-Yoda style (`['equal' => false, 'identical' => false, 'less_and_greater' => false]`) or ignore those conditions (`null`) based on configuration.
            'yoda_style'                                       => true,
        ];
    }
}
