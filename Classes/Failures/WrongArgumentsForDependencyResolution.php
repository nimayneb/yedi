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
    class WrongArgumentsForDependencyResolution extends Exception
    {
        protected array $restArguments;

        /**
         * @param ReflectionClass $reflectedClass
         * @param array $arguments
         * @param array $restArguments
         */
        public function __construct(ReflectionClass $reflectedClass, array $arguments, array $restArguments)
        {
            $this->restArguments = $restArguments;

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
