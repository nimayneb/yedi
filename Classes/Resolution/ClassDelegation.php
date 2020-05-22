<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Resolution {

    use Ds\Map;
    use JayBeeR\YEDI\Failures\{CannotFindClassName,
        CannotInstantiateClass,
        CannotReflectClass,
        ClassNameIsIncorrectlyCapitalized
    };
    use JayBeeR\YEDI\Reflection;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess) Reason: because of Factory calls
     */
    class ClassDelegation implements DelegationType
    {

        protected Map $aliases;

        protected string $fromClassName;

        /**
         * @param Map $aliases
         * @param string $fromClassName
         */
        public function __construct(Map $aliases, string $fromClassName)
        {
            $this->aliases = $aliases;
            $this->fromClassName = $fromClassName;
        }

        /**
         * @param string $fullyClassName
         *
         * @throws CannotFindClassName
         * @throws CannotInstantiateClass
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         */
        public function to(string $fullyClassName): void
        {
            $this->assertInstantiableClass($fullyClassName);

            // There is no need to check whether an abstract class, an interface or a trait has been used,
            //  because this will be checked during instantiation.

            $this->aliases->put($this->fromClassName, new ArgumentInjection($fullyClassName));
        }

        /**
         * @param string $fullyClassName
         *
         * @throws CannotFindClassName
         * @throws CannotInstantiateClass
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         */
        public function asSingleton(string $fullyClassName = null): void
        {
            $fullyClassName = $fullyClassName ?? $this->fromClassName;

            $this->assertInstantiableClass($fullyClassName);

            $singleton = new ArgumentSingleton($fullyClassName);

            $this->aliases->put($this->fromClassName, $singleton);
        }

        /**
         * @param object $object
         */
        public function toSingleton(object $object): void
        {
            $this->aliases->put($this->fromClassName, $object);
        }

        /**
         * @param string $fullyClassName
         *
         * @throws CannotFindClassName
         * @throws CannotInstantiateClass
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         */
        protected function assertInstantiableClass(string $fullyClassName)
        {
            if (!class_exists($fullyClassName)) {
                throw new CannotFindClassName($fullyClassName);
            }

            $reflectedClass = Reflection::from($fullyClassName);

            if (!$reflectedClass->isInstantiable()) {
                throw new CannotInstantiateClass($reflectedClass);
            }
        }
    }
}
