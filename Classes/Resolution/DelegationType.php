<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Resolution {

    /**
     *
     */
    interface DelegationType
    {
        /**
         * @param string $fullyClassName
         */
        public function to(string $fullyClassName): void;

        /**
         * @param string|null $fullyClassName
         */
        public function asSingleton(string $fullyClassName = null): void;

        /**
         * @param object $object
         */
        public function toSingleton(object $object): void;
    }
}