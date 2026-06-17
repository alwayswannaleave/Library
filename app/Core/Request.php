<?php

namespace App\Core;

class Request
{
    private array $server;
    private array $get;
    private array $post;
    private array $files;
    private string $body;

    public function __construct(array $server, array $get, array $post, array $files, string $body)
    {
        $this->server = $server;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->body = $body;
    }

    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function getUri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $uri = strtok($uri, '?');
        return rtrim($uri, '/') ?: '/';
    }

    public function getBody(): array
    {
        return json_decode($this->body, true) ?? [];
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post, $this->getBody());
    }

    public function get(string $key, $default = null)
    {
        return $this->all()[$key] ?? $default;
    }
}
