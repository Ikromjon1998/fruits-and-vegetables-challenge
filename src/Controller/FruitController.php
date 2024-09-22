<?php

namespace App\Controller;

use App\Helpers\ItemHelpers;
use App\Service\FruitCollectionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/fruits')]
class FruitController extends AbstractController implements CollectionControllerInterface
{
    public function __construct(
        private FruitCollectionManager $fruitCollectionManager,
        private ValidatorInterface $validator,
        private ItemHelpers $itemHelpers,
    ) {
    }

    #[Route('/', name: 'list_fruits', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $filters = $request->query->all('filter');
        $sorts = $request->query->all('sort');

        $items = $this->fruitCollectionManager->list($filters, $sorts);

        return $this->json($items);
    }

    #[Route('/', name: 'add_fruit', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $itemData = $this->itemHelpers->createFruitDTOFromRequest($request);
        $errors = $this->validator->validate($itemData);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->itemHelpers->createErrorResponse($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $fruit = $this->fruitCollectionManager->add($itemData);

        return $this->itemHelpers->createJsonResponse($fruit, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'delete_fruit', methods: ['DELETE'])]
    public function destroy(int $id): JsonResponse
    {
        try {
            // Validate ID
            $this->fruitCollectionManager->validateId($id);
            $this->fruitCollectionManager->remove($id);

            return $this->itemHelpers->createJsonResponse(['Fruit removed from warehouse'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->itemHelpers->createErrorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
