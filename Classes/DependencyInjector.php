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
        WrongArgumentsForDependencyResolution};
    use JayBeeR\YEDI\Resolution\{ClassNameGetter, Injector, Singleton};
    use ReflectionClass;
    use ReflectionException;
    use ReflectionNamedType;
    use ReflectionParameter;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess) Reason: because of Factory calls
     */
    class DependencyInjector extends DependencyResolver
    {

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
         * @throws ReflectionException (cannot occur)
         * @throws WrongArgumentsForDependencyResolution
         */
        protected function resolveType(ReflectionParameter $reflectedParameter, ReflectionNamedType $reflectedType)
        {
            $fullyClassName = $reflectedType->getName();

            if ($reflectedType->isBuiltin()) {
                throw new InvalidTypeForDependencyInjection($reflectedParameter);
            }

            return $this->get($fullyClassName);
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
            $arguments = $this->resolveConstructor($reflectedClass, $derivedClassName);

            return $reflectedClass->newInstance(...$arguments);
        }

        /**
         * @param ReflectionClass $reflectedClass
         * @param string $derivedClassName
         *
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
        protected function resolveConstructor(ReflectionClass $reflectedClass, string $derivedClassName)
        {
            $this->currentArguments = $this->resolutionContainer->for($derivedClassName);
            $availableArguments = $this->resolutionContainer->get($derivedClassName)->getArguments();
            $arguments = [];

            // TODO: ... variadic - check of single type

            foreach ($reflectedClass->getConstructor()->getParameters() as $reflectedParameter) {
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
                    $reflectedClass,
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
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws WrongArgumentsForDependencyResolution
         * @throws MissingTypeForArgument
         * @throws ReflectionException (cannot occur)
         * @throws CannotInstantiateClass
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
            $this->currentArguments->setArgument($argumentName)->asInjection($fullyClassName);

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
         * @throws ReflectionException (cannot occur)
         * @throws WrongArgumentsForDependencyResolution
         */
        protected function resolveArgumentWithResolution(ReflectionParameter $reflectedParameter, $argumentResolution)
        {
            $argumentName = $reflectedParameter->getName();

            if ($argumentResolution instanceof Injector) {
                $argument = $this->resolveArgumentValue($reflectedParameter, $argumentResolution);
            } elseif ($argumentResolution instanceof Singleton) {
                $argument = $this->resolveArgumentValue($reflectedParameter, $argumentResolution);
                $this->currentArguments->setArgument($argumentName)->to($argument);
            } else {
                $argument = $argumentResolution;
            }

            return $argument;
        }

        /**
         * @param ReflectionParameter $reflectedParameter
         * @param ClassNameGetter $resolution
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
        protected function resolveArgumentValue(ReflectionParameter $reflectedParameter, ClassNameGetter $resolution)
        {
            $reflectedType = Reflection::getNamedType($reflectedParameter);
            $typeName = $reflectedType->getName();

            return $this->get($resolution->getClassName($typeName));
        }
    }
} 