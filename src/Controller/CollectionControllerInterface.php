<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface CollectionControllerInterface
{
    public function index(Request $request): Response;

    /**
     * @return array<string, mixed>
     */
    public function store(): array;

    /**
     * @return array<string, mixed>
     */
    public function destroy(int $id): array;
}
