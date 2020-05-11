<?php declare(strict_types=1);

namespace JayBeeR\Tests {

    use JayBeeR\Tests\Unit\Fixtures\MyClassA;
    use JayBeeR\Tests\Unit\Fixtures\MyClassA_YEDI;
    use JayBeeR\Tests\Unit\Fixtures\MyClassB;
    use JayBeeR\Tests\Unit\Fixtures\MyClassB_YEDI;
    use JayBeeR\Tests\Unit\Fixtures\MyClassC;
    use JayBeeR\Tests\Unit\Fixtures\MyClassH;
    use JayBeeR\YEDI\DependencyInjector;
    use JayBeeR\YEDI\Failures\CannotFindClassName;
    use JayBeeR\YEDI\Failures\CannotReflectClass;
    use JayBeeR\YEDI\Failures\ClassNameIsIncorrectlyCapitalized;
    use JayBeeR\YEDI\Failures\DependencyIdentifierNotFound;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyIdentifier;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyInjection;
    use JayBeeR\YEDI\Failures\MissingTypeForArgument;
    use JayBeeR\YEDI\Failures\WrongArgumentsForDependencyResolution;
    use PHPUnit\Framework\TestCase;
    use ReflectionException;

    class DependencyInjectorTest extends TestCase
    {
        protected DependencyInjector $di;

        public function setUp()
        {
            $this->di = new DependencyInjector();
        }

        public function getDependenciesProvider(): array
        {
            return [
                [ MyClassA::class ],
                [ MyClassB::class ],
                [ MyClassC::class ],
                [ MyClassA_YEDI::class ],
                [ MyClassB_YEDI::class ]
            ];
        }

        /**
         * @param string $className
         *
         * @throws CannotFindClassName
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws MissingTypeForArgument
         * @throws ReflectionException (cannot occur)
         * @throws WrongArgumentsForDependencyResolution
         *
         * @dataProvider getDependenciesProvider
         * @test
         */
        public function get_returnsDependency(string $className): void
        {
            $object = $this->di->get($className);

            $this->assertEquals($className, get_class($object));
        }

        public function getDependenciesForAliasProvider(): array
        {
            return [
                [ MyClassB::class ],
                [ MyClassA_YEDI::class ]
            ];
        }

        /**
         * @param string $className
         *
         * @throws CannotFindClassName
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws WrongArgumentsForDependencyResolution
         * @throws MissingTypeForArgument
         * @throws ReflectionException (cannot occur)
         *
         * @dataProvider getDependenciesForAliasProvider
         * @test
         */
        public function get_ifAliasIsSet_returnsDependency(string $className)
        {
            $this->di->delegate(MyClassA::class)->to(MyClassH::class);

            $object = $this->di->get($className);

            $this->assertEquals($className, get_class($object));
            $this->assertInstanceOf(
                MyClassH::class,
                $object->myClassA,
                sprintf('Current is <%s>', get_class($object->myClassA))
            );
        }

        public function tearDown()
        {
            unset($this->di);
        }
    }
}
