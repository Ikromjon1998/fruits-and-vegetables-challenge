<?php

namespace App\Controller;

use App\Service\ItemCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ItemController extends AbstractController
{
    public function __construct(private ItemCollection $itemCollection)
    {
    }

    /**
     * @Route("/api/items", name="add_item", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addItem(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->itemCollection->add($data);

        return new JsonResponse(['status' => 'Item added successfully'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/items/{type}", name="list_items", methods={"GET"})
     *
     * @param string $type
     * @return JsonResponse
     */
    public function listItems(string $type): JsonResponse
    {
        $items = $this->itemCollection->list($type);

        dump($items);
        return new JsonResponse($items, Response::HTTP_OK);
    }

    /**
     * @Route("/api/items/{id}", name="remove_item", methods={"DELETE"})
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function removeItem(int $id): JsonResponse
    {
        $this->itemCollection->remove($id);
        return new JsonResponse(['status' => 'Item removed'], Response::HTTP_NO_CONTENT);
    }
}