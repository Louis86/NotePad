<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Twig\Environment;
use App\Repository\CategorieRepository;
use App\Entity\Note;
use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Form\NoteType;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;



class NoteController extends Controller
{
    /**
     * @Route("/", name="list_note")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $listNotes  = $em->getRepository(Note::class)->findAll();
        return $this->render('note/index.html.twig', $mesVars = array(
            'listNotes' => $listNotes));
    }

    /**
     * @Route("/note/ajouter", name = "ajt_note")
     */
    public function ajouternoteAction(Request $request)
    {
        $titre = "Ajouter une Note";
        $note = new Note(); // on cree une nouvelle note
        $form = $this->createForm(NoteType::class, $note);// on récupère le formulaire et on spécifi qu'il doit etre crée a partir de l'objet note
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($note);
            $em->flush();
            return $this->redirectToRoute('list_note');
        }
        // on génère le HTML du formulaire crée
        $formView = $form->createView();
        return $this->render('note/AjouterNote.html.twig', array('form' => $form->createView(),'titre' => $titre));
    }


    /**
     * @Route("/note/edit/{id}", name = "edit_note", requirements = { "id" = "\d+" })
     */
    public function edinotetAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Categorie::class)->findAll();
        $titre = "Modifier la Note";
        $note = new Note();
        $note = $em->getRepository(Note::class)->findOneById($id);
        $form = $this->createFormBuilder($note)
            ->add('title',   TextType::class, array(
                'data' => $note->getTitle(),
            ))
            ->add('content',   TextareaType::class, array(
                'data' => $note->getContent(),
            ))
            ->add('date',   DateType::class, array(
                'data' => $note->getDate(),
            ))
            ->add('categorie', ChoiceType::class, array(
                    'choices'    => $categories,
                    'choice_label' => function($cat, $key, $index){
                        return $cat->getLibelle();
                    })
            )
            ->add('Sauvegarder',   SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        $note = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($note);
            $em->flush();
            return $this->redirect($this->generateUrl('list_note', array(
                'id' => $note->getId())));
        }
        return $this->render('note/AjouterNote.html.twig', array('form' => $form->createView(),'titre' => $titre));
    }
    /**
     * @Route("/note/delete/{id}", name = "del_note", requirements = { "id" = "\d+" })
     */
    public function deletenoteAction($id)
    {
        $note = $this->getDoctrine()->getRepository(Note::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($note);
        $em->flush();
        return $this->redirect($this->generateUrl('list_note', array('id' => $note->getId())));
    }


}
