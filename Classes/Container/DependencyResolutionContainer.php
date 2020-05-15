<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Container {

    use Ds\Map;
    use JayBeeR\YEDI\ClassValidation;

    use JayBeeR\YEDI\Failures\{
        CannotFindClassName,
        DependencyIdentifierNotFound,
        InvalidTypeForDependencyIdentifier
    };

    use JayBeeR\YEDI\Resolution\{
        ArgumentAllocator,
        Arguments
    };

    use Psr\Container\ContainerInterface;

    /**
     *
     */
    class DependencyResolutionContainer implements ContainerInterface
    {
        use ClassValidation;

        protected Map $resolvesDependencies;

        /**
         *
         */
        public function __construct()
        {
            $this->resolvesDependencies = new Map;
        }

        /**
         * Finds an entry of the container by its identifier and returns it.
         *
         * @param mixed $fullyClassName Identifier of the entry to look for.
         *
         * @return Arguments Entry.
         * @throws InvalidTypeForDependencyIdentifier Error while retrieving the entry.
         * @throws DependencyIdentifierNotFound  No entry was found for **this** identifier.
         */
        public function get($fullyClassName): Arguments
        {
            if (!$this->has($fullyClassName)) {
                throw new DependencyIdentifierNotFound($fullyClassName);
            }

            return $this->resolvesDependencies->get($fullyClassName);
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
            if (!is_string($fullyClassName)) {
                throw new InvalidTypeForDependencyIdentifier($fullyClassName);
            }

            return $this->resolvesDependencies->hasKey($fullyClassName);
        }

        /**
         * @param string $derivedClassName
         *
         * @return Arguments
         * @throws CannotFindClassName
         */
        public function for(string $derivedClassName): Arguments
        {
            $this->assertValidObjectName($derivedClassName);

            if (!$this->resolvesDependencies->hasKey($derivedClassName)) {
                $this->resolvesDependencies->put($derivedClassName, new ArgumentAllocator);
            }

            return $this->resolvesDependencies->get($derivedClassName);
        }
    }
}

