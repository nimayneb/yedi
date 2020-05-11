<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Failures {

    use Exception;
    use ReflectionParameter;

    class MissingTypeForArgument extends Exception
    {
        /**
         * @param ReflectionParameter $reflectedParameter
         */
        public function __construct(ReflectionParameter $reflectedParameter)
        {
            parent::__construct(
                sprintf(
                    'Missing type for argument <%s> in class <%s>',
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