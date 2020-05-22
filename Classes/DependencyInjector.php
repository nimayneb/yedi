<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI {

    use JayBeeR\YEDI\Container\{DependencyAliasContainer, DependencyResolutionContainer};
    use JayBeeR\YEDI\Failures\{CannotFindClassName,
        CannotInstantiateClass,
        CannotReconstructSingletonClass,
        CannotReflectClass,
        ClassNameIsIncorrectlyCapitalized,
        DependencyIdentifierNotFound,
        InvalidTypeForDependencyIdentifier,
        InvalidTypeForDependencyInjection,
        MissingTypeForArgument,
        WrongArgumentsForDependencyResolution
    };
    use JayBeeR\YEDI\Resolution\{ArgumentInjection, Arguments, ArgumentSingleton, DelegationType};
    use ReflectionClass;
    use ReflectionException;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess) Reason: because of Factory calls
     */
    class DependencyInjector
    {
        use ClassValidation;

        protected DependencyResolutionContainer $resolutionContainer;

        protected DependencyAliasContainer $aliasesContainer;

        /**
         * @param DependencyAliasContainer $aliasesContainer
         * @param DependencyResolutionContainer $resolverContainer
         */
        public function __construct(
            DependencyAliasContainer $aliasesContainer = null,
            DependencyResolutionContainer $resolverContainer = null
        )
        {
            $this->aliasesContainer = $aliasesContainer ?? new DependencyAliasContainer;
            $this->resolutionContainer = $resolverContainer ?? new DependencyResolutionContainer;
        }

        /**
         * @param ReflectionClass $reflectedClass
         * @param string $derivedClassName
         *
         * @return object
         * @throws CannotFindClassName
         * @throws CannotInstantiateClass
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws MissingTypeForArgument
         * @throws ReflectionException (cannot occur)
         * @throws WrongArgumentsForDependencyResolution
         */
        protected function resolveClass(ReflectionClass $reflectedClass, string $derivedClassName): object
        {
            $resolver = new DependencyResolver(
                $reflectedClass,
                $this->for($derivedClassName),
                $this
            );

            $arguments = $resolver->resolveConstructor();

            return $reflectedClass->newInstance(...$arguments);
        }

        /**
         * @param $fullyClassName
         * @param mixed ...$arguments
         *
         * @return object|mixed
         * @throws CannotFindClassName
         * @throws CannotInstantiateClass
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws ReflectionException (cannot occur)
         * @throws CannotReconstructSingletonClass
         */
        public function new($fullyClassName, ...$arguments): object
        {
            if ($this->aliasesContainer->has($fullyClassName)) {
                $object = $this->aliasesContainer->get($fullyClassName);

                if ($object instanceof ArgumentSingleton) {
                    throw new CannotReconstructSingletonClass($fullyClassName);
                } elseif ($object instanceof ArgumentInjection) {
                    $object = $this->instantiateClass(
                        $object->getClassName($fullyClassName),
                        $arguments
                    );
                }
            } else {
                $object = $this->instantiateClass($fullyClassName, $arguments);
            }

            return $object;
        }

        /**
         * @param string $fullyClassName
         * @param mixed ...$arguments
         *
         * @return object
         * @throws CannotFindClassName
         * @throws CannotInstantiateClass
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws ReflectionException
         */
        protected function instantiateClass(string $fullyClassName, array $arguments = []): object
        {
            $reflectedClass = $this->getReflectionClass($fullyClassName);
            $parameters = $reflectedClass->getConstructor()->getParameters();

            if (count($arguments)) {
                $parameters = array_slice($parameters, count($arguments));
            }

            $additionalArguments = [];

            foreach ($parameters as $parameter) {
                if (($parameter->hasType()) && ($parameter->getType() === DependencyInjector::class)) {
                    $additionalArguments[] = $this;

                    break;
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $additionalArguments[] = $parameter->getDefaultValue();
                }
            }

            $arguments = array_merge($arguments, $additionalArguments);

            if (count($arguments)) {
                $object = $reflectedClass->newInstanceArgs($arguments);
            } else {
                $object = $reflectedClass->newInstance();
            }

            return $object;
        }

        /**
         * @param string $fullyClassName
         *
         * @return object|mixed
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
        public function get(string $fullyClassName): object
        {
            if ($this->aliasesContainer->has($fullyClassName)) {
                $object = $this->aliasesContainer->get($fullyClassName);

                if ($object instanceof ArgumentSingleton) {
                    $object = $this->instantiateClassWithResolution(
                        $object->getClassName($fullyClassName)
                    );

                    $this->aliasesContainer->delegate($fullyClassName)->toSingleton($object);
                } elseif ($object instanceof ArgumentInjection) {
                    $object = $this->instantiateClassWithResolution(
                        $object->getClassName($fullyClassName)
                    );
                }
            } else {
                $object = $this->instantiateClassWithResolution($fullyClassName);
            }

            return $object;
        }

        /**
         * @param string $fullyClassName
         *
         * @return object
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
        protected function instantiateClassWithResolution(string $fullyClassName): object
        {
            $reflectedClass = $this->getReflectionClass($fullyClassName);

            if ((null === $reflectedClass->getConstructor())
                || (0 === $reflectedClass->getConstructor()->getNumberOfRequiredParameters())
            ) {
                $object = $reflectedClass->newInstance();
            } elseif (array_key_exists(
                DependencyInjectorConstructor::class,
                $reflectedClass->getTraits()
            )) {
                $object = $reflectedClass->newInstance($this);
            } else {
                $object = $this->resolveClass($reflectedClass, $fullyClassName);
            }

            return $object;
        }

        /**
         * @param string $fullyClassName
         *
         * @return ReflectionClass
         * @throws CannotFindClassName
         * @throws CannotInstantiateClass
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         */
        protected function getReflectionClass(string $fullyClassName): ReflectionClass
        {
            $this->assertValidObjectName($fullyClassName);

            $reflectedClass = Reflection::from($fullyClassName);

            if (!$reflectedClass->isInstantiable()) {
                throw new CannotInstantiateClass($reflectedClass);
            }

            return $reflectedClass;
        }

        /**
         * @param string $fullyClassName
         *
         * @return DelegationType
         * @throws CannotFindClassName
         */
        public function delegate(string $fullyClassName): DelegationType
        {
            return $this->aliasesContainer->delegate($fullyClassName);
        }

        /**
         * @param string $fullyClassName
         *
         * @return Arguments
         * @throws CannotFindClassName
         */
        public function for(string $fullyClassName): Arguments
        {
            return $this->resolutionContainer->for($fullyClassName);
        }
    }
}
