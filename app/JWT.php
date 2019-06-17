<?php
    
    namespace App;
    
    
    class JWT extends \Firebase\JWT\JWT
    {
        /***
         * @param string $input
         *
         * @return string
         */
        public static function urlsafeB64Decode($input)
        {
            return decrypt($input);
        }
    
        /***
         * @param string $input
         *
         * @return string
         */
        public static function urlsafeB64Encode($input)
        {
            return encrypt($input);
        }
    }
