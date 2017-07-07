<?php

namespace Kriss\Core\Model;

use Kriss\Mvvm\Model\ModelInterface;

class ArrayModel implements ModelInterface {
    use ArrayModelTrait;
    
    const PHPPREFIX = '<'.'?php /* ';
    const PHPSUFFIX = ' */ ?'.'>';
      
    protected $resultClass;
    protected $slug;
    protected $file;
    protected $data = [];

    public function __construct($slug = "data", $resultClass = null, $prefix = 'data') {
        $this->resultClass = $resultClass;
        $this->slug = $slug;

        $this->file = getcwd()
                    . DIRECTORY_SEPARATOR . trim($prefix, DIRECTORY_SEPARATOR)
                    . DIRECTORY_SEPARATOR . $slug . '.php';
        if (file_exists($this->file)) {
            $this->data = unserialize(
                gzinflate(
                    base64_decode(
                        substr(
                            file_get_contents($this->file),
                            strlen(self::PHPPREFIX),
                            -strlen(self::PHPSUFFIX)
                        )
                    )
                )
            );
        }
    }
    
    public function flush() {
        if (!empty($this->data)) {
            if (!file_exists($this->file)) $this->createDir();
            file_put_contents(
                $this->file,
                self::PHPPREFIX
                . base64_encode(gzdeflate(serialize($this->data)))
                . self::PHPSUFFIX,
                LOCK_EX
            );
        } else if (file_exists($this->file)) {
            unlink($this->file);
        }
    }
}
