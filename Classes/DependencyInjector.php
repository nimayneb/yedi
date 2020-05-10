<?php declare(strict_types=1);

namespace JayBeeR\YEDI {

    use ArrayAccess;
    use Closure;
    use Generator;
    use Iterator;
    use IteratorAggregate;
    use JayBeeR\YEDI\Container\DependencyAliasContainer;
    use JayBeeR\YEDI\Container\DependencyResolutionContainer;
    use JayBeeR\YEDI\Failures\CannotReflectClass;
    use JayBeeR\YEDI\Failures\ClassNameIsIncorrectlyCapitalized;
    use JayBeeR\YEDI\Failures\DependencyIdentifierNotFound;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyIdentifier;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyInjection;
    use JayBeeR\YEDI\Failures\WrongAmountOfArgumentForDependencyResolution;
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
        protected DependencyResolutionContainer $resolverContainer;

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
            $this->resolverContainer = $resolverContainer ?? new DependencyResolutionContainer;
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
         * @throws WrongAmountOfArgumentForDependencyResolution
         */
        public function resolveType(ReflectionParameter $reflectedParameter): object
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
         * @throws WrongAmountOfArgumentForDependencyResolution
         */
        public function resolveClass(ReflectionClass $reflectedClass, string $derivedClassName): object
        {
            if (!$this->resolverContainer->has($derivedClassName)) {
                $dependencyInjections = [];

                foreach ($reflectedClass->getConstructor()->getParameters() as $reflectedParameter) {
                    if (!$reflectedParameter->isOptional()) {
                        break;
                    }

                    $fullyClassName = $reflectedParameter->getType()->getName();

                    $this->resolverContainer
                        ->for($derivedClassName)
                        ->setArgument($reflectedParameter->getName())
                        ->asInjection($fullyClassName)
                    ;

                    $dependencyInjections[] = $this->resolveType($reflectedParameter);
                }

                $arguments = $this->resolverContainer->get($derivedClassName);
            } else {
                $arguments = $this->resolverContainer->get($derivedClassName);

                $min = $reflectedClass->getConstructor()->getNumberOfRequiredParameters();
                $max = count($reflectedClass->getConstructor()->getParameters());
                $count = count($arguments);

                if ($min > $count || $max < $count) {
                    throw new WrongAmountOfArgumentForDependencyResolution($reflectedClass, $arguments);
                }
            }

            return $reflectedClass->newInstance(...$arguments);
        }

        /**
         * @param string $className
         *
         * @return mixed
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws WrongAmountOfArgumentForDependencyResolution
         */
        public function get(string $className): object
        {
            if ($this->aliasesContainer->has($className)) {
                $className = $this->aliasesContainer->get($className);
            }

            $reflectedClass = Reflection::create($className);

            if (
                (null === $reflectedClass->getConstructor())
                || (0 === $reflectedClass->getConstructor()->getNumberOfRequiredParameters())
            ) {
                $object = $reflectedClass->newInstance();
            } else {
                $object = $this->resolveClass($reflectedClass, $className);
            }

            return $object;
        }
    }
} 