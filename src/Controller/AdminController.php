<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_index", methods={"GET"})
     */
    public function index(): Response
    {
        if ($this->testAdmin()) {
            return $this->render('admin/index.html.twig', [
                'controller_name' => 'AdminController',
            ]);
        } else {
            return $this->redirectToRoute("categorie_index");
        }
    }

    /**
     * @Route("/stats", name="admin_statistiques", methods={"GET"})
     */
    public function stats(): Response
    {
        if ($this->testAdmin()) {
            return $this->render('admin/stats.html.twig', [
                'controller_name' => 'AdminController',
            ]);
        } else {
            return $this->redirectToRoute("categorie_index");
        }
    }

    /**
     * @Route("/users", name="admin_users", methods={"GET"})
     */
    public function users(): Response
    {
        if ($this->testAdmin()) {
            return $this->render('admin/users.html.twig', [
                'controller_name' => 'AdminController',
            ]);
        } else {
            return $this->redirectToRoute("categorie_index");
        }
    }

    /**
     * @Route("/quizzes", name="admin_quizzes", methods={"GET"})
     */
    public function quizzes(): Response
    {
        if ($this->testAdmin()) {
            return $this->render('admin/quizzes.html.twig', [
                'controller_name' => 'AdminController',
            ]);
        } else {
            return $this->redirectToRoute("categorie_index");
        }
    }

    private function testAdmin()
    {
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        } else {
            return false;
        }
    }
}
