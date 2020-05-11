<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Failures {

    use Exception;
    use ReflectionClass;

    class ClassNameIsIncorrectlyCapitalized extends Exception
    {
        /**
         * @param string $fullyClassName
         * @param ReflectionClass $reflectedClass
         */
        public function __construct(string $fullyClassName, ReflectionClass $reflectedClass)
        {
            parent::__construct(
                sprintf('Class name <%s> is incorrectly capitalized, must be <%s>', $fullyClassName, $reflectedClass->getName())
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