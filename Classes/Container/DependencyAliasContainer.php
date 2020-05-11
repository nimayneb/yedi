<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Container {

    use Ds\Map;
    use JayBeeR\YEDI\AliasTo;
    use JayBeeR\YEDI\Failures\CannotFindClassName;
    use JayBeeR\YEDI\Failures\CannotInstantiateAbstractClass;
    use JayBeeR\YEDI\Failures\DependencyIdentifierNotFound;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyIdentifier;
    use JayBeeR\YEDI\Reflection;
    use Psr\Container\ContainerInterface;

    /**
     * The alias container should be used for the mapping of classes to be overwritten.
     * That should be classes or interfaces.
     *
     */
    class DependencyAliasContainer implements ContainerInterface
    {
        /**
         * @var Map
         */
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
         * @param mixed $className Identifier of the entry to look for.
         *
         * @return string Entry.
         * @throws InvalidTypeForDependencyIdentifier Error while retrieving the entry.
         * @throws DependencyIdentifierNotFound  No entry was found for **this** identifier.
         */
        public function get($className): string
        {
            // TODO: PSR-11 for PHP 7.4?
            if (!$this->has($className)) {
                throw new DependencyIdentifierNotFound($className);
            }

            return $this->aliases->get($className);
        }

        /**
         * Returns true if the container can return an entry for the given identifier.
         * Returns false otherwise.
         * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
         * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
         *
         * @param mixed $className Identifier of the entry to look for.
         *
         * @return bool
         * @throws InvalidTypeForDependencyIdentifier Error while retrieving the entry.
         */
        public function has($className): bool
        {
            // TODO: PSR-11 for PHP 7.4?
            if (!is_string($className)) {
                throw new InvalidTypeForDependencyIdentifier($className);
            }

            return $this->aliases->hasKey($className);
        }

        /**
         * @param $fullyClassName
         *
         * @return AliasTo
         * @throws CannotFindClassName
         */
        public function delegate($fullyClassName): AliasTo
        {
            Reflection::assertValidObjectName($fullyClassName);

            return new class ($this->aliases, $fullyClassName) implements AliasTo {

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
                 */
                public function to(string $fullyClassName): void
                {
                    if (!class_exists($fullyClassName)) {
                        throw new CannotFindClassName($fullyClassName);
                    }

                    $reflectedClass = Reflection::from($fullyClassName);

                    if ($reflectedClass->isAbstract()) {
                        throw new CannotInstantiateAbstractClass($reflectedClass);
                    }

                    // There is no need to check whether an abstract class, an interface or a trait has been used,
                    // because this will be checked during instantiation.

                    $this->aliases->put($this->fromClassName, $fullyClassName);
                }
            };
        }
    }
} 