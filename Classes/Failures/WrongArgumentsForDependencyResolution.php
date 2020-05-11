<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Failures {

    use Exception;
    use ReflectionClass;

    class WrongArgumentsForDependencyResolution extends Exception
    {
        /**
         * @param ReflectionClass $reflectedClass
         * @param array $arguments
         * @param array $restArguments
         */
        public function __construct(ReflectionClass $reflectedClass, array $arguments, array $restArguments)
        {
            parent::__construct(
                sprintf(
                    'Wrong amount of arguments <%d> for dependency <%s> and their resolution (min: %d, max: %d)',
                    count($arguments),
                    $reflectedClass->getName(),
                    $reflectedClass->getConstructor()->getNumberOfRequiredParameters(),
                    count($reflectedClass->getConstructor()->getParameters())
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