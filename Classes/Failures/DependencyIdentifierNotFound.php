<?php declare(strict_types=1);

namespace JayBeeR\YEDI\Failures {

    use Exception;
    use Psr\Container\NotFoundExceptionInterface;

    class DependencyIdentifierNotFound extends Exception implements NotFoundExceptionInterface
    {
        /**
         * @param mixed $identifier
         */
        public function __construct($identifier)
        {
            $type = gettype($identifier);

            parent::__construct(
                sprintf(
                    'Dependency identifier <%s> not found.',
                    ('object' === $type) ? get_class($identifier) : $type
                )
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