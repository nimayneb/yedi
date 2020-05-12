<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.yawl".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\YEDI\Failures {

    use Exception;
    use Psr\Container\NotFoundExceptionInterface;

    /**
     *
     */
    class DependencyIdentifierNotFound extends Exception implements NotFoundExceptionInterface
    {
        /**
         * @param mixed $identifier
         */
        public function __construct($identifier)
        {
            parent::__construct(
                sprintf(
                    'Dependency identifier <%s> not found.',
                    $this->getNamedIdentifier($identifier)
                )
            );
        }

        /**
         * @param mixed $identifier
         *
         * @return string
         */
        protected function getNamedIdentifier($identifier): string
        {
            $name = gettype($identifier);

            switch ($name) {
                case 'object':
                {
                    $name = get_class($name);

                    break;
                }

                case 'string':
                {
                    $name = $identifier;

                    break;
                }

                default;
            }

            return $name;
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