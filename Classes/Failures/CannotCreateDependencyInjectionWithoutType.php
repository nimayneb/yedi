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
    class CannotCreateDependencyInjectionWithoutType extends Exception
    {
        /**
         * @param ReflectionParameter $reflectedParameter
         */
        public function __construct(ReflectionParameter $reflectedParameter)
        {
            parent::__construct(
                sprintf(
                    'Cannot create dependency injection without type for argument <%s> in class <%s>',
                    $reflectedParameter->getName(),
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