<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
