<?php
namespace FAC;
use FAC;
/**
 * Media handling class.
 */

class Media{

    static public function getUpload($filename, $type='image'){

        return Config::getInstance()->get('base_url')."/uploads/{$filename}";

    }
}
