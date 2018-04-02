<?php

namespace EFrame\Sanitize;

use InvalidArgumentException;
use EFrame\Sanitize\Contracts;
use Illuminate\Contracts\Container\Container;

class Registrar implements Contracts\Registrar
{
    /** @var array */
    protected $registrations = [];

    /** @var \Illuminate\Contracts\Container\Container */
    private $container;

    /**
     * Create a new Laravel registrar instance.
     *
     * @param  \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     *
     * @return array|callable|mixed|object
     */
    public function resolve($name)
    {
        $value = $this->registrations[$name];

        if (is_string($value)) {
            // Check for `class@method` format.
            if (strpos($value, '@') >= 1) {
                $segments = explode('@', $value, 2);
                if ($this->container->bound($segments[0])) {
                    return [
                        $this->container->make($segments[0]), $segments[1],
                    ];
                }
            }

            if ($this->container->bound($name)) {
                return $this->container->make($name);
            }
        }

        if (
            $this->isRegistred($name) &&
            is_callable($this->registrations[$name])
        ) {
            return $this->registrations[$name];
        }

        throw new InvalidArgumentException(sprintf('Could not resolve [%s] from the registrar.', $name));
    }

    /**
     * @param string $name
     * @param mixed  $sanitizer
     *
     * @return bool
     */
    public function register($name, $sanitizer)
    {
        if ($this->isRegistred($name)) {
            return false;
        }
        $this->registrations[$name] = $sanitizer;

        return true;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isRegistred($name)
    {
        return isset($this->registrations[$name]);
    }
}