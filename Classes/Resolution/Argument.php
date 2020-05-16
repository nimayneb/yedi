<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Resolution {

    /**
     *
     */
    abstract class Argument implements ClassNameGetter
    {
        protected ?string $className;

        /**
         * @param string $fullyClassName
         */
        public function __construct(?string $fullyClassName)
        {
            $this->className = $fullyClassName;
        }

        /**
         * @param string $derivedClassName
         *
         * @return string
         */
        public function getClassName(string $derivedClassName): ?string
        {
            return $this->className ?? $derivedClassName;
        }
    }
} 