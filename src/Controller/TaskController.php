<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * Affiche toutes les tâches
     * @Route("/", name="browse")
     */
    public function browse(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();
        return $this->render('task/browse.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Edite une tâche
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Task $task): Response
    {
        $taskForm = $this->createForm(TaskType::class, $task);

        $taskForm->handleRequest($request);

        if ($taskForm->isSubmitted() && $taskForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $task->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            // Ajout d'un flashMessage pour avertir l'utilisateur de l'édition de la tâche
            $this->addFlash('success', "La tâche `{$task->getTitle()}` a été modifiée.");

            // Redirection
            return $this->redirectToRoute('browse');
        }
        // Ajout du formulaire à la vue
        return $this->render('task/add.html.twig', [
            'task_form' => $taskForm->createView(),
            'task' => $task,
            'page' => 'edit'
        ]);
    }

    /**
     * Ajoute une tâche
     * @Route("/add", name="add", methods={"GET", "POST"})
     */
    public function add(Request $request): Response
    {
        $task = new Task();

        // Création d'un formulaire vide
        $taskForm = $this->createForm(TaskType::class, $task);

        $taskForm->handleRequest($request);

        if ($taskForm->isSubmitted() && $taskForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($task);
            $task->setIsComplete(false);
            $entityManager->flush();

            // Ajout d'un flashMessage pour avertir l'utilisateur de l'ajout de la tâche
            $this->addFlash('success', "La tâche `{$task->getTitle()}` a été crée.");

            // Redirection
            return $this->redirectToRoute('browse');
        }

        // Ajout du formulaire à la vue
        return $this->render('task/add.html.twig', [
            'task_form' => $taskForm->createView(),
            'page' => 'ajouter'
        ]);
    }

    /**
     * Supprime une tâche
     * @Route("/delete/{id}", name="delete", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function delete(Task $task, EntityManagerInterface $entityManager): Response
    {
        $this->addFlash('danger', "La tâche `{$task->getTitle()}` a été supprimée.");

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('browse');
    }
}
