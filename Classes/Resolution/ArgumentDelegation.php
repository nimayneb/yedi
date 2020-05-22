<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Resolution {

    use JayBeeR\YEDI\Failures\CannotFindClassName;

    /**
     *
     */
    class ArgumentDelegation implements ArgumentType
    {
        protected string $argumentName;

        protected array $arguments;

        protected Arguments $container;

        /**
         * @param array $arguments
         * @param string $argumentName
         * @param Arguments $container
         */
        public function __construct(array &$arguments, string $argumentName, Arguments $container)
        {
            $this->arguments = &$arguments;
            $this->argumentName = $argumentName;
            $this->container = $container;
        }

        /**
         * @param string|null $fullyClassName
         *
         * @return Arguments
         */
        public function asInjection(?string $fullyClassName): Arguments
        {
            $this->arguments[$this->argumentName] = new ArgumentInjection($fullyClassName);

            return $this->container;
        }

        /**
         * @param mixed $value
         *
         * @return Arguments
         */
        public function to($value): Arguments
        {
            $this->arguments[$this->argumentName] = $value;

            return $this->container;
        }

        /**
         * @param string|null $fullyClassName
         *
         * @return Arguments
         * @throws CannotFindClassName
         */
        public function asSingleton(?string $fullyClassName): Arguments
        {
            if (!class_exists($fullyClassName)) {
                throw new CannotFindClassName($fullyClassName);
            }

            $this->arguments[$this->argumentName] = new ArgumentSingleton($fullyClassName);

            return $this->container;
        }
    }
}
