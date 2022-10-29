<?php

namespace App\Entity\Image;

use Exception;
use DateTimeImmutable;
use DateTimeInterface;
use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity]
#[Vich\Uploadable]
class UserProfilePicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: Types::INTEGER)]
    private $id;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Assert\Image(maxSize: "4Mi", minWidth: 450, maxWidth: 4000, maxHeight: 4000, minHeight: 450, allowLandscape: true, allowPortrait: true)]
    #[Vich\UploadableField(mapping: 'user_profile_picture', fileNameProperty: 'imageName', size: 'imageSize')]
    private $imageFile;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private $imageName;

    #[ORM\Column(type: Types::INTEGER)]
    private $imageSize;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private $updatedAt;

    #[ORM\OneToOne(targetEntity: User::class)]
    private $user;

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     *
     * @throws Exception
     * @return $this
     */
    public function setImageFile(?File $image = null)
    {
        $this->imageFile = $image;
        if (null !== $image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setImageName(?string $imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize)
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): UserProfilePicture
    {
        $this->user = $user;

        return $this;
    }
}
