<?php

namespace App\Controller;

use App\Helpers\ItemHelpers;
use App\Service\VegetableCollectionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/vegetables')]
class VegetableController extends AbstractController implements CollectionControllerInterface
{
    public function __construct(
        private VegetableCollectionManager $vegetableCollectionManager,
        private ItemHelpers $itemHelpers,
        private ValidatorInterface $validator,
    ) {
    }

    #[Route('/', name: 'list_vegetables', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $filters = $request->query->all('filter');
        $sorts = $request->query->all('sort');

        $items = $this->vegetableCollectionManager->list($filters, $sorts);

        return $this->json($items);
    }

    #[Route('/', name: 'add_vegetable', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $itemData = $this->itemHelpers->createFruitDTOFromRequest($request);
        $errors = $this->validator->validate($itemData);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->itemHelpers->createErrorResponse($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $vegetable = $this->vegetableCollectionManager->add($itemData);

        return $this->itemHelpers->createJsonResponse($vegetable, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'delete_vegetable', methods: ['DELETE'])]
    public function destroy(int $id): JsonResponse
    {
        try {
            // Validate ID
            $this->vegetableCollectionManager->validateId($id);
            $this->vegetableCollectionManager->remove($id);

            return $this->itemHelpers->createJsonResponse(['Fruit removed from warehouse'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->itemHelpers->createErrorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
