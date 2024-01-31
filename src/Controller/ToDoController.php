<?php

namespace App\Controller;

use App\Entity\ToDo;
use App\Repository\ToDoRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api", "api_")]
class ToDoController extends AbstractController
{
    #[Route('/todo', name: 'to_do', methods: ["GET"])]
    public function index(ToDoRepository $toDoRepository): JsonResponse
    {
        $todos = $toDoRepository->findAll();
        // dd($todos);
        return $this->json($todos);
    }

    #[Route('/todo/{id}', 'get_todo', methods: ["GET"])]
    public function getTodo(ToDo $toDo): JsonResponse
    {
        return $this->json($toDo);
    }

    #[Route('/todo', name: 'create_todo', methods: ["POST"])]
    public function createTodo(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);
        $todo = new ToDo();
        $todo->setName($requestBody["name"]);
        //
        $todo->setDescription($requestBody["description"]);
        $todo->setPrice($requestBody["price"]);
        $todo->setFreeCancelation($requestBody["freeCancelation"]);

        $entityManager->persist($todo);
        $entityManager->flush();

        return $this->json($todo, status: Response::HTTP_CREATED);
    }
}
