<?php
//
//  FPDM - Filter Flate
//  NOTE: requires ZLIB >= 1.0.9!
//

$__tmp = version_compare(phpversion(), "5") == -1 ? array('FilterFlateDecode') : array('FilterFlateDecode', false);
if (!call_user_func_array('class_exists', $__tmp)) {


	if(isset($FPDM_FILTERS)) array_push($FPDM_FILTERS,"FlateDecode");

    class FilterFlate {
        
        var $data = null;
        var $dataLength = 0;
    
        function error($msg) {
            die($msg);
        }
        
        /**
         * Method to decode GZIP compressed data.
         *
         * @param string data    The compressed data.
         * @return uncompressed data
         */
        function decode($data) {
    
            $this->data = $data;
            $this->dataLength = strlen($data);
    
            // Try common zlib/flate variants; silence PHP warning noise from invalid payloads.
            $decoded = @gzuncompress($data);

            if ($decoded === false) {
                $decoded = @gzinflate($data);
            }

            // Some streams include zlib header bytes.
            if ($decoded === false && $this->dataLength > 2) {
                $decoded = @gzinflate(substr($data, 2));
            }

            // Some producers store gzip-wrapped payloads.
            if ($decoded === false && function_exists('gzdecode')) {
                $decoded = @gzdecode($data);
            }

            if ($decoded === false) {
                // Fallback: keep raw stream content to avoid hard failure on non-standard PDFs.
                return $data;
            }
             
            return $decoded;
        }
        
        
        function encode($in) {
            return gzcompress($in, 9);
        }
   }

}
//unset $__tmp;
?>
