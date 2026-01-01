<?php
/**
 * UTM Common classes
 */

declare(strict_types=1);

namespace UTM\Utilities\Debug;

final class PrettyArray
{
    /**
     * The default resolver for the array values.
     *
     * @var array
     */
    private $resolverCallbacks = [];

    /**
     * Create a new PrettyArray instance.
     */
    public function __construct()
    {
        $this->resolverCallbacks = [
            'class'   => function ($value) {
                $class = \str_replace('\\\\', '\\', $value);
                $class = \sprintf('%s::class', $class);

                return 0 === \mb_strpos($class, '\\') ? $class : '\\' . $class;
            },
            'integer' => function ($value) {
                return (string) $value;
            },
            'double'  => function ($value) {
                return (string) $value;
            },
        ];
    }

    /**
     * Add a type resolver.
     *
     * @see http://php.net/manual/en/function.gettype.php for the supported types
     */
    public function setResolver(string $type, \Closure $closure): void
    {
        $this->resolverCallbacks[$type] = $closure;
    }

    /**
     * Returns a pretty php array for saving or output.
     *
     * @param mixed[] $data
     */
    public function print(array $data, int $indentLevel = 1): string
    {
        $indentChar  = ' ';
        $indentMulti = 2;

        $indent  = \str_repeat($indentChar, $indentLevel * $indentMulti);
        $entries = [];

        foreach ($data as $key => $value) {
            if (!\is_int($key)) {
                if ($this->isClass($key)) {
                    $key = $this->resolverCallbacks['class']($key);
                } else {
                    $key = \sprintf("'%s'", $key);
                }
            }

            $entries[] = \sprintf(
                '%s%s%s,',
                $indent,
                \sprintf('%s => ', $key),
                $this->createValue($value, $indentLevel),
            );
        }

        $outerIndent = \str_repeat($indentChar, ($indentLevel - 1) * $indentMulti);
        if (count($entries) > 0) {
            return \sprintf(PHP_EOL . '[' . PHP_EOL . '%s' . PHP_EOL . '%s]', \implode(\PHP_EOL, $entries), $outerIndent);
        }

        return \sprintf('');
    }

    /**
     * Create the right value.
     */
    private function createValue($value, int $indentLevel): string
    {
        $type = \gettype($value);

        if ('array' === $type) {
            return $this->print($value, $indentLevel + 1);
        }

        if ($this->isClass($value)) {
            return $this->resolverCallbacks['class']($value);
        }

        if (isset($this->resolverCallbacks[$type])) {
            return $this->resolverCallbacks[$type]($value);
        }

        return @\var_export($value, true);
    }

    /**
     * Check if entry is a class.
     */
    private function isClass($key): bool
    {
        if (!\is_string($key)) {
            return false;
        }

        $key       = \ltrim($key, '\\');
        $firstChar = \mb_substr($key, 0, 1);

        return (\class_exists($key) || \interface_exists($key)) && \mb_strtolower($firstChar) !== $firstChar;
    }
}
