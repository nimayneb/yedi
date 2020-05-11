<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Container {

    use Ds\Map;
    use JayBeeR\YEDI\Arguments;
    use JayBeeR\YEDI\ArgumentResolution;
    use JayBeeR\YEDI\Failures\DependencyIdentifierNotFound;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyIdentifier;
    use Psr\Container\ContainerInterface;

    /**
     *
     */
    class DependencyResolutionContainer implements ContainerInterface
    {
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
         * @param mixed $className Identifier of the entry to look for.
         *
         * @return Arguments Entry.
         * @throws InvalidTypeForDependencyIdentifier Error while retrieving the entry.
         * @throws DependencyIdentifierNotFound  No entry was found for **this** identifier.
         */
        public function get($className): Arguments
        {
            if (!$this->has($className)) {
                throw new DependencyIdentifierNotFound($className);
            }

            return $this->resolvesDependencies->get($className);
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
            if (!is_string($className)) {
                throw new InvalidTypeForDependencyIdentifier($className);
            }

            return $this->resolvesDependencies->hasKey($className);
        }

        /**
         * @param string $derivedClassName
         *
         * @return Arguments
         */
        public function for(string $derivedClassName)
        {
            if (!$this->resolvesDependencies->hasKey($derivedClassName)) {
                $this->resolvesDependencies->put(
                    $derivedClassName,

                    new class () implements Arguments {
                        /**
                         * @var array
                         */
                        protected array $arguments = [];

                        /**
                         * @param string $argumentName
                         *
                         * @return ArgumentResolution
                         */
                        public function setArgument(string $argumentName): ArgumentResolution
                        {
                            return new class($this->arguments, $argumentName, $this) implements ArgumentResolution {
                                protected string $argumentName;

                                protected array $arguments;

                                protected Arguments $container;

                                /**
                                 * @param array $arguments
                                 * @param string $argumentName
                                 * @param Arguments $container
                                 */
                                public function __construct(array &$arguments, string $argumentName, Arguments $container)
                                {
                                    $this->arguments = &$arguments;
                                    $this->argumentName = $argumentName;
                                    $this->container = $container;
                                }

                                /**
                                 * @param $className
                                 *
                                 * @return Arguments
                                 */
                                public function asInjection(string $className): Arguments
                                {
                                    $this->arguments[$this->argumentName] = $className;

                                    return $this->container;
                                }

                                /**
                                 * @param mixed $value
                                 *
                                 * @return Arguments
                                 */
                                public function asValue($value): Arguments
                                {
                                    $this->arguments[$this->argumentName] = $value;

                                    return $this->container;
                                }
                            };
                        }

                        /**
                         * @return array
                         */
                        public function getArguments(): array
                        {
                            return $this->arguments;
                        }
                    }
                );
            }

            return $this->resolvesDependencies->get($derivedClassName);
        }
    }
}

