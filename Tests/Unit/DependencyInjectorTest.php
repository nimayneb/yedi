<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\Tests\Unit {

    use JayBeeR\Tests\Unit\Fixtures\MyClassA;
    use JayBeeR\Tests\Unit\Fixtures\MyClassA_YEDI;
    use JayBeeR\Tests\Unit\Fixtures\MyClassB;
    use JayBeeR\Tests\Unit\Fixtures\MyClassB_YEDI;
    use JayBeeR\Tests\Unit\Fixtures\MyClassC;
    use JayBeeR\Tests\Unit\Fixtures\MyClassE;
    use JayBeeR\Tests\Unit\Fixtures\MyClassF;
    use JayBeeR\Tests\Unit\Fixtures\MyClassG;
    use JayBeeR\Tests\Unit\Fixtures\MyClassH;
    use JayBeeR\Tests\Unit\Fixtures\MyClassWithArrayType;
    use JayBeeR\Tests\Unit\Fixtures\MyClassWithBooleanType;
    use JayBeeR\Tests\Unit\Fixtures\MyClassWithFloatType;
    use JayBeeR\Tests\Unit\Fixtures\MyClassWithIntegerType;
    use JayBeeR\Tests\Unit\Fixtures\MyClassWithMissingType;
    use JayBeeR\Tests\Unit\Fixtures\MyClassWithMissingTypeOfDependency;
    use JayBeeR\Tests\Unit\Fixtures\MyAbstractA;
    use JayBeeR\Tests\Unit\Fixtures\MyClassWithObjectType;
    use JayBeeR\Tests\Unit\Fixtures\MyClassWithStringType;
    use JayBeeR\Tests\Unit\Fixtures\MyInterfaceA;
    use JayBeeR\Tests\Unit\Fixtures\MyInterfacedClassA;
    use JayBeeR\Tests\Unit\Fixtures\MyInterfacedClassB;
    use JayBeeR\Tests\Unit\Fixtures\MyTraitA;
    use JayBeeR\YEDI\Defaults;
    use JayBeeR\YEDI\DependencyInjector;
    use JayBeeR\YEDI\Failures\CannotFindClassName;
    use JayBeeR\YEDI\Failures\CannotInstantiateClass;
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

        protected function setUp(): void
        {
            $this->di = new DependencyInjector();
        }

        public function getDependenciesProvider(): array
        {
            return [
                [MyClassA::class],
                [MyClassB::class],
                [MyClassC::class],
                [MyClassA_YEDI::class],
                [MyClassB_YEDI::class],
            ];
        }

        /**
         * @dataProvider getDependenciesProvider
         * @test
         *
         * @param string $className
         *
         * @throws CannotFindClassName
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws MissingTypeForArgument
         * @throws ReflectionException
         * @throws WrongArgumentsForDependencyResolution
         * @throws CannotInstantiateClass
         */
        public function get_returnsDependency(string $className): void
        {
            $object = $this->di->get($className);

            $this->assertInstanceOf($className, $object);
        }

        public function getDependenciesForAliasProvider(): array
        {
            return [
                [MyClassB::class],
                [MyClassA_YEDI::class],
            ];
        }

        /**
         * @dataProvider getDependenciesForAliasProvider
         * @test
         *
         * @param string $className
         *
         * @throws CannotFindClassName
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws MissingTypeForArgument
         * @throws ReflectionException
         * @throws WrongArgumentsForDependencyResolution
         * @throws CannotInstantiateClass
         */
        public function get_ifAliasIsSet_returnsDependency(string $className)
        {
            $this->di->delegate(MyClassA::class)->to(MyClassH::class);

            $object = $this->di->get($className);

            $this->assertInstanceOf($className, $object);
            $this->assertInstanceOf(MyClassH::class, $object->myClassA);
        }

        /**
         * @test
         * @throws CannotFindClassName
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws MissingTypeForArgument
         * @throws ReflectionException
         * @throws WrongArgumentsForDependencyResolution
         * @throws CannotInstantiateClass
         */
        public function get_ifTwoResolutionsIsSet_returnsDependency()
        {
            $this->di->for(MyClassE::class)
                ->setArgument('myInterfaceA')->asInjection(MyInterfacedClassA::class);

            $this->di->for(MyClassF::class)
                ->setArgument('myInterfaceA')->asInjection(MyInterfacedClassB::class);

            $object = $this->di->get(MyClassE::class);

            $this->assertInstanceOf(MyClassE::class, $object);
            $this->assertInstanceOf(MyInterfacedClassA::class, $object->myInterfaceA);

            $object = $this->di->get(MyClassF::class);

            $this->assertInstanceOf(MyClassF::class, $object);
            $this->assertInstanceOf(MyInterfacedClassB::class, $object->myInterfaceA);
        }

        /**
         * @test
         * @throws CannotFindClassName
         * @throws CannotReflectClass
         * @throws ClassNameIsIncorrectlyCapitalized
         * @throws DependencyIdentifierNotFound
         * @throws InvalidTypeForDependencyIdentifier
         * @throws InvalidTypeForDependencyInjection
         * @throws MissingTypeForArgument
         * @throws ReflectionException
         * @throws WrongArgumentsForDependencyResolution
         * @throws CannotInstantiateClass
         */
        public function get_ifResolutionIsSet_returnsDependency()
        {
            $this->di->for(MyClassG::class)
                ->setArgument('myInterfaceA')->asInjection(MyInterfacedClassA::class)
                ->setArgument('myInterfaceB')->asInjection(MyInterfacedClassB::class);

            $object = $this->di->get(MyClassG::class);

            $this->assertInstanceOf(MyClassG::class, $object);
            $this->assertInstanceOf(MyInterfacedClassA::class, $object->myInterfaceA);
            $this->assertInstanceOf(MyInterfacedClassB::class, $object->myInterfaceB);
        }

        /**
         * @return array
         */
        public function getClassesWithInvalidTypeProvider()
        {
            return [
                [MyClassWithMissingType::class, MissingTypeForArgument::class],
                [MyClassWithMissingTypeOfDependency::class, MissingTypeForArgument::class],

                [MyClassWithIntegerType::class, InvalidTypeForDependencyInjection::class],
                [MyClassWithFloatType::class, InvalidTypeForDependencyInjection::class],
                [MyClassWithStringType::class, InvalidTypeForDependencyInjection::class],
                [MyClassWithObjectType::class, InvalidTypeForDependencyInjection::class],
                [MyClassWithBooleanType::class, InvalidTypeForDependencyInjection::class],
                [MyClassWithArrayType::class, InvalidTypeForDependencyInjection::class],

                [MyAbstractA::class, CannotInstantiateClass::class],
                [MyInterfaceA::class, CannotInstantiateClass::class],
                [MyTraitA::class, CannotInstantiateClass::class],

                ['UnknownClass', CannotFindClassName::class],
            ];
        }

        /**
         * @dataProvider getClassesWithInvalidTypeProvider
         * @test
         *
         * @param string $className
         * @param string $exceptionClassName
         *
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
        public function get_withoutInvalidType_throwsException(string $className, string $exceptionClassName)
        {
            $this->expectException($exceptionClassName);
            $this->di->get($className);
        }

        /**
         * @return array
         */
        public function getClassesWithWrongCaseSensitivesProvider()
        {
            return [
                [strtolower(MyClassA::class), ClassNameIsIncorrectlyCapitalized::class],
                [strtoupper(MyClassA::class), ClassNameIsIncorrectlyCapitalized::class],
            ];
        }

        /**
         * @dataProvider getClassesWithWrongCaseSensitivesProvider
         * @test
         *
         * @param string $className
         * @param string $exceptionClassName
         *
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
        public function get_withWrongCaseSensitiveClassNames_throwsException(string $className, string $exceptionClassName)
        {
            $this->expectException($exceptionClassName);

            Defaults::$classNamesAreCaseSensitive = true;
            $this->di->get($className);
        }

        /**
         * @dataProvider getClassesWithWrongCaseSensitivesProvider
         * @test
         *
         * @param string $className
         *
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
        public function get_withWrongCaseSensitiveClassNames_ButValid_returnsDependency(string $className)
        {
            Defaults::$classNamesAreCaseSensitive = false;
            $object = $this->di->get($className);
            $this->assertInstanceOf($className, $object);
        }

        // public function __construct($variable);
        // public function __construct(string $variable);
        // public function __construct(Class $variable);
        // public function __construct($variable = 'abc');
        // public function __construct(int $variable = 123);

        protected function tearDown(): void
        {
            unset($this->di);
            Defaults::$classNamesAreCaseSensitive = false;
        }
    }
}
