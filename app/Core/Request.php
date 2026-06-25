<?php

namespace App\Core;

class Request
{
    private array $server;
    private array $get;
    private array $post;
    private array $files;
    private string $body;
    private array $params;

    public function __construct(array $server, array $get, array $post, array $files, string $body, array $params = [])
    {
        $this->server = $server;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->body = $body;
        $this->params = $params;
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

    public function getHeader(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$key] ?? null;
    }

    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
