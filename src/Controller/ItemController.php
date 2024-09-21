<?php

namespace App\Controller;

use App\DTO\ItemData;
use App\Service\ItemCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ItemController extends AbstractController
{
    public function __construct(
        private ItemCollection $itemCollection,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @Route("/api/items", name="add_item", methods={"POST"})
     */
    public function addItem(Request $request): JsonResponse
    {
        $itemData = $this->standardItem($request);
        $errors = $this->validator->validate($itemData);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->createErrorResponse($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $item = $this->itemCollection->add($itemData);

        return $this->createJsonResponse($item, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/items", name="list_items", methods={"GET"})
     */
    public function listItems(Request $request): JsonResponse
    {
        try {
            $type = $request->query->get('type', null);
            $search = $request->query->get('search', '');
            $orderBy = $request->query->get('orderBy');

            $items = $this->itemCollection->list($type, $search, $orderBy);

            return $this->createJsonResponse($items, Response::HTTP_OK);
        } catch (NotFoundHttpException $e) {
            return $this->createErrorResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route("/api/items/{id}", name="update_item", methods={"PUT"})
     */
    public function updateItem(Request $request, int $id): JsonResponse
    {
        $itemData = $this->standardItem($request);
        $errors = $this->validator->validate($itemData);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->createErrorResponse($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $item = $this->itemCollection->update($id, $itemData);

        return $this->createJsonResponse($item, Response::HTTP_OK);
    }

    /**
     * @Route("/api/items/{id}", name="remove_item", methods={"DELETE"})
     */
    public function removeItem(int $id): JsonResponse
    {
        try {
            // Validate ID
            $this->itemCollection->toValidateId($id);
            $this->itemCollection->remove($id);

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->createErrorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Helper method to create a consistent JSON response.
     */
    private function createJsonResponse($data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $jsonData = $this->serializer->serialize($data, 'json');

        return new JsonResponse($jsonData, $statusCode, [], true);
    }

    /**
     * Helper method to create standardized error responses.
     */
    private function createErrorResponse(string $message, int $statusCode): JsonResponse
    {
        return new JsonResponse(['error' => $message], $statusCode);
    }

    /**
     * Helper method to create standardized ItemData validation rules.
     */
    private function standardItem(Request $request): ItemData
    {
        $data = json_decode($request->getContent(), true);
        $itemData = new ItemData();
        $itemData->name = $data['name'];
        $itemData->type = $data['type'];
        $itemData->quantity = $data['quantity'];
        $itemData->unit = $data['unit'];

        return $itemData;
    }
}
