<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\RegistryInterface;
//use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Twig\Environment;
use App\Repository\CategorieRepository;
use App\Entity\Categorie;
use App\Form\CategorieType;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;



class CategorieController extends Controller
{

    /**
     * @Route("/categorie/liste", name="list_cat")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository(Categorie::class)->findAll();
        return $this->render('categorie/index.html.twig', array('cat' => $cat,));
    }

    /**
     * @Route("/categorie/ajout", name="ajoutCat")
     */
    public function AjoutCategory(Request $request)
    {
        // on commence par cree une nouvelle categorie
        $categorie = new Categorie();
        $erreur ="";
        $titre = "Ajouter une categorie";
        // on recupère le formulaire
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        // si le formulaire à ete soumis
        if ($form->isSubmitted() && $form->isValid()) {
            $save = $this
                ->getDoctrine()
                ->getRepository(Categorie::class)
                ->findBylibelle($categorie->getLibelle());
            if(!$save) {
                //on enregistre le produit en base de donnée
                $em = $this->getDoctrine()->getManager();
                $em->persist($categorie); // prepare l'objet pour l'insere dans la base de donnée
                $em->flush(); // évacu les données vers la base de donnée
                $erreur = "Categorie ajouté ";
                $formView = $form->createView();
                return $this->render('categorie/AjoutCategorie.html.twig', array('form'=>$formView,'erreur' => $erreur, 'class' => '', 'class' => "alert alert-success",'titre' => $titre));

            }
            else {
                $erreur = "La categorie existe déja";
                $formView = $form->createView();
                return $this->render('categorie/AjoutCategorie.html.twig', array('form'=>$formView,'erreur' => $erreur, 'class' => '', 'class' => "alert alert-danger",'titre' => $titre));

                //return $this->redirectToRoute('ajt_cat', array('erreur' => "Cette categorie existe déjà."));
            }

            return $this->redirectToRoute('list_cat');
        }
        $formView = $form->createView();
        // on genère le HTML du formulaire et on rend la vue
        return $this->render('categorie/AjoutCategorie.html.twig', array('form'=>$formView, 'erreur' => $erreur, 'class' => '','titre' => $titre));
    }

    /**
     * @Route("/categorie/editer/{id}", name="edit_cat", requirements = { "id" = "\d+" })
     */
    public function editecatAction(Request $request, $id)
    {
        $cat = new Categorie();
        $em = $this->getDoctrine()->getManager();
        $save = $em->getRepository(Categorie::class)->find($id);
        $erreur = "";
        $titre = "Modifier une categorie";
        $form = $this->createFormBuilder($cat) ->add('Libelle', TextType::class, array('data' => $save -> getLibelle(),))->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $save->setLibelle($cat->getLibelle());
            $repository = $this->getDoctrine()->getManager();
            $reposi = $repository->getRepository(Categorie::class)->findOneBylibelle($cat->getLibelle());

            if(!$reposi){
                $erreur = "La categorie a ete modifier";
                $em = $this->getDoctrine()->getManager();
                $em->persist($save);
                $em->flush();
            }
            else {
                $erreur = "La categorie existe déja";
                $formView = $form->createView();
                return $this->render('categorie/AjoutCategorie.html.twig', array('form'=>$formView, 'erreur' => $erreur, 'class' => "alert alert-danger",'titre' => $titre));
            }
            return $this->redirectToRoute('list_cat');
        }
        $formView = $form->createView();
        return $this->render('categorie/AjoutCategorie.html.twig', array('form'=>$formView, 'erreur' => $erreur, 'class' => '','titre' => $titre));
    }

    /**
     * @Route("/categorie/supprimer/{id}", name="del_cat", requirements = { "id" = "\d+" })
     */
    public function supprimercatAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository(Categorie::class)->find($id);
        $em->remove($cat);
        $em->flush();
        return $this->redirect($this->generateUrl('list_cat', array('id' => $cat->getId())));
    }

}
