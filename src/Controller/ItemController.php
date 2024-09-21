<?php

namespace App\Controller;

use App\Entity\Item;
use App\Service\ItemCollection;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ItemController
 * @package App\Controller
 * @param Request $request
 * @param SerializerInterface $serializer
 * @param int $id
 * @param string $type
 *
 * @return JsonResponse
 */
class ItemController extends AbstractController
{
    public function __construct(private ItemCollection $itemCollection, private SerializerInterface $serializer)
    {
    }

    /**
     * @Route("/api/items", name="add_item", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function addItem(Request $request): JsonResponse
    {
        // validate the request
        $this->validateCreateAndUpdateRequest($request);

        $data = json_decode($request->getContent(), true);
        $item = $this->itemCollection->add($data);

        $jsonItem = $this->serializer->serialize($item, 'json');

        return new JsonResponse($jsonItem, Response::HTTP_CREATED, [], true);
    }

    /**
     * @Route("/api/items/{type}", name="list_items", methods={"GET"})
     *
     * @param Request $request
     * @param string $type
     * @return JsonResponse
     */
    public function listItems(Request $request, string $type): JsonResponse
    {
        // Get the sort parameter from the request
        $orderBy = $request->query->get('orderBy');

        $items = $this->itemCollection->list($type, $orderBy);

        $json = $this->serializer->serialize($items, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @Route("/api/items/{id}", name="update_item", methods={"PUT"})
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateItem(Request $request, int $id): JsonResponse
    {
        // validate the request
        $this->itemCollection->toValidateId($id);
        $this->validateCreateAndUpdateRequest($request);

        $data = json_decode($request->getContent(), true);
        $item = $this->itemCollection->update($id, $data);

        $jsonItem = $this->serializer->serialize($item, 'json');

        return new JsonResponse($jsonItem, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/items/{id}", name="remove_item", methods={"DELETE"})
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function removeItem(int $id): JsonResponse
    {
        // validate the request
        $this->itemCollection->toValidateId($id);
        $this->itemCollection->remove($id);
        return new JsonResponse('Item removed', Response::HTTP_NO_CONTENT, [], false);
    }

    /**
     * @Route("/api/items/search/{name}", name="search_items", methods={"GET"})
     *
     * @param string $name
     * @return JsonResponse
     */
    public function searchItems(string $name): JsonResponse
    {
        $items = $this->itemCollection->search($name);

        $json = $this->serializer->serialize($items, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    private function validateCreateAndUpdateRequest(Request $request): void
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['type']) || !isset($data['quantity']) || !isset($data['unit'])) {
            throw new \Exception('Invalid request data');
        }
    }
}