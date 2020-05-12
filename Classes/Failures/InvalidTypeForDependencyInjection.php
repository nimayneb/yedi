<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Failures {

    use Exception;
    use ReflectionParameter;

    /**
     *
     */
    class InvalidTypeForDependencyInjection extends Exception
    {
        /**
         * @param ReflectionParameter $reflectedParameter
         */
        public function __construct(ReflectionParameter $reflectedParameter)
        {
            parent::__construct(
                sprintf(
                    'Invalid type <%s> for dependency injection in class <%s>',
                    $reflectedParameter->getType()->getName(),
                    $reflectedParameter->getDeclaringClass()->getName()
                )
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