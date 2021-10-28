<?php

namespace App\Controller;

use App\Entity\HistoriqueQuizz;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Entity\Categorie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\EmailType;
use App\Form\CategorieChooserType;
use \Doctrine\Common\Collections\Criteria;

/**
 * @Route("/admin")
 */
class EmailController extends AbstractController
{
    /**
     * @Route("/email", name="admin_email")
     */
    public function index(): Response
    {
        return $this->render('email/index.html.twig', [
            'controller_name' => 'EmailController',
        ]);
    }

    /**
     * @Route("/email/lastMonth/{connected}", name="admin_email_last_month")
     */
    public function emailLastMonth($connected, MailerInterface  $mailer)
    {

        $request = Request::createFromGlobals();
        $form = $this->createForm(EmailType::class);
        $form->handleRequest($request);
        $connected = $connected === "true" ? true : false;
        if ($form->isSubmitted() && $form->isValid()) {
            $addresses = [];
            $date = new \DateTime();
            $date->modify("-1 month");

            $entityManager = $this->getDoctrine()->getManager();
            $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findAllConnectedLastPeriod($date, $connected);
            foreach ($users as $user) {
                array_push($addresses, $user->getEmail());
            }

            $email = (new Email())
                ->from("quizbot@mail.com")
                ->to(...$addresses)
                ->text($form->get("email")->getData());

            // $mailer = new MailerInterface(); 
            if (count($addresses) > 0) {
                $mailer->send($email);
                return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
            } else {
                return $this->render('admin/index.html.twig', ["errors" => "No adresses found"]);
            }
        }
        return $this->renderForm('email/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/email/quizz/{taken}/{ids}", name="admin_email_quiz")
     */
    public function emailQuizzTaken($taken, $ids, MailerInterface $mailer)
    {
        $request = Request::createFromGlobals();
        $form = $this->createForm(EmailType::class);
        $form->handleRequest($request);
        $ids = explode(";", $ids);
        array_pop($ids); 
        $taken = $taken === "true" ? true : false;

        if ($form->isSubmitted() && $form->isValid()) {
            $addresses = [];
            $entityManager = $this->getDoctrine()->getManager();
      
            $historiques = $this->getDoctrine()
                ->getRepository(HistoriqueQuizz::class)
                // ->findBy(["categorie" => $ids]); 
                ->findUsersByQuizzTaken($ids, $taken); 
            $users = []; 
                foreach ($historiques as $historique) {
                    $user = $historique->getUser(); 
                    array_push($addresses, $user->getEmail());
                    array_push($users, $user->getId()); 
                }
            if(!$taken){
                $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findUsersByNegId($users); 
                $addresses = []; 
                foreach($users as $u){
                    array_push($addresses, $u->getEmail()); 
                }
            }
           
            $email = (new Email())
                ->from("quizbot@mail.com")
                ->to(...$addresses)
                ->text($form->get("email")->getData());

            // $mailer = new MailerInterface(); 
            if (count($addresses) > 0) {
                $mailer->send($email);
                return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
            } else {
                return $this->render('admin/index.html.twig', ["errors" => "No user found"]);
            }
        }
        return $this->renderForm('email/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/email/{taken}", name="admin_email_choose_quiz")
     */
    public function chooseQuizzesEmail($taken, MailerInterface  $mailer)
    {
        $request = Request::createFromGlobals();

        $form = $this->createForm(CategorieChooserType::class, new Categorie());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // var_dump($categorieIds); 
            $data = $form->getData()->getCategorie(); 
            $categorieIds = "";
            foreach($data as $d){
                $categorieIds.= $d->id . ";"; 
            }
            return $this->redirectToRoute("admin_email_quiz", ["taken" => $taken, "ids"=>$categorieIds],  Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('email/new.html.twig', [
            'form' => $form,
        ]);
    }
}
