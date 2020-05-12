<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Resolution {

    /**
     *
     */
    interface ClassNameGetter
    {
        /**
         * @param string $derivedClassName
         *
         * @return string
         */
        public function getClassName(string $derivedClassName): ?string;
    }
}