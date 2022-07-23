<?php

namespace App\Service\Updater\Wiki;

use App\Entity\Wiki\Artist;
use Doctrine\ORM\EntityManagerInterface;

class ArtistUpdater
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function update(Artist $artist, Artist $newArtist): void
    {
        $artist->setLabelName($newArtist->getLabelName());
        $artist->setBiography($newArtist->getBiography());
        $artist->setMembers($newArtist->getMembers());
        $artist->setCountryCode($newArtist->getCountryCode());
        foreach ($artist->getSocials() as $social) {
            $artist->removeSocial($social);
            $this->entityManager->remove($social);
        }
        foreach ($newArtist->getSocials() as $social) {
            $artist->addSocial($social);
        }
    }
}
