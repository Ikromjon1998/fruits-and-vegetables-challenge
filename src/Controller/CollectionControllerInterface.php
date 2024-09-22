<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface CollectionControllerInterface
{
    public function index(Request $request): Response;

    /**
     * @return array<string, mixed>
     */
    public function store(Request $request): Response;

    public function destroy(int $id): JsonResponse;
}
