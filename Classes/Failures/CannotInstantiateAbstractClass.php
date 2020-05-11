<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Failures {

    use Exception;
    use ReflectionClass;

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