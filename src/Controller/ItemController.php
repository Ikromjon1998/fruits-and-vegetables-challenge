<?php

namespace App\Controller;

use App\Helpers\ItemHelpers;
use App\Service\ItemCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ItemController extends AbstractController
{
    public function __construct(
        private ItemCollection $itemCollection,
        private ValidatorInterface $validator,
        private ItemHelpers $itemHelpers,
    ) {
    }

    /**
     * @Route("/api/items", name="add_item", methods={"POST"})
     */
    public function addItem(Request $request): JsonResponse
    {
        $itemData = $this->itemHelpers->createItemDTOFromRequest($request);
        $errors = $this->validator->validate($itemData);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->itemHelpers->createErrorResponse($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $item = $this->itemCollection->add($itemData);

        return $this->itemHelpers->createJsonResponse($item, Response::HTTP_CREATED);
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

            return $this->itemHelpers->createJsonResponse($items, Response::HTTP_OK);
        } catch (NotFoundHttpException $e) {
            return $this->itemHelpers->createErrorResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route("/api/items/{id}", name="update_item", methods={"PUT"})
     */
    public function updateItem(Request $request, int $id): JsonResponse
    {
        $itemData = $this->itemHelpers->createItemDTOFromRequest($request);
        $errors = $this->validator->validate($itemData);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->itemHelpers->createErrorResponse($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $item = $this->itemCollection->update($id, $itemData);

        return $this->itemHelpers->createJsonResponse($item, Response::HTTP_OK);
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
            return $this->itemHelpers->createErrorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
