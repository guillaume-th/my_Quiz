<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('categorie_index');
    }
    /**
     * @Route("/autre", name="home")
     */
    public function home(): Response
    {
        /**
         * @ORM\id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="question")
         */
        // private $id;
        return $this->render('index/index.html.twig', [
            // $em = $this->getDoctrine()->getManager();
            // $tableName = $em->getClassMetadata('StoreBundle:User')->getTableName();
            'quiz_name' => 'hello',
        ]);

    }
}
