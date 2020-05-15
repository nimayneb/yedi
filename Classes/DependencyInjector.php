<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI {

    use JayBeeR\YEDI\Container\DependencyAliasContainer;
    use JayBeeR\YEDI\Container\DependencyResolutionContainer;
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
    use JayBeeR\YEDI\Resolution\{AliasTo, Arguments};
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
         * @param string $fullyClassName
         *
         * @return mixed
         * @throws CannotFindClassName
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws MissingTypeForArgument
         * @throws ReflectionException (cannot occur)
         * @throws WrongArgumentsForDependencyResolution
         * @throws CannotInstantiateClass
         */
        public function get(string $fullyClassName): object
        {
            $this->assertValidObjectName($fullyClassName);

            if ($this->aliasesContainer->has($fullyClassName)) {
                $fullyClassName = $this->aliasesContainer->get($fullyClassName);
            }

            $reflectedClass = Reflection::from($fullyClassName);

            if (!$reflectedClass->isInstantiable()) {
                throw new CannotInstantiateClass($reflectedClass);
            }

            if (
                (null === $reflectedClass->getConstructor())
                || (0 === $reflectedClass->getConstructor()->getNumberOfRequiredParameters())
            ) {
                $object = $reflectedClass->newInstance();
            } elseif (array_key_exists(DependencyInjectorConstructor::class, $reflectedClass->getTraits())) {
                $object = $reflectedClass->newInstance($this);
            } else {
                $object = $this->resolveClass($reflectedClass, $fullyClassName);
            }

            return $object;
        }

        /**
         * @param string $fullyClassName
         *
         * @return AliasTo
         * @throws CannotFindClassName
         */
        public function delegate(string $fullyClassName): AliasTo
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