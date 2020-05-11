<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Failures {

    use Exception;

    class CannotFindClassName extends Exception
    {
        /**
         * @param string $className
         */
        public function __construct(string $className)
        {
            parent::__construct(
                sprintf('Cannot find class name <%s>', $className)
            );
        }

        /**
         *
         */
        public function describe(): void
        {
            // TODO: Implement describe() method.
        }
    }
} 