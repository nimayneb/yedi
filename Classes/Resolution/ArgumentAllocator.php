<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Resolution {

    /**
     *
     */
    class ArgumentAllocator implements Arguments
    {
        /**
         * @var array
         */
        protected array $arguments = [];

        /**
         * @param string $argumentName
         *
         * @return ArgumentType
         */
        public function setArgument(string $argumentName): ArgumentType
        {
            return new ArgumentDelegation($this->arguments, $argumentName, $this);
        }

        /**
         * @return array
         */
        public function getArguments(): array
        {
            return $this->arguments;
        }
    }
} 