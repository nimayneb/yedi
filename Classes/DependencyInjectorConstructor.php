<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI {

    use JayBeeR\YEDI\Failures\{
        CannotFindClassName,
        CannotReflectClass,
        ClassNameIsIncorrectlyCapitalized,
        DependencyIdentifierNotFound,
        InvalidTypeForDependencyIdentifier,
        InvalidTypeForDependencyInjection,
        WrongArgumentsForDependencyResolution
    };

    use ReflectionException;

    /**
     *
     */
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