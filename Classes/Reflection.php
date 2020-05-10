<?php declare(strict_types=1);

namespace JayBeeR\YEDI {

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
        public static function create($fullyClassName): ReflectionClass
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
    }
} 