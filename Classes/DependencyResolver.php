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
    use JayBeeR\YEDI\Resolution\{Arguments, ClassNameGetter, Injector, Singleton};
    use ReflectionClass;
    use ReflectionException;
    use ReflectionNamedType;
    use ReflectionParameter;

    /**
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Reason: because of Factory calls
     */
    class DependencyResolver
    {
        protected Arguments $allocator;

        protected ReflectionClass $reflection;

        protected DependencyInjector $di;

        /**
         * @param ReflectionClass $reflectedClass
         * @param Arguments $allocator
         * @param DependencyInjector $di
         */
        public function __construct(ReflectionClass $reflectedClass, Arguments $allocator, DependencyInjector $di)
        {
            $this->reflection = $reflectedClass;
            $this->allocator = $allocator;
            $this->di = $di;
        }

        /**
         * @return array
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
        public function resolveConstructor()
        {
            $availableArguments = $this->allocator->getArguments();
            $arguments = [];

            // TODO: ... variadic - check of single type

            foreach ($this->reflection->getConstructor()->getParameters() as $reflectedParameter) {
                if (false === $this->resolveArgument(
                        $reflectedParameter,
                        $availableArguments,
                        $arguments
                    )) {

                    break;
                }
            }

            if (count($availableArguments)) {
                throw new WrongArgumentsForDependencyResolution(
                    $this->reflection,
                    $availableArguments,
                    $arguments
                );
            }

            return $arguments;
        }

        /**
         * @param ReflectionParameter $reflectedParameter
         * @param array $availableArguments
         * @param array $arguments
         *
         * @return bool
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
        protected function resolveArgument(
            ReflectionParameter $reflectedParameter,
            array &$availableArguments,
            array &$arguments
        ): bool
        {
            $argumentName = $reflectedParameter->getName();
            $argumentExists = array_key_exists($argumentName, $availableArguments);

            if ((null === $reflectedParameter->getType()) && (false === $argumentExists)) {
                if (!$reflectedParameter->isOptional()) {
                    // wo/ argument, this is not possible:
                    // - public function __construct($variable);
                    throw new MissingTypeForArgument($reflectedParameter);
                }

                return false;
            } elseif (
                (
                    (null !== $reflectedParameter->getType())
                    && ($reflectedParameter->getType()->isBuiltin())
                ) && (false === $argumentExists)
            ) {
                if (!$reflectedParameter->isOptional()) {
                    // wo/ argument, this is not possible:
                    // - public function __construct(bool $variable);
                    // - public function __construct(int $variable);
                    // - public function __construct(float $variable);
                    // - public function __construct(string $variable);
                    // - public function __construct(object $variable);
                    // - public function __construct(array $variable);
                    throw new InvalidTypeForDependencyInjection($reflectedParameter);
                }

                return false;
            }

            if (!$argumentExists) {
                $arguments[] = $this->resolveArgumentWithoutResolution($reflectedParameter);
            } else {
                $arguments[] = $this->resolveArgumentWithResolution(
                    $reflectedParameter,
                    $availableArguments[$argumentName]
                );

                unset($availableArguments[$argumentName]);
            }

            return true;
        }

        /**
         * @param ReflectionParameter $reflectedParameter
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
         * @throws ReflectionException (cannot occur)
         * @throws WrongArgumentsForDependencyResolution
         */
        protected function resolveArgumentWithoutResolution(ReflectionParameter $reflectedParameter)
        {
            $argumentName = $reflectedParameter->getName();
            $reflectedType = Reflection::getNamedType($reflectedParameter);
            $fullyClassName = $reflectedType->getName();
            $this->allocator->setArgument($argumentName)->asInjection($fullyClassName);

            if ($reflectedParameter->isDefaultValueAvailable()) {
                $argument = $reflectedParameter->getDefaultValue();
            } else {
                $argument = $this->resolveType($reflectedParameter, $reflectedType);
            }

            return $argument;
        }

        /**
         * @param ReflectionParameter $reflectedParameter
         * @param $argumentResolution
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
        protected function resolveArgumentWithResolution(ReflectionParameter $reflectedParameter, $argumentResolution)
        {
            $argumentName = $reflectedParameter->getName();

            if ($argumentResolution instanceof Injector) {
                $argument = $this->resolveArgumentValue($reflectedParameter, $argumentResolution);
            } elseif ($argumentResolution instanceof Singleton) {
                $argument = $this->resolveArgumentValue($reflectedParameter, $argumentResolution);
                $this->allocator->setArgument($argumentName)->to($argument);
            } else {
                $argument = $argumentResolution;
            }

            return $argument;
        }

        /**
         * @param ReflectionParameter $reflectedParameter
         * @param ReflectionNamedType $reflectedType
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
        protected function resolveType(ReflectionParameter $reflectedParameter, ReflectionNamedType $reflectedType)
        {
            $fullyClassName = $reflectedType->getName();

            if ($reflectedType->isBuiltin()) {
                throw new InvalidTypeForDependencyInjection($reflectedParameter);
            }

            return $this->di->get($fullyClassName);
        }

        /**
         * @param ReflectionParameter $reflectedParameter
         * @param ClassNameGetter $resolution
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
        protected function resolveArgumentValue(ReflectionParameter $reflectedParameter, ClassNameGetter $resolution)
        {
            $reflectedType = Reflection::getNamedType($reflectedParameter);
            $typeName = $reflectedType->getName();

            return $this->di->get($resolution->getClassName($typeName));
        }
    }

} 