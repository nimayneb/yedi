<?php declare(strict_types=1);

namespace JayBeeR\Tests\Unit\Fixtures {

    use JayBeeR\YEDI\DependencyInjectorGetter;

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

    class MyClassA_DI
    {
        use DependencyInjectorGetter;

        public MyClassA $myClassA;

        public function __construct()
        {
            $this->myClassA = $this->get(MyClassA::class);
        }
    }

    class MyClassB_DI
    {
        use DependencyInjectorGetter;

        public MyClassA_DI $myClassA_DI;

        public function __construct()
        {
            $this->myClassA_DI = $this->get(MyClassA_DI::class);
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