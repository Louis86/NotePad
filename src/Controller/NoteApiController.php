<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\NoteRepository;
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



use App\Entity\Note;

class NoteApiController extends Controller
{

    /**
     * @Route("/api/liste/note")
     */
    public function apiListeNoteAction()
    {
        $rps = new Response();
        $encoders = array(new XmlEncoder(),new JsonEncode());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $repository = $this->getDoctrine() ->getRepository(Note::class);
        $note = $repository->findAll();

        $jsonserial = $serializer->serialize($note, 'json');
        $rps->setContent($jsonserial);
        return $rps;

    }




    /**
     * @Route("/api/supprimer/note/{id}")
     */
    public function apiSupprimerNoteAction($id){

        $em = $this->getDoctrine()->getManager();
        $nt= $em->getRepository(Note::class)->find($id);

        if($nt) {

            $em->remove($nt);
            $em->flush();
            $response = new JsonResponse( array(
                'status'   => 'DELETED',
                'message'  => 'Le note est supprimé'
            ));

            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
        else {
            return new JsonResponse(
                array(
                    'status' => 'NOT FOUND',
                    'message' => 'This note does not exist'
                )
            );
        }
    }

    /**
     * @Route("/api/ajout/note")
     */
    public function PostNotesAction(Request $request)
    {
        $note = new Note();
        $em = $this->getDoctrine()->getManager();
        $body = $request -> getContent();
        if(empty($body)){
            return new JsonResponse("note vide");
        }
        $data = json_decode($body, true);
        if(!$data){
            return new JsonResponse("json code empty");
        }
        if(empty($data['title'])){
            return new JsonResponse("titre est vide");
        }
        if(empty($data['content'])){
            return new JsonResponse("contenu est vide");
        }
        if(empty($data['categorie'])){
            return new JsonResponse("categorie est vide");
        }
        $note->setTitle($data['title']);
        $note->setDate(new \DateTime('NOW'));
        $note->setContent($data['content']);
        $categories = $em->getRepository(Categorie::class)->findOneByLibelle($data['categorie']);
        if(!$categories){
            return new JsonResponse("categorie n'existe pas");
        }
        $note->setCategorie($categories);
        $em->persist($note);
        $em->flush();
        return new JsonResponse("note ajouté");
    }




    /**
     * @Route("/api/edit/note/{id}")
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
                    'message' => 'le corps d
                    e cette requete est vide')
            );


        }

        $eme = $this->getDoctrine()->getManager();
        $not = $eme->getRepository(Note::class)->find($id);



        if($not){
            $note_requete = json_decode($contenu, true);

            $not->setTitle($note_requete['title']);
            $not->setDate(new \DateTime('NOW'));
            $not->setContent($note_requete['content']);
            $categories = $eme->getRepository(Categorie::class)->findOneByLibelle($note_requete['categorie']);
            $not->setCategorie($categories);


            $eme->persist($not);
            $eme->flush();
            $response = new JsonResponse(

                array(
                    'status' => 'UPDATE',
                    'message' => 'la note est mise à jour.'
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

