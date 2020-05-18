<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\Tests\Unit\Fixtures {

    use JayBeeR\YEDI\DependencyInjectorConstructor;

    class MyClassA
    {

    }

    class MyClassB
    {
        public MyClassA $myClassA;

        public function __construct(MyClassA $myClassA)
        {
            $this->myClassA = $myClassA;
        }
    }

    class MyClassC
    {
        public MyClassB $myClassB;

        public function __construct(MyClassB $myClassB)
        {
            $this->myClassB = $myClassB;
        }
    }

    class MyClassD
    {
        public MyClassB $myClassB;

        public MyClassC $myClassC;

        public function __construct(MyClassB $myClassB, MyClassC $myClassC)
        {
            $this->myClassB = $myClassB;
            $this->myClassC = $myClassC;
        }
    }

    class MyClassA_YEDI
    {
        use DependencyInjectorConstructor;

        public MyClassA $myClassA;

        protected function injectDependencies()
        {
            $this->myClassA = $this->get(MyClassA::class);
        }
    }

    class MyClassB_YEDI
    {
        use DependencyInjectorConstructor;

        public MyClassA_YEDI $myClassA_YEDI;

        protected function injectDependencies()
        {
            $this->myClassA_YEDI = $this->get(MyClassA_YEDI::class);
        }
    }

    interface MyInterfaceA
    {

    }

    class MyInterfacedClassA implements MyInterfaceA
    {

    }

    class MyInterfacedClassB implements MyInterfaceA
    {

    }

    class MyClassE
    {
        public MyInterfaceA $myInterfaceA;

        public function __construct(MyInterfaceA $myInterfaceA)
        {
            $this->myInterfaceA = $myInterfaceA;
        }
    }

    class MyClassF
    {
        public MyInterfaceA $myInterfaceA;

        public function __construct(MyInterfaceA $myInterfaceA)
        {
            $this->myInterfaceA = $myInterfaceA;
        }
    }

    class MyClassG
    {
        public MyInterfaceA $myInterfaceA;

        public MyInterfaceA $myInterfaceB;

        public function __construct(MyInterfaceA $myInterfaceA, MyInterfaceA $myInterfaceB)
        {
            $this->myInterfaceA = $myInterfaceA;
            $this->myInterfaceB = $myInterfaceB;
        }
    }

    class MyClassH extends MyClassA
    {

    }

    class MyClassWithMissingType
    {
        public function __construct($property)
        {

        }
    }

    class MyClassWithIntegerType
    {
        public function __construct(int $property)
        {

        }
    }

    class MyClassWithFloatType
    {
        public function __construct(float $property)
        {

        }
    }

    class MyClassWithStringType
    {
        public function __construct(string $property)
        {

        }
    }

    class MyClassWithObjectType
    {
        public function __construct(object $property)
        {

        }
    }

    class MyClassWithBooleanType
    {
        public function __construct(bool $property)
        {

        }
    }

    class MyClassWithArrayType
    {
        public function __construct(array $property)
        {

        }
    }

    class MyClassWithDefaultType
    {
        public function __construct(int $property = 123)
        {

        }
    }

    class MyClassWithMissingTypeOfDependency
    {
        public function __construct(MyClassWithMissingType $myClassI)
        {

        }
    }

    abstract class MyAbstractA {

    }

    trait MyTraitA {

    }
}