<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI {

    use JayBeeR\YEDI\Failures\{CannotReflectClass, ClassNameIsIncorrectlyCapitalized, MissingTypeForArgument};
    use ReflectionClass;
    use ReflectionException;
    use ReflectionNamedType;
    use ReflectionParameter;
    use ReflectionType;

    /**
     *
     */
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