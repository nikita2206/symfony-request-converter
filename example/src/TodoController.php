<?php

namespace TodoExample;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TodoExample\Request\Task;
use TodoExample\Storage\Storage;

class TodoController
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var UrlGeneratorInterface
     */
    private $urls;

    public function __construct(Storage $storage, UrlGeneratorInterface $urls)
    {
        $this->storage = $storage;
        $this->urls = $urls;
    }

    public function add(Task $task)
    {
        $id = $this->storage->store($task);

        $url = $this->urls->generate("todo_retrieve", ["id" => $id]);

        return new RedirectResponse($url);
    }

    public function all()
    {
        $objects = $this->storage->all();

        $json = json_encode($objects);

        return new Response($json, 200, ["Content-Type" => "application/json"]);
    }

    public function retrieve($id)
    {
        $object = $this->storage->retrieve($id);

        $json = json_encode($object);

        return new Response($json, 200, ["Content-Type" => "application/json"]);
    }
}
