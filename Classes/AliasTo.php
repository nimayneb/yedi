<?php declare(strict_types=1);


namespace JayBeeR\YEDI {

    interface AliasTo
    {
        /**
         * @param string $className
         */
        public function to(string $className): void;
    }
}