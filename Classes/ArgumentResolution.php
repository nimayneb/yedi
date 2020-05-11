<?php declare(strict_types=1);

namespace JayBeeR\YEDI {

    interface ArgumentResolution
    {
        /**
         * @param $fullyClassName
         *
         * @return Arguments
         */
        public function asInjection(string $fullyClassName): Arguments;

        /**
         * @param mixed $value
         *
         * @return Arguments
         */
        public function to($value): Arguments;

        /**
         * @param string|null $fullyClassName
         *
         * @return Arguments
         */
        public function asSingleton(?string $fullyClassName): Arguments;
    }
}