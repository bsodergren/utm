<?php
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
            'class' => function ($value) {
                $class = \str_replace('\\\\', '\\', $value);
                $class = \sprintf('%s::class', $class);

                return \mb_strpos($class, '\\') === 0 ? $class : '\\' . $class;
            },
            'integer' => function ($value) {
                return (string) $value;
            },
            'double' => function ($value) {
                return (string) $value;
            },
        ];
    }

    /**
     * Add a type resolver.
     *
     * @see http://php.net/manual/en/function.gettype.php for the supported types
     *
     * @param string   $type
     * @param \Closure $closure
     *
     * @return void
     */
    public function setResolver(string $type, \Closure $closure): void
    {
        $this->resolverCallbacks[$type] = $closure;
    }

    /**
     * Returns a pretty php array for saving or output.
     *
     * @param mixed[] $data
     * @param int     $indentLevel
     *
     * @return string
     */
    public function print(array $data, int $indentLevel = 1): string
    {
        $indent  = \str_repeat(' ', $indentLevel * 4);
        $entries = [];

        foreach ($data as $key => $value) {
            if (! \is_int($key)) {
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
                $this->createValue($value, $indentLevel)
            );
        }

        $outerIndent = \str_repeat(' ', ($indentLevel - 1) * 4);

        return \sprintf('[' . \PHP_EOL . '%s' . \PHP_EOL . '%s]', \implode(\PHP_EOL, $entries), $outerIndent);
    }

    /**
     * Create the right value.
     *
     * @param mixed $value
     * @param int   $indentLevel
     *
     * @return string
     */
    private function createValue($value, int $indentLevel): string
    {
        $type = \gettype($value);

        if ($type === 'array') {
            return $this->print($value, $indentLevel + 1);
        }

        if ($this->isClass($value)) {
            return $this->resolverCallbacks['class']($value);
        }

        if (isset($this->resolverCallbacks[$type])) {
            return $this->resolverCallbacks[$type]($value);
        }

        return \var_export($value, true);
    }

    /**
     * Check if entry is a class.
     *
     * @param mixed $key
     *
     * @return bool
     */
    private function isClass($key): bool
    {
        if (! \is_string($key)) {
            return false;
        }

        $key       = \ltrim($key, '\\');
        $firstChar = \mb_substr($key, 0, 1);

        return (\class_exists($key) || \interface_exists($key)) && \mb_strtolower($firstChar) !== $firstChar;
    }
}