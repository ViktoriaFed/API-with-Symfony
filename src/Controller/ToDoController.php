<?php

namespace App\Controller;

use App\Entity\ToDo;
use App\Optionsresolver\ToDoOptionsResolver;
use App\Repository\ToDoRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use PhpParser\Node\Stmt\Catch_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function createTodo(ToDoRepository $toDoRepository, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validatorInterface, ToDoOptionsResolver $toDoOptionsResolver): JsonResponse
    {
        // try {
        $requestBody = json_decode($request->getContent(), true);
        // $fields = $toDoOptionsResolver->configureName(true)->resolve($requestBody);
        $todo = new ToDo();
        $todo->setName($requestBody["name"]);
        //
        $todo->setDescription($requestBody["description"]);
        $todo->setPrice($requestBody["price"]);
        $todo->setFreeCancelation($requestBody["freeCancelation"]);

        // To validate the entity

        $errors = $validatorInterface->validate($todo);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($todo);
        $entityManager->flush();

        return $this->json($todo, status: Response::HTTP_CREATED);
        // } catch (Exception $e) {
        //     throw new BadRequestHttpException($e->getMessage());
        // }
    }

    #[Route("/todo/{id}", "delete_todo", methods: ["DELETE"])]
    public function deleteTodo(ToDo $toDo, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($toDo);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route("/todo/{id}", "update_todo", methods: ["PATCH", "PUT"])]
    public function updateTodo(ToDo $toDo, ToDoOptionsResolver $toDoOptionsResolver, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        try {
            $isPatchMethod = $request->getMethod() === "PUT";
            $requestBody = json_decode($request->getContent(), true);

            $fields = $toDoOptionsResolver
                ->configureName($isPatchMethod)
                ->resolve($requestBody);

            // Updating only the fields that are provided in the request
            foreach ($fields as $field => $value) {
                switch ($field) {
                    case "name":
                        $toDo->setName($value);
                        break;
                        // case "FreeCancelation":
                        //     $toDo->setFreeCancelation($value);
                        //     break; //smth doesnt work here for now
                        // maybe will add more fields later
                }
            }

            $errors = $validator->validate($toDo);
            if (count($errors) > 0) {
                throw new InvalidArgumentException((string) $errors);
            }
            $entityManager->persist($toDo);
            $entityManager->flush();

            return $this->json($toDo);
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
