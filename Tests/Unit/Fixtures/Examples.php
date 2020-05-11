<?php declare(strict_types=1);

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

        public function injectDependencies()
        {
            $this->myClassA = $this->get(MyClassA::class);
        }
    }

    class MyClassB_YEDI
    {
        use DependencyInjectorConstructor;

        public MyClassA_YEDI $myClassA_YEDI;

        public function injectDependencies()
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
}