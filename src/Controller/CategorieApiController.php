<?php

namespace App\Controller;

use Symfony\Component\DependencyInjection\Tests\Compiler\C;
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


    private function crossOriginResource(){
        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            $response = new Response();
            $response->headers->set('Content-Type', 'application/text'); //L'en-tête Content-Type sert à indiquer le type MIME de la ressource.
            $response->headers->set('Access-Control-Allow-Origin', '*'); // origin définit un URI qui peut accéder à la ressource.
            $response->headers->set("Access-Control-Allow-Methods", "GET, PUT, POST, DELETE, OPTIONS"); //L'en-tête Access-Control-Allow-Methods indique la ou les méthodes qui sont autorisées pour accéder à la ressourec.
            return $response;
        }
    }




    /**
     * @Route("/api/liste/categorie")
     */
    public function apiListeCategorieAction()
    {
        $this->crossOriginResource();
        $rs = new Response();
        $rs->headers->set('Content-Type','application/json');
        $rs->headers->set('Access-Control-Allow-Origin','*');
        $rs->headers->set('Access-Control-Allow-Methods','GET, OPTIONS');
        $encoders = array(new XmlEncoder(),new JsonEncode());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $repository = $this->getDoctrine() ->getRepository(Categorie::class);
        $cats = $repository->findAll();
        $jsonserial = $serializer->serialize($cats, 'json');
        $rs->setContent($jsonserial);
        return $rs;
    }


    /**
     * @Route("/api/ajout/categorie")
     */
    public function apiAjoutCategorieAction(Request $request){

        $this->crossOriginResource();
        $rs = new Response();
        $rs->headers->set('Content-Type','application/json');
        $rs->headers->set('Access-Control-Allow-Origin','*');
        $rs->headers->set('Access-Control-Allow-Methods','POST, OPTIONS');

        $contenu = $request->getContent();
        $cat_donnee = json_decode($contenu, true);
        $objetCategorie = new Categorie();
        $objetCategorie ->setLibelle($cat_donnee['libelle']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($objetCategorie);
        $em->flush();
        $rs->setStatusCode(Response::HTTP_OK);
        $response= array('success'=> 'categorie ajouter ');
        $jsoncontent = json_encode($response);
        $rs->setContent($jsoncontent);
        return $rs;
    }



  /**
   * @Route("/api/supprimer/categorie/{id}")
   */
  public function apiSupprimerCategorieAction($id){

      $em = $this->getDoctrine()->getManager();
      $ct= $em->getRepository(Categorie::class)->find($id);

      if($ct) {

          $em->remove($ct);
          $em->flush();
          $response = new JsonResponse( array(
              'status'   => 'DELETED',
              'message'  => 'Le categorier est supprimé'
          ));

          $response->headers->set('Access-Control-Allow-Origin', '*');
          return $response;
      }
      else {
          return new JsonResponse(
              array(
                  'status' => 'NOT FOUND',
                  'message' => 'This category does not exist'
              )
          );
      }
  }


    /**
     * @Route("/api/edit/categorie/{id}")
     */
        public function apiEditCategorieAction(Request $requete ,$id){


            $encoders = array(new XmlEncoder(),new JsonEncode());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);


            $contenu = $requete->getContent();

            if(empty($contenu)){

                return new JsonResponse(
                    array(
                        'status' => 'VIDE',
                        'message' => 'le corps de cette requete est vide')
                );


            }

            $eme = $this->getDoctrine()->getManager();
            $cat= $eme->getRepository(Categorie::class)->find($id);



            if($cat){
                $categorie_requete = json_decode($contenu, true);

                $cat->setLibelle($categorie_requete['libelle']);
                $eme->persist($cat);
                $eme->flush();
                $response = new JsonResponse(

                    array(
                        'status' => 'UPDATE',
                        'message' => 'la categorie est mise à jour.'
                    )
                );

                $response->headers->set('Content-Type', 'application/json');
                $response->headers->set('Access-Control-Allow-Origin', '*');
                $response->setStatusCode(Response::HTTP_OK);

                return $response;


            }

            else {
                return new JsonResponse(
                    array(
                        'status' => 'NOT FOUND',
                        'message' => 'le libelle nexiste pas'
                    )
                );
            }

        }

}
