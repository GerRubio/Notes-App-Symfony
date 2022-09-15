<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\Task;
use App\Form\TaskType;

class TaskController extends AbstractController
{
    public function index(ManagerRegistry $doctrine): Response
    {   
        $em = $doctrine->getManager();

        # Get all the tasks.
        $task_repo = $doctrine->getRepository(Task::class);
        
        $tasks = $task_repo->findBy([], ['id' => 'ASC']);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Function to see the details of one task.
     */
    public function detail(Task $task)
    {
		if (!$task) {
			return $this->redirectToRoute('tasks');
		}
		
		return $this->render('task/detail.html.twig',[
			'task' => $task,
		]);
	}

    /**
     * Function that create a new task.
     */
    public function creation(ManagerRegistry $doctrine, Request $request, UserInterface $user): Response
    {
		$task = new Task();

		$form = $this->createForm(TaskType::class, $task);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$task->setCreatedAt(new \Datetime('now'));
			$task->setUser($user);
			
			$em = $doctrine->getManager();
			
            $em->persist($task);
			$em->flush();
			
			return $this->redirect($this->generateUrl('task_detail', ['id' => $task->getId()]));
		}
		
		return $this->render('task/creation.html.twig',[
			'form' => $form->createView(),
		]);
	}

    /**
     * Function to show the task of one user.
     */
    public function myTasks(UserInterface $user): Response
    {   
		$tasks = $user->getTasks();
				
		return $this->render('task/my-tasks.html.twig',[
			'tasks' => $tasks, 
		]);	
	}

    /**
     * Function to edit the tasks.
     */
    public function edit(ManagerRegistry $doctrine, Request $request, UserInterface $user, Task $task): Response
    {   
		if (!$user || $user->getId() != $task->getUser()->getId()) {
			return $this->redirectToRoute('tasks');
		}
		
		$form = $this->createForm(TaskType::class, $task);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$em = $doctrine->getManager();
			
            $em->persist($task);
			$em->flush();
			
			return $this->redirect($this->generateUrl('task_detail', ['id' => $task->getId()]));
		}
		
		return $this->render('task/creation.html.twig',[
			'edit' => true,
			'form' => $form->createView(),
		]);
	}
	
    /**
     * Function to delete one task.
     */
	public function delete(ManagerRegistry $doctrine, UserInterface $user, Task $task): Response
    {
		if (!$user || $user->getId() != $task->getUser()->getId()) {
			return $this->redirectToRoute('tasks');
		}
		
		if (!$task) {
			return $this->redirectToRoute('tasks');
		}
		
		$em = $doctrine->getManager();
		
        $em->remove($task);
		$em->flush();
		
		return $this->redirectToRoute('tasks');
	}
}
