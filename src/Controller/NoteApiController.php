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










}
