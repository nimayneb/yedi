<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\Tests\Unit\Container {

    use JayBeeR\Tests\Unit\Fixtures\MyClassA;
    use JayBeeR\Tests\Unit\Fixtures\MyAbstractA;
    use JayBeeR\Tests\Unit\Fixtures\MyInterfaceA;
    use JayBeeR\Tests\Unit\Fixtures\MyTraitA;
    use JayBeeR\YEDI\Container\DependencyAliasContainer;
    use JayBeeR\YEDI\Defaults;
    use JayBeeR\YEDI\Failures\CannotFindClassName;
    use JayBeeR\YEDI\Failures\CannotInstantiateClass;
    use PHPUnit\Framework\TestCase;

    class DependencyAliasContainerTest extends TestCase
    {
        protected DependencyAliasContainer $da;

        protected function setUp(): void
        {
            $this->da = new DependencyAliasContainer;
        }

        /**
         * @test
         *
         * @throws CannotFindClassName
         */
        public function delegate_withUnknownClass_throwsException()
        {
            $this->expectException(CannotFindClassName::class);
            $this->da->delegate('UnknownClass');
        }

        /**
         * @test
         *
         * @throws CannotFindClassName
         */
        public function to_withUnknownClass_throwsException()
        {
            $this->expectException(CannotFindClassName::class);
            $this->da->delegate(MyClassA::class)->to('UnknownClass');
        }

        /**
         * @test
         *
         * @throws CannotFindClassName
         */
        public function to_withInterfaceClass_throwsException()
        {
            $this->expectException(CannotFindClassName::class);
            $this->da->delegate(MyClassA::class)->to(MyInterfaceA::class);
        }

        /**
         * @test
         *
         * @throws CannotFindClassName
         */
        public function to_withTraitClass_throwsException()
        {
            $this->expectException(CannotFindClassName::class);
            $this->da->delegate(MyClassA::class)->to(MyTraitA::class);
        }

        /**
         * @test
         *
         * @throws CannotFindClassName
         */
        public function to_withAbstractClass_throwsException()
        {
            $this->expectException(CannotInstantiateClass::class);
            $this->da->delegate(MyClassA::class)->to(MyAbstractA::class);
        }

        // public function __construct($variable);
        // public function __construct(string $variable);
        // public function __construct(Class $variable);
        // public function __construct($variable = 'abc');
        // public function __construct(int $variable = 123);

        protected function tearDown(): void
        {
            unset($this->da);
            Defaults::$classNamesAreCaseSensitive = false;
        }
    }
}
