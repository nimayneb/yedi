<?php declare(strict_types=1);


namespace JayBeeR\YEDI {

    use JayBeeR\YEDI\Failures\CannotReflectClass;
    use JayBeeR\YEDI\Failures\ClassNameIsIncorrectlyCapitalized;
    use JayBeeR\YEDI\Failures\DependencyIdentifierNotFound;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyIdentifier;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyInjection;
    use JayBeeR\YEDI\Failures\WrongArgumentsForDependencyResolution;

    trait DependencyInjectorGetter
    {
        protected ?DependencyInjector $injector = null;

        /**
         * @param string $fullyClassName
         *
         * @return mixed
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws WrongArgumentsForDependencyResolution
         */
        protected function get(string $fullyClassName): object
        {
            return $this->getInjector()->get($fullyClassName);
        }

        /**
         * @return DependencyInjector
         */
        protected function getInjector(): DependencyInjector
        {
            return $this->injector ?? $this->injector = new DependencyInjector;
        }
    }
}