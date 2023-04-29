<?php

namespace RTC\Utils;

use InvalidArgumentException;

trait EnumHelperTrait
{
    public static function count(): int
    {
        return count(static::cases());
    }

    public static function has(string $key): bool
    {
        return in_array(
            needle: strtolower($key),
            haystack: static::getDBCompatibleEnum()
        );
    }

    public static function getDBCompatibleEnum(bool $keyAsLowercase = true): array
    {
        return array_map(
            callback: fn($status) => $keyAsLowercase
                ? $status->lowercase()
                : $status->name,
            array: static::cases()
        );
    }

    public function lowercase(): string
    {
        return strtolower($this->name);
    }

    /**
     * @param array $cases
     * @return static[]
     */
    public static function casesExcept(array $cases): array
    {
        $cases = self::toDBUsable($cases);
        $enumCases = static::cases();
        $retCases = [];

        foreach ($enumCases as $enumCase) {
            if (in_array($enumCase->lowercase(), $cases)) {
                continue;
            }

            $retCases[] = $enumCase;
        }

        return $retCases;
    }

    protected static function toDBUsable(array $cases, bool $keyAsLowercase = true): array
    {
        return array_map(
            callback: fn($status) => $keyAsLowercase
                ? $status->lowercase()
                : $status->name,
            array: $cases
        );
    }

    public static function fromName(string $name): static
    {
        $name = strtoupper($name);
        foreach (static::cases() as $case) {
            if ($case->name == $name) {
                return $case;
            }
        }

        $className = get_class();
        throw new InvalidArgumentException("Case '$name' not found in '$className' enum");
    }

    /**
     * @return array<array{title: string, value: string}>
     */
    public static function optionCompatibleCases(): array
    {
        return array_map(
            callback: fn(string $ln) => ['value' => $ln, 'title' => ucfirst($ln)],
            array: self::getDBCompatibleEnum()
        );
    }

    /**
     * @return static
     */
    public static function random(): static
    {
        $values = static::cases();
        return $values[array_rand($values)];
    }

    /**
     * @return string
     */
    public static function randomValue(): string
    {
        return self::random()->lowercase();
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function equals(mixed $value): bool
    {
        return $this->lowercase() === $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isNot(string $name): bool
    {
        return !$this->is($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function is(string $name): bool
    {
        return $this->lowercase() == strtolower($name);
    }
}
