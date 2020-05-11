<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Failures {

    use Exception;

    class CannotFindClassName extends Exception
    {
        /**
         * @param string $fullyClassName
         */
        public function __construct(string $fullyClassName)
        {
            parent::__construct(
                sprintf('Cannot find class name <%s>', $fullyClassName)
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