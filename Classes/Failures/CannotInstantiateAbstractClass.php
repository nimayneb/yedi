<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Failures {

    use Exception;
    use ReflectionClass;

    /**
     *
     */
    class CannotInstantiateAbstractClass extends Exception
    {
        /**
         * @param ReflectionClass $reflectedClass
         */
        public function __construct(ReflectionClass $reflectedClass)
        {
            parent::__construct(
                sprintf('Cannot instantiate an abstract class named <%s>', $reflectedClass->getName())
            );
        }

        /**
         *
         */
        public function describe(): void
        {
            // TODO: Implement describe() method.
        }
    }
} 