<?php declare(strict_types=1);

namespace JayBeeR\Tests {

    use JayBeeR\Tests\Unit\Fixtures\MyClassA;
    use JayBeeR\Tests\Unit\Fixtures\MyClassA_DI;
    use JayBeeR\Tests\Unit\Fixtures\MyClassB;
    use JayBeeR\Tests\Unit\Fixtures\MyClassB_DI;
    use JayBeeR\Tests\Unit\Fixtures\MyClassC;
    use JayBeeR\Tests\Unit\Fixtures\MyClassH;
    use JayBeeR\YEDI\DependencyInjector;
    use JayBeeR\YEDI\Failures\CannotFindClassName;
    use JayBeeR\YEDI\Failures\CannotReflectClass;
    use JayBeeR\YEDI\Failures\ClassNameIsIncorrectlyCapitalized;
    use JayBeeR\YEDI\Failures\DependencyIdentifierNotFound;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyIdentifier;
    use JayBeeR\YEDI\Failures\InvalidTypeForDependencyInjection;
    use JayBeeR\YEDI\Failures\WrongArgumentsForDependencyResolution;
    use PHPUnit\Framework\TestCase;

    class DependencyInjectorTest extends TestCase
    {
        protected DependencyInjector $di;

        public function setUp()
        {
            $this->di = new DependencyInjector;

            parent::setUp();
        }

        public function getDependenciesProvider(): array
        {
            return [
                [ MyClassA::class ],
                [ MyClassB::class ],
                [ MyClassC::class ],
                [ MyClassA_DI::class ],
                [ MyClassB_DI::class ]
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
                [ MyClassA_DI::class ]
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
         *
         * @dataProvider getDependenciesForAliasProvider
         * @test
         */
        public function get_ifAliasIsSet_returnsDependency(string $className)
        {
            $this->di->delegate(MyClassA::class)->to(MyClassH::class);

            $object = $this->di->get($className);

            $this->assertEquals($className, get_class($object));
            $this->assertInstanceOf(MyClassH::class, $object->myClassA);
        }

        public function tearDown()
        {
            unset($this->di);

            parent::tearDown();
        }
    }
}
