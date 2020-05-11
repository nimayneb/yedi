<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Container {

    use Ds\Map;
    use JayBeeR\YEDI\Arguments;
    use JayBeeR\YEDI\ArgumentResolution;
    use JayBeeR\YEDI\Failures\CannotFindClassName;
    use JayBeeR\YEDI\Failures\DependencyIdentifierNotFound;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyIdentifier;
    use JayBeeR\YEDI\Injector;
    use JayBeeR\YEDI\Reflection;
    use JayBeeR\YEDI\Singleton;
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
        public function for(string $derivedClassName)
        {
            Reflection::assertValidObjectName($derivedClassName);

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
                                 * @param $fullyClassName
                                 *
                                 * @return Arguments
                                 */
                                public function asInjection(?string $fullyClassName): Arguments
                                {
                                    $this->arguments[$this->argumentName] = new class($fullyClassName) implements Injector {
                                        /**
                                         * @var string
                                         */
                                        protected ?string $className;

                                        /**
                                         * @param string $fullyClassName
                                         */
                                        public function __construct(?string $fullyClassName) {
                                            $this->className = $fullyClassName;
                                        }

                                        /**
                                         * @param string $derivedClassName
                                         *
                                         * @return string
                                         */
                                        public function getClassName(string $derivedClassName): ?string
                                        {
                                            return $this->className ?? $derivedClassName;
                                        }
                                    };

                                    return $this->container;
                                }

                                /**
                                 * @param mixed $value
                                 *
                                 * @return Arguments
                                 */
                                public function to($value): Arguments
                                {
                                    $this->arguments[$this->argumentName] = $value;

                                    return $this->container;
                                }

                                /**
                                 * @param string|null $fullyClassName
                                 *
                                 * @return Arguments
                                 */
                                public function asSingleton(?string $fullyClassName): Arguments
                                {
                                    if (!class_exists($fullyClassName)) {
                                        throw new CannotFindClassName($fullyClassName);
                                    }

                                    $this->arguments[$this->argumentName] = new class($fullyClassName) implements Singleton {
                                        /**
                                         * @var string
                                         */
                                        protected ?string $className;

                                        /**
                                         * @param string $fullyClassName
                                         */
                                        public function __construct(?string $fullyClassName) {
                                            $this->className = $fullyClassName;
                                        }

                                        /**
                                         * @param string $derivedClassName
                                         *
                                         * @return string
                                         */
                                        public function getClassName(string $derivedClassName): ?string
                                        {
                                            return $this->className ?? $derivedClassName;
                                        }
                                    };

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

