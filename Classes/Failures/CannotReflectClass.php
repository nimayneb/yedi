<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Failures {

    use Exception;

    /**
     *
     */
    class CannotReflectClass extends Exception
    {
        /**
         * @param string $fullyClassName
         */
        public function __construct(string $fullyClassName)
        {
            parent::__construct(
                sprintf('Cannot reflect class <%s>', $fullyClassName)
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