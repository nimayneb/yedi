<?php declare(strict_types=1);


namespace JayBeeR\YEDI {

    interface AliasTo
    {
        /**
         * @param string $fullyClassName
         */
        public function to(string $fullyClassName): void;
    }
}