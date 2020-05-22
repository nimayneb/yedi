<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Resolution {

    /**
     *
     */
    interface Arguments
    {
        /**
         * @param string $argumentName
         *
         * @return ArgumentType
         */
        public function setArgument(string $argumentName): ArgumentType;

        /**
         * @return array
         */
        public function getArguments(): array;
    }
}
