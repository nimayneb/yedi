<?php declare(strict_types=1);

namespace JayBeeR\YEDI {

    interface ArgumentResolution
    {
        /**
         * @param $className
         *
         * @return Arguments
         */
        public function asInjection(string $className): Arguments;

        /**
         * @param mixed $value
         *
         * @return Arguments
         */
        public function asValue($value): Arguments;
    }
}