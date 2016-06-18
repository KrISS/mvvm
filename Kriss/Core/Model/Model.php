<?php

namespace Kriss\Core\Model;

use Kriss\Mvvm\Model\ModelInterface;

class Model implements ModelInterface {
    const PHPPREFIX = '<?php /* ';
    const PHPSUFFIX = ' */ ?>';
      
    protected $resultClass;
    protected $slug;
    protected $file;
    
    protected $data = null;

    public function __construct($slug = "data", $resultClass = null, $prefix = 'data') {
        $this->resultClass = $resultClass;
        $this->slug = $slug;

        // Get main directory
        $trace = debug_backtrace();
        $trace = $trace[count($trace) - 1];
        $trace = dirname($trace['file']) . DIRECTORY_SEPARATOR . rtrim($prefix, '/');
        $this->file = $trace . DIRECTORY_SEPARATOR . $slug . '.php'; 
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
      
    public function getData()
    {
        return $this->data;
    }
    
    public function findOneBy($criteria)
    {
        if (is_null($criteria)) return $this->data;
        else return null;
    }
    
    public function findBy($criteria = null, $orderBy = null, $limit = null, $offset = null)
    {
        if (is_null($criteria)) return $this->data;
        else return null;
    }
    
    public function getResultClass()
    {
        return $this->resultClass;
    }
    
    public function getSlug()
    {
        return $this->slug;
    }
    
    public function persist($data) {
        if (is_null($this->resultClass) || $data instanceOf $this->resultClass) {
            $this->data = $data;
        }
    }
    
    public function remove($data) {
        $this->data = null;
    }
    
    public function flush() {
        file_put_contents(
            $this->file,
            self::PHPPREFIX
            . base64_encode(gzdeflate(serialize($this->data)))
            . self::PHPSUFFIX,
            LOCK_EX
        );
    }
}
