<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\Tests\Unit\Container {

    use JayBeeR\Tests\Unit\Fixtures\MyClassB;
    use JayBeeR\YEDI\Container\DependencyResolutionContainer;
    use JayBeeR\YEDI\Defaults;
    use JayBeeR\YEDI\Failures\CannotFindClassName;
    use PHPUnit\Framework\TestCase;

    class DependencyResolutionContainerTest extends TestCase
    {
        protected DependencyResolutionContainer $dr;

        protected function setUp(): void
        {
            $this->dr = new DependencyResolutionContainer;
        }

        /**
         * @test
         *
         * @throws CannotFindClassName
         */
        public function for_withUnknownClass_throwsException()
        {
            $this->expectException(CannotFindClassName::class);
            $this->dr->for('UnknownClass');
        }

        /**
         * @throws CannotFindClassName
         */
        public function setArgument_withUnknownClass_throwsException()
        {
            $this->expectException(CannotFindClassName::class);
            $this->dr->for(MyClassB::class);
        }

        protected function tearDown(): void
        {
            unset($this->dr);
            Defaults::$classNamesAreCaseSensitive = false;
        }
    }
}
