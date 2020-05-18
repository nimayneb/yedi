<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Container {

    use Ds\Map;
    use JayBeeR\YEDI\ClassValidation;
    use JayBeeR\YEDI\Failures\{CannotFindClassName, DependencyIdentifierNotFound, InvalidTypeForDependencyIdentifier};
    use JayBeeR\YEDI\Resolution\{ClassDelegation, DelegationType};
    use Psr\Container\ContainerInterface;

    /**
     * The alias container should be used for the mapping of classes to be overwritten.
     * That should be classes or interfaces.
     */
    class DependencyAliasContainer implements ContainerInterface
    {
        use ClassValidation;

        protected Map $aliases;

        /**
         *
         */
        public function __construct()
        {
            $this->aliases = new Map;
        }

        /**
         * Finds an entry of the container by its identifier and returns it.
         *
         * @param mixed $fullyClassName Identifier of the entry to look for.
         *
         * @return object Entry.
         * @throws InvalidTypeForDependencyIdentifier Error while retrieving the entry.
         * @throws DependencyIdentifierNotFound  No entry was found for **this** identifier.
         */
        public function get($fullyClassName): object
        {
            // TODO: PSR-11 for PHP 7.4?
            if (!$this->has($fullyClassName)) {
                throw new DependencyIdentifierNotFound($fullyClassName);
            }

            return $this->aliases->get($fullyClassName);
        }

        /**
         * Returns true if the container can return an entry for the given identifier.
         * Returns false otherwise.
         * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
         * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
         *
         * @param mixed $fullyClassName Identifier of the entry to look for.
         *
         * @return bool
         * @throws InvalidTypeForDependencyIdentifier Error while retrieving the entry.
         */
        public function has($fullyClassName): bool
        {
            // TODO: PSR-11 for PHP 7.4?
            if (!is_string($fullyClassName)) {
                throw new InvalidTypeForDependencyIdentifier($fullyClassName);
            }

            return $this->aliases->hasKey($fullyClassName);
        }

        /**
         * @param $fullyClassName
         *
         * @return DelegationType
         * @throws CannotFindClassName
         */
        public function delegate($fullyClassName): DelegationType
        {
            $this->assertValidObjectName($fullyClassName);

            return new ClassDelegation($this->aliases, $fullyClassName);
        }
    }
} 