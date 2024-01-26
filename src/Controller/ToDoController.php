<?php

namespace App\Controller;

use App\Repository\ToDoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}
