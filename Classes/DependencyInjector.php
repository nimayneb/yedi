<?php declare(strict_types=1);

namespace JayBeeR\YEDI {

    use ArrayAccess;
    use Closure;
    use Generator;
    use Iterator;
    use IteratorAggregate;
    use JayBeeR\YEDI\Container\DependencyAliasContainer;
    use JayBeeR\YEDI\Container\DependencyResolutionContainer;
    use JayBeeR\YEDI\Failures\CannotFindClassName;
    use JayBeeR\YEDI\Failures\CannotInstantiateAbstractClass;
    use JayBeeR\YEDI\Failures\CannotReflectClass;
    use JayBeeR\YEDI\Failures\ClassNameIsIncorrectlyCapitalized;
    use JayBeeR\YEDI\Failures\DependencyIdentifierNotFound;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyIdentifier;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyInjection;
    use JayBeeR\YEDI\Failures\WrongArgumentsForDependencyResolution;
    use ReflectionClass;
    use ReflectionParameter;
    use Serializable;
    use stdClass;
    use Throwable;
    use Traversable;
    use WeakReference;

    /**
     *
     */
    class DependencyInjector
    {
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
         * @param ReflectionParameter $reflectedParameter
         *
         * @return object
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws WrongArgumentsForDependencyResolution
         * @throws CannotFindClassName
         */
        protected function resolveType(ReflectionParameter $reflectedParameter): object
        {
            $fullyClassName = $reflectedParameter->getType()->getName();

            switch ($fullyClassName) {
                // Types
                case 'bool';
                case 'object';
                case 'array';
                case 'float';
                case 'string';
                case 'int';
                case 'iterable';

                // Predefined interfaces
                case Serializable::class;
                case Throwable::class;
                case ArrayAccess::class;

                // Predefined extended interfaces
                case Iterator::class;
                case IteratorAggregate::class;
                case Traversable::class;

                // Predefined classes
                case WeakReference::class;
                case Closure::class;

                // Predefined extended classes
                case Generator::class:
                {
                    throw new InvalidTypeForDependencyInjection($reflectedParameter);
                }

                // Predefined classes
                case stdClass::class;

                default:
                {
                    $instance = $this->get($fullyClassName);
                }
            }

            return $instance;
        }

        /**
         * @param ReflectionClass $reflectedClass
         * @param string $derivedClassName
         *
         * @return object
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws InvalidTypeForDependencyInjection
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws WrongArgumentsForDependencyResolution
         * @throws CannotFindClassName
         */
        protected function resolveClass(ReflectionClass $reflectedClass, string $derivedClassName): object
        {
            $arguments = $this->resolveArguments($reflectedClass, $derivedClassName);

            return $reflectedClass->newInstance(...$arguments);
        }

        /**
         * @param ReflectionClass $reflectedClass
         * @param string $derivedClassName
         *
         * @return array
         * @throws CannotFindClassName
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws WrongArgumentsForDependencyResolution
         */
        protected function resolveArguments(ReflectionClass $reflectedClass, string $derivedClassName)
        {
            $classNameContainer = $this->resolutionContainer->for($derivedClassName);
            $argumentsExists = $this->resolutionContainer->get($derivedClassName)->getArguments();
            $arguments = [];

            foreach ($reflectedClass->getConstructor()->getParameters() as $reflectedParameter) {
                $parameterName = $reflectedParameter->getName();

                if (
                    ($reflectedParameter->isOptional())
                    && (false === array_key_exists($parameterName, $argumentsExists))
                ) {
                    break;
                }

                unset($argumentsExists[$parameterName]);

                $fullyClassName = $reflectedParameter->getType()->getName();
                $classNameContainer->setArgument($parameterName)->asInjection($fullyClassName);

                $arguments[] = $this->resolveType($reflectedParameter);
            }

            if (count($argumentsExists)) {
                throw new WrongArgumentsForDependencyResolution($reflectedClass, $argumentsExists, $arguments);
            }

            return $arguments;
        }

        /**
         * @param string $fullyClassName
         *
         * @return mixed
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws WrongArgumentsForDependencyResolution
         * @throws CannotFindClassName
         */
        public function get(string $fullyClassName): object
        {
            Reflection::assertValidObjectName($fullyClassName);

            if ($this->aliasesContainer->has($fullyClassName)) {
                $fullyClassName = $this->aliasesContainer->get($fullyClassName);
            }

            $reflectedClass = Reflection::from($fullyClassName);

            if (
                (null === $reflectedClass->getConstructor())
                || (0 === $reflectedClass->getConstructor()->getNumberOfRequiredParameters())
            ) {
                $object = $reflectedClass->newInstance();
            } else {
                $object = $this->resolveClass($reflectedClass, $fullyClassName);
            }

            return $object;
        }

        /**
         * @param string $className
         *
         * @return AliasTo
         * @throws CannotFindClassName
         */
        public function delegate(string $className): AliasTo
        {
            return $this->aliasesContainer->delegate($className);
        }

        /**
         * @param string $className
         *
         * @return Arguments
         */
        public function for(string $className): Arguments
        {
            return $this->resolutionContainer->for($className);
        }
    }
} 