<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Form\CommandeType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
class CommandeController extends AbstractController
 /**
     * @Route("/commandes")
     */
{
    /**
     * @Route("/", name="commande")
     */
    public function index(CommandeRepository $repo): Response
    {
        $commandes = $repo->findAll();

        return $this->render('commande/index.html.twig',[
            "commandes" =>$commandes,
        ]);
    }
/**
 * * @Route("/add", name="command_add")
 */
    public function addCommande(Request $request, MailerInterface $mailer){
        $commande= new Commande();
        $form = $this->createForm(CommandeType::class,$commande);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
         
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commande);
            $entityManager->flush();
            
             // $this->emailCommande($mailer, $commande,$commande->getUser());
            return $this->redirectToRoute("shop");

        }

        return $this->render("shop/commande.html.twig",[
            'commande' =>$commande,
            'form' => $form->createView()
        ]);
    }



/**
 * @Route("/edit/{id}", name="commande_edit", methods={"GET", "POST"})
 */
public function edit(CommandeRepository $repo,Request $request, EntityManagerInterface $entityManager,int $id): Response
{
    $commande = $repo->find($id);
    $form = $this->createForm(CommandeType::class,$commande);
    $form->handleRequest($request);
   

    if($form->isSubmitted() && $form->isValid())
        {
          
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute("commande");
    
        }
        return $this->render("shop/commande.html.twig", [
            'commande' =>$commande,
               'form' => $form->createView()
        ]);
}

    /**
     * @Route("/delete/{id}", name="commande_delete")
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, $id , CommandeRepository $repo): Response
    {

        $commande = $repo->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($commande);
        $entityManager->flush();
     
        return $this->redirectToRoute("commande");
    }


      /**
     * @Route("/stripe", name="stripe")
     */
    
    public function stripe( SessionInterface $session): Response
    {
        \Stripe\Stripe::setApiKey('sk_test_51Kb76ABM0Kal1h8BwCJNP3B3evMy6K8B9Kib8ZQVL5El9QactHpPjI2bqUO1bw9pthM2fUh6sWTZVrmyrgtcobSj00a6mmF3sK');
        $amount=$session->get('total');
    

        \Stripe\Charge::create(array(
            "amount"=>$amount,
            "currency"=>"eur",
            "source"=>"tok_visa",
            "description"=>"Paiement réussie",
        ));
        return $this->render('shop/payment.html.twig');
    }

    public function emailCommande(MailerInterface $mailer, $commande, $user){


        //src="{{ email.image('@newsletterImages/img/infernalLogo.png') }}"
        $email = (new TemplatedEmail())
            ->from('infernalgames2022@gmail.com')
          //  ->to($user->getEmail())
            ->subject('Infernal Games - Purchase')
            ->htmlTemplate('shop\emailCommande.html.twig')
            ->context([ 'commande'=>$commande, 'user'=>$user]);

        $mailer->send($email);
        return null;
    }
}
