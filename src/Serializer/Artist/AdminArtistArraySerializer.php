<?php

namespace App\Serializer\Artist;

use App\Entity\Wiki\Artist;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Intl\Countries;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class AdminArtistArraySerializer
{
    public function __construct(
        private readonly AdminArtistSocialSerializer $adminArtistSocialSerializer,
        private readonly UploaderHelper              $uploaderHelper,
        private readonly CacheManager                $cacheManager
    ) {
    }

    /**
     * @param Artist[] $artists
     */
    public function listToArray(array $artists): array
    {
        $result = [];
        foreach ($artists as $artist) {
            $result[] = $this->toArray($artist);
        }

        return $result;
    }

    public function toArray(Artist $artist): array
    {
        $imagePath = $artist->getCover() ? $this->uploaderHelper->asset($artist->getCover(), 'imageFile') : '';

        return [
            'id'           => $artist->getId(),
            'name'         => $artist->getName(),
            'slug'         => $artist->getSlug(),
            'biography'    => $artist->getBiography(),
            'label_name'   => $artist->getLabelName(),
            'country_name' => $artist->getCountryCode() ? Countries::getAlpha3Name($artist->getCountryCode()) : '',
            'country_code' => $artist->getCountryCode() ?: '',
            'members'      => $artist->getMembers(),
            'socials'      => $this->adminArtistSocialSerializer->listToArray($artist->getSocials()),
            'cover'        => $imagePath ? $this->cacheManager->getBrowserPath($imagePath, 'wiki_artist_cover_filter') : '',
        ];
    }
}
