<?php

namespace WavesBundle\Entity;

/**
 * MusicRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MusicRepository extends \Doctrine\ORM\EntityRepository
{
   public function getPlayliste($id)
   {
      $rawSQL = "SELECT * FROM `playlist_music` WHERE `play_id`=".$id;
      $stmt = $this->getEntityManager()->getConnection()->prepare($rawSQL);
      $stmt->execute([]);
      return $stmt->fetchAll();
   }

   public function getMusic($id)
   {
      $rawSQL = "SELECT * FROM `music` WHERE `music_id` =".$id;
      $stmt = $this->getEntityManager()->getConnection()->prepare($rawSQL);
      $stmt->execute([]);
      return $stmt->fetch();
   }

   public function getPlaylisteUser($id)
   {
      $rawSQL = "SELECT * FROM `playlist` WHERE `user_id`=".$id;
      $stmt = $this->getEntityManager()->getConnection()->prepare($rawSQL);
      $stmt->execute([]);
      return $stmt->fetchAll();
   }
}