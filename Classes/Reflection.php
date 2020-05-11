<?php declare(strict_types=1);

namespace JayBeeR\YEDI {

    use JayBeeR\YEDI\Failures\CannotFindClassName;
    use JayBeeR\YEDI\Failures\CannotReflectClass;
    use JayBeeR\YEDI\Failures\ClassNameIsIncorrectlyCapitalized;
    use ReflectionClass;
    use ReflectionException;

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
    }
} 