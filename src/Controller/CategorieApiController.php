<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use App\Entity\Categorie;



class CategorieApiController extends Controller
{
    
    /**
     * @Route("/api/liste/categorie")
     */
    public function apiListeCategorieAction()
    {
        $rps = new Response();
        $encoders = array(new XmlEncoder(),new JsonEncode());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $repository = $this->getDoctrine() ->getRepository(Categorie::class);
        $cats = $repository->findAll();
        
        $jsonserial = $serializer->serialize($cats, 'json');
        $rps->setContent($jsonserial);
        return $rps;
        
    }

    /**
     * @Route("/api/ajout/categorie")
     */  
    public function apiAjoutCategorieAction(Request $request)
    {
       
        $contenu = $request->getContent();
        
        $cat_data = json_decode($contenu, true);
        
        $categorie = new Categorie();
        
        $categorie -> setLibelle($cat_data['libelle']);
        
        $em = $this->getDoctrine()->getManager();
        
        $em->persist($categorie);
        
        $em->flush();
        
        
       //   $save = $this->getDoctrine()->getRepository(Categorie::class)->findBylibelle($categorie->getLibelle());
        
        return new JsonResponse("sucess categorie Add");
        
    }    
    
    
     /**
     * @Route("/api/supprime/categorie/{categorie}")
     */  
    public function apiSupprimeCategorieAction(Categorie $categorie)
    {
       
        $em = $this->getDoctrine()->getManager()->findByID;
        
        $em->remove($categorie);
        
        $em->flush();
        
        return new JsonResponse(['sucess' => true]);
        
        
    }  
    
    
    
    /**
     * @Route("/api/edit/categorie/{id}")
     */
   
     public function apiEditCategorieAction($id, Request $requete){
         
         $cat = new Categorie();

         $contenu = $requete->getContent();

         $donneeCategorie = Json_decode($contenu,true);


         $em = $this->getDoctrine()->getManager();

         $cat = $this->getDoctrine()->getRepository(Categorie ::class)->findOneById($id);

         if(!$cat){



         }
         elseif(!empty($donneeCategorie ['libelle'])){

             $cat->setLibelle($donneeCategorie['libelle']);

             $em->persist($cat);

             $em->flush();

             return new JsonResponse(['sucess' => true]);
         }




     }
}
