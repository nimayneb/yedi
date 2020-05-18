<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI {

    use JayBeeR\YEDI\Failures\{CannotFindClassName,
        CannotInstantiateClass,
        CannotReflectClass,
        ClassNameIsIncorrectlyCapitalized,
        DependencyIdentifierNotFound,
        InvalidTypeForDependencyIdentifier,
        InvalidTypeForDependencyInjection,
        MissingTypeForArgument,
        WrongArgumentsForDependencyResolution
    };
    use ReflectionException;

    /**
     *
     */
    trait DependencyInjectorConstructor
    {
        protected ?DependencyInjector $di;

        /**
         * @param DependencyInjector $di
         */
        public function __construct(DependencyInjector $di)
        {
            $this->di = $di;
            $this->injectDependencies();
        }

        /**
         * @param string $fullyClassName
         *
         * @return mixed
         * @throws CannotFindClassName
         * @throws CannotInstantiateClass
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws MissingTypeForArgument
         * @throws ReflectionException
         * @throws WrongArgumentsForDependencyResolution
         */
        protected function get(string $fullyClassName): object
        {
            return $this->di->get($fullyClassName);
        }

        /**
         * @return void
         */
        abstract protected function injectDependencies();
    }
}