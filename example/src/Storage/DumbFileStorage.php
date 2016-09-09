<?php

namespace TodoExample\Storage;

class DumbFileStorage implements Storage
{
    /**
     * @var string
     */
    private $dir;

    public function __construct($dir)
    {
        $this->dir = $dir;

        if ( ! file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    public function store($object)
    {
        $serialized = serialize($object);
        do {
            $id = uniqid(md5($serialized), true);
        } while (file_exists("{$this->dir}/{$id}"));

        file_put_contents("{$this->dir}/{$id}", $serialized);

        return $id;
    }

    public function all()
    {
        $objects = [];

        foreach (new \DirectoryIterator($this->dir) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $objects[$fileInfo->getBasename()] = $this->retrieve($fileInfo->getBasename());
        }

        return $objects;
    }

    public function retrieve($id)
    {
        if ( ! is_file("{$this->dir}/{$id}")) {
            throw new \RuntimeException("Object does not exist");
        }

        $file = file_get_contents("{$this->dir}/{$id}");

        return unserialize($file);
    }
}
