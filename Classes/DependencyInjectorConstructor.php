<?php declare(strict_types=1);

namespace JayBeeR\YEDI {

    use JayBeeR\YEDI\Failures\CannotFindClassName;
    use JayBeeR\YEDI\Failures\CannotReflectClass;
    use JayBeeR\YEDI\Failures\ClassNameIsIncorrectlyCapitalized;
    use JayBeeR\YEDI\Failures\DependencyIdentifierNotFound;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyIdentifier;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyInjection;
    use JayBeeR\YEDI\Failures\WrongArgumentsForDependencyResolution;
    use ReflectionException;

    trait DependencyInjectorConstructor
    {
        protected ?DependencyInjector $di = null;

        /**
         * @param string $fullyClassName
         *
         * @return mixed
         * @throws CannotFindClassName
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws Failures\MissingTypeForArgument
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws WrongArgumentsForDependencyResolution
         * @throws ReflectionException (cannot occur)
         */
        protected function get(string $fullyClassName): object
        {
            return $this->di->get($fullyClassName);
        }

        /**
         * @param DependencyInjector $di
         */
        public function __construct(DependencyInjector $di)
        {
            $this->di = $di;
            $this->injectDependencies();
        }

        /**
         * @return void
         */
        abstract public function injectDependencies();
    }
}