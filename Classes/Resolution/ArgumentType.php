<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Resolution {

    /**
     *
     */
    interface ArgumentType
    {
        /**
         * @param $fullyClassName
         *
         * @return Arguments
         */
        public function asInjection(string $fullyClassName): Arguments;

        /**
         * @param mixed $value
         *
         * @return Arguments
         */
        public function to($value): Arguments;

        /**
         * @param string|null $fullyClassName
         *
         * @return Arguments
         */
        public function asSingleton(?string $fullyClassName): Arguments;
    }
}
