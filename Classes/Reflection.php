<?php declare(strict_types=1);

namespace JayBeeR\YEDI {

    use JayBeeR\YEDI\Failures\CannotFindClassName;
    use JayBeeR\YEDI\Failures\CannotReflectClass;
    use JayBeeR\YEDI\Failures\ClassNameIsIncorrectlyCapitalized;
    use JayBeeR\YEDI\Failures\MissingTypeForArgument;
    use ReflectionClass;
    use ReflectionException;
    use ReflectionNamedType;
    use ReflectionParameter;
    use ReflectionType;

    class Reflection
    {
        /**
         * @param $fullyClassName
         *
         * @return ReflectionClass
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         */
        public static function from($fullyClassName): ReflectionClass
        {
            try {
                $reflectedClass = new ReflectionClass($fullyClassName);
            } catch (ReflectionException $e) {
                throw new CannotReflectClass($fullyClassName);
            }

            if ((Defaults::$classNamesAreCaseSensitive) && ($fullyClassName !== $reflectedClass->getName())) {
                throw new ClassNameIsIncorrectlyCapitalized($fullyClassName, $reflectedClass);
            }

            return $reflectedClass;
        }

        /**
         * @param string $fullyClassName
         *
         * @throws CannotFindClassName
         */
        public static function assertValidObjectName(string $fullyClassName): void
        {
            if (
                (!class_exists($fullyClassName))
                && (!interface_exists($fullyClassName))
                && (!trait_exists($fullyClassName))
            ) {
                throw new CannotFindClassName($fullyClassName);
            }
        }

        /**
         * @param ReflectionParameter $reflectedParameter
         *
         * @return ReflectionNamedType|ReflectionType
         * @throws MissingTypeForArgument
         */
        public static function getNamedType(ReflectionParameter $reflectedParameter): ReflectionType
        {
            // TODO: PHP 8.0 variant return types?
            if (null === $reflectedParameter->hasType()) {
                throw new MissingTypeForArgument($reflectedParameter);
            }

            return $reflectedParameter->getType();
        }
    }
} 