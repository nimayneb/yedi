<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Failures {

    use Exception;
    use ReflectionClass;

    class ClassNameIsIncorrectlyCapitalized extends Exception
    {
        /**
         * @param string $className
         * @param ReflectionClass $reflectedClass
         */
        public function __construct(string $className, ReflectionClass $reflectedClass)
        {
            parent::__construct(
                sprintf('Class name <%s> is incorrectly capitalized, must be <%s>', $className, $reflectedClass->getName())
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