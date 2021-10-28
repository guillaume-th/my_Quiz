<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\Categorie;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use App\Form\UserType;
use App\Form\CategorieType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Question;
use App\Entity\Reponse;

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
    public function users(UserRepository $userRepository): Response
    {
        if ($this->testAdmin()) {
            return $this->render('admin/users.html.twig', [
                'controller_name' => 'AdminController',
                'users' => $userRepository->findAll(),
            ]);
        } else {
            return $this->redirectToRoute("categorie_index");
        }
    }

    /**
     * @Route("/users/{id}", name="admin_user_show", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function showUser(User $user): Response
    {
        if ($this->testAdmin()) {
            $stats = [];
            $stats = $user->getHistoriqueQuizzs();
            $roles = $user->getRoles();
            $roles = in_array('ROLE_ADMIN', $roles);

            return $this->render('admin/showUser.html.twig', [
                'user' => $user,
                "stats" => $stats,
                'admin' => $roles
            ]);
        } else {
            return $this->redirectToRoute("categorie_index");
        }
    }

    /**
     * @Route("/users/{id}", name="user_to_admin", methods={"POST"})
     */
    public function newAdmin(User $user): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user->setRoles(array('ROLE_ADMIN'));
        $entityManager->flush();
        return $this->redirectToRoute('admin_users', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/users/{id}/edit", name="admin_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        if ($this->testAdmin()) {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            $roles = $user->getRoles();
            $roles = in_array('ROLE_ADMIN', $roles);

            if ($form->isSubmitted() && $form->isValid()) {

                $user->setPassword(
                    $userPasswordHasherInterface->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(new Address('quiz@gmail.com', 'QuizBot'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );

                return $this->redirectToRoute('app_verify_wait', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('admin/editUser.html.twig', [
                'user' => $user,
                'form' => $form,
                'admin' => $roles
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
            $categories = $this->getDoctrine()
                ->getRepository(Categorie::class)
                ->findAll();
            return $this->render('admin/quizzes.html.twig', [
                'controller_name' => 'AdminController',
                'categories' => $categories,
            ]);
        } else {
            return $this->redirectToRoute("categorie_index");
        }
    }

    /**
     * @Route("/quizzes/{id}", name="admin_categorie_show", methods={"GET"})
     */
    public function showQuizzes(Categorie $categorie): Response
    {
        if ($this->testAdmin()) {
            return $this->render('categorie/show.html.twig', [
                'categorie' => $categorie,
            ]);
        } else {
            return $this->redirectToRoute("categorie_index");
        }
    }

    /**
     * @Route("/quizzes/{id}/edit", name="admin_categorie_edit", methods={"GET","POST"})
     */
    public function editQuizz(Request $request, Categorie $categorie): Response
    {
        if ($this->testAdmin()) {
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

                return $this->redirectToRoute('admin_quizzes', [], Response::HTTP_SEE_OTHER);
            }
            return $this->renderForm('admin/editQuizz.html.twig', [
                'categorie' => $categorie,
                'form' => $form,
            ]);
        }
        return $this->redirectToRoute("app_verify_wait");
        } else {
            return $this->redirectToRoute("categorie_index");
        }
    }

    private function testAdmin()
    {
        if ($this->get('security.token_storage')->getToken() !== null) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
