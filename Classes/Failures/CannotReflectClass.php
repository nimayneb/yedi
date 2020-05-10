<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Failures {

    use Exception;

    /**
     *
     */
    class CannotReflectClass extends Exception
    {
        /**
         * @param string $className
         */
        public function __construct(string $className)
        {
            parent::__construct(
                sprintf('Cannot reflect class <%s>', $className)
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