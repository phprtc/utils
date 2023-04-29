<?php

namespace RTC\Utils;

use JetBrains\PhpStorm\Pure;

trait InstanceCreator
{
    /**
     * @var static $_instance
     */
    protected static $instance;

    public static function createSingleton(...$arguments): static
    {
        if (!isset(static::$instance)) {
            return static::$instance = self::create(...$arguments);
        }

        return self::$instance;
    }

    #[Pure] public static function create(...$arguments): static
    {
        return new static(...$arguments);
    }
}