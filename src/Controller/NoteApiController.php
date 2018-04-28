<?php

namespace App\Controller;

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
    public function allNoteAction()
    {
        $rs = new Response();
        $encoders = array(new XmlEncoder(),new JsonEncode());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $repository = $this->getDoctrine() ->getRepository(Note::class);
        $nots = $repository->findAll();
        
        $jsonserial = $serializer->serialize($nots, 'json');
        $rs->setContent($jsonserial);
        return $rs;
        
    }





    /**
     * @Route("/api/ajout/note")
     */
    public function apiAjoutNote(Request $request)
    {

        $contenu = $request->getContent();

        $note_data = json_decode($contenu, true);

        $note= new Note();

        $note ->setTitle($note_data['titre']);

        $note -> setContent($note_data ['contenu']);

        $note -> setDate($note_data ['date']);

        $em = $this->getDoctrine()->getManager();

        $em->persist($note);

        $em->flush();

        return new JsonResponse("sucess note Add");

    }


    /**
     * @Route("/api/supprime/note/{note}")
     */
    public function apiSupprimeNoteAction(Note $note)
    {

        $em = $this->getDoctrine()->getManager()->findByID;

        $em->remove($note);

        $em->flush();

        return new JsonResponse(['sucess' => true]);


    }



    /**
     * @Route("/api/edit/note/{id}")
     */

    public function apiEditNoteAction($id, Request $requete){

        $note = new Note();

        $contenu = $requete->getContent();

        $donneeNote = Json_decode($contenu,true);


        $em = $this->getDoctrine()->getManager();

        $not = $this->getDoctrine()->getRepository(Note ::class)->findOneById($id);

        if(!$not){



        }
        elseif(!empty($donneeNote ['titre'])){

            $note->setTitle($donneeNote['titre']);


            $note -> setContent($donneeNote['contenu']);

            $note -> setDate($donneeNote['date']);

            $em->persist($note);

            $em->flush();

            return new JsonResponse(['sucess' => true]);
        }

    }





}
