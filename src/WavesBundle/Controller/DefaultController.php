<?php

namespace WavesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Leafo\ScssPhp\Compiler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use WavesBundle\Entity\Music;
use WavesBundle\Form\MusicType;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        //Creer l'objet Compiler()
        $scss = new Compiler();
        //formatage du Css
        $scss->setFormatter(new \Leafo\ScssPhp\Formatter\Expanded());
        //Path SaSS
        $sassFilesPath = $this->get('kernel')->getRootDir() . '/../web/framework/sass/';
        //Path Css
        $cssFilesPath = $this->get('kernel')->getRootDir() . '/../web/framework/css/';
        // Ecrit dans les fichier CSS 'all.css'
        file_put_contents(
            $cssFilesPath. "style.css",
            $scss->compile(
                file_get_contents(
                    $sassFilesPath. "style.scss"
                )
            )
        );

        //Construction Formulaires :
        $CreateMusic = new Music();
        $FormMusic = $this->createForm(MusicType::class, $CreateMusic);
        //Entete de requete
        $FormMusic->handleRequest($request);

        //Récupération des Playlist (all)
        $playlist = $this->getDoctrine()
        ->getRepository('WavesBundle:Playlist')
        ->findAll();
        //duplique la playlists
        $playlistcopie = $playlist;

        //Récupére les PlayListe User
        $user = $this->getUser();
        if($user){
            if ($UserId = $user->getId()){
                if(!empty($UserId)){
                    $PlayListUser = [];
                    $PlayListUser = $this->getDoctrine()->getManager()->getRepository('WavesBundle:Music')->getPlaylisteUser($UserId);
                }
            }
        }else { $PlayListUser = false;}
        //$PlayListUser = false;
        //Récupération des music (all)
        $music = $this->getDoctrine()
        ->getRepository('WavesBundle:Music')
        ->findAll();
        //Récupération des Commentaires (all)
        $commentaire = $this->getDoctrine()
        ->getRepository('WavesBundle:Commentaire')
        ->findAll();

        if( $FormMusic->isSubmitted()){
            $file = $CreateMusic->getFile();
            // var_dump($file->getClientOriginalName());exit();
            
           // $fileName = $file->getClientOriginalName();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();  
            $CreateMusic->setSrc('../framework/audio/'.$fileName);
            $file->move(
                $this->getParameter('path_music'),
                $fileName
            );

            $CreateMusic->setFile($fileName);

            //return $this->redirect($this->generateUrl('app_product_list'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($CreateMusic);
            $em->flush();
            return $this->redirectToRoute('waves_homepage');
        }

        return $this->render('WavesBundle:Default:home.html.twig', array(
            'playlist' => $playlist,
            'playlistcopie' => $playlistcopie,
            'PlayListUser' => $PlayListUser,
            'music' => $music,
            'commentaire' => $commentaire,
            'FormMusic' => $FormMusic->createView(),
        ));
    }

    /**
    * @return string
    */
     private function generateUniqueFileName()
     {
         // md5() reduces the similarity of the file names generated by
         // uniqid(), which is based on timestamps
         return md5(uniqid());
     }
}
