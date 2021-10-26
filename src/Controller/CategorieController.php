<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\User;
use App\Entity\Reponse;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Visiteur;

/**
 * @Route("/categorie")
 */
class CategorieController extends AbstractController
{
    /**
     * @Route("/", name="categorie_index", methods={"GET"})
     */
    public function index(): Response
    {
        $session = new Session();

        if($session->get('Visiteur')==null){
        $session = new Session();
        $session->set('Visiteur', 1 );
        $entityManager = $this->getDoctrine()->getManager();

        $product = new Visiteur();
        $product->settime(new \DateTime());
        $entityManager->persist($product);
        $entityManager->flush();
        }

        $user = $this->get('security.token_storage')->getToken();
        if($user){
            $user=$user->getUser(); 
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->find($user->getId());
            $user->setLastLogin(new \DateTime('NOW'));
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $categories = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->findAll();
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/new", name="categorie_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($user->isVerified()) {
            $categorie = new Categorie();
            $questions = [];
            $reponses = [];
            for ($i = 0; $i < 10; $i++) {
                $q = new Question();
                $r1 = new Reponse();
                $r2 = new Reponse();
                $r3 = new Reponse();
                $q->addReponse($r1);
                $q->addReponse($r2);
                $q->addReponse($r3);
                array_push($questions, $q);
                array_push($reponses, $r1, $r2, $r3);
                $categorie->addCategorie($q);
            }

            $form = $this->createForm(CategorieType::class, $categorie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($categorie);
                foreach ($questions as $q) {
                    $entityManager->persist($q);
                }
                foreach ($reponses as $r) {
                    $entityManager->persist($r);
                }
                $entityManager->flush();
                // $id = $this->getDoctrine()->getRepository(Categorie::class)->findBy(array(),array('id'=>'DESC'),1,0);

                return $this->redirectToRoute('categorie_index', [], Response::HTTP_SEE_OTHER);
            }
            return $this->renderForm('categorie/new.html.twig', [
                'categorie' => $categorie,
                'form' => $form,
            ]);
        }
        return $this->redirectToRoute("app_verify_wait");
    }

    /**
     * @Route("/{id}", name="categorie_show", methods={"GET"})
     */
    public function show(Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categorie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Categorie $categorie): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_delete", methods={"POST"})
     */
    public function delete(Request $request, Categorie $categorie): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorie->id, $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
