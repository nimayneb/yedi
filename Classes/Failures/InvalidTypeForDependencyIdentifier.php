<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Failures {

    use Exception;
    use Psr\Container\ContainerExceptionInterface;

    /**
     *
     */
    class InvalidTypeForDependencyIdentifier extends Exception implements ContainerExceptionInterface
    {
        /**
         * @param mixed $identifier
         */
        public function __construct($identifier)
        {
            $type = gettype($identifier);

            parent::__construct(
                sprintf(
                    'Invalid type <%s> for dependency identifier.',
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