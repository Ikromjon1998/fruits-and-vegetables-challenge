<?php

namespace App\Controller;

use App\Service\FruitCollectionManager;
use App\Service\VegetableCollectionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/vegetables')]
class VegetableController extends AbstractController implements CollectionControllerInterface
{
    public function __construct(private VegetableCollectionManager $vegetableCollectionManager)
    {
    }

    #[Route('/', name: 'list_vegetables', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $filters = $request->query->all('filter');
        $sorts = $request->query->all('sort');

        $items = $this->vegetableCollectionManager->list($filters, $sorts);

        return $this->json($items);
    }

    public function store(): array
    {
        // TODO: Implement store() method.
        return [];
    }

    public function destroy(int $id): array
    {
        // TODO: Implement destroy() method.
        return [];
    }
}
