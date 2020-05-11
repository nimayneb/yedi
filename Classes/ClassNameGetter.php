<?php declare(strict_types=1);

namespace JayBeeR\YEDI {

    interface ClassNameGetter
    {
        /**
         * @param string $derivedClassName
         *
         * @return string
         */
        public function getClassName(string $derivedClassName): ?string;
    }
}