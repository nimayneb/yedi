<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI {

    use JayBeeR\YEDI\Failures\CannotFindClassName;

    /**
     *
     */
    trait ClassValidation
    {
        /**
         * @param string $fullyClassName
         *
         * @throws CannotFindClassName
         */
        public function assertValidObjectName(string $fullyClassName): void
        {
            if (
                (!class_exists($fullyClassName))
                && (!interface_exists($fullyClassName))
                && (!trait_exists($fullyClassName))
            ) {
                throw new CannotFindClassName($fullyClassName);
            }
        }
    }
} 