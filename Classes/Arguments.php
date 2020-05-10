<?php declare(strict_types=1);

namespace JayBeeR\YEDI {

    interface Arguments
    {
        /**
         * @param string $argumentName
         *
         * @return ArgumentResolution
         */
        public function setArgument(string $argumentName): ArgumentResolution;

        /**
         * @return array
         */
        public function getArguments(): array;
    }
}