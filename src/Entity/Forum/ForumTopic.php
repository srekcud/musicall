<?php

namespace App\Entity\Forum;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\User;
use App\Repository\Forum\ForumTopicRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ForumTopicRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => [ForumTopic::LIST]]],
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['groups' => [ForumTopic::ITEM]]],
    ],
    attributes: ['pagination_client_enabled' => true, 'pagination_items_per_page' => 15]
)]
#[ApiFilter(SearchFilter::class, properties: ['forum' => SearchFilterInterface::STRATEGY_EXACT])]
#[ApiFilter(OrderFilter::class, properties: ['creationDatetime' => 'DESC'])]
class ForumTopic
{
    final const LIST = 'FORUM_TOPIC_LIST';
    final const ITEM = 'FORUM_TOPIC_ITEM';

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'guid')]
    #[ORM\GeneratedValue(strategy: 'UUID')]
    #[Groups([ForumTopic::LIST])]
    private $id;
    #[ORM\ManyToOne(targetEntity: Forum::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([ForumTopic::ITEM])]
    private $forum;
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([ForumTopic::LIST, ForumTopic::ITEM])]
    private $title;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $slug;
    #[ORM\Column(type: 'integer')]
    #[Groups([ForumTopic::LIST])]
    private $type;
    #[ORM\Column(type: 'boolean')]
    #[Groups([ForumTopic::LIST])]
    private $isLocked;
    #[ORM\ManyToOne(targetEntity: ForumPost::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([ForumTopic::LIST])]
    private $lastPost;
    #[ORM\Column(type: 'datetime')]
    #[Groups([ForumTopic::LIST])]
    private $creationDatetime;
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([ForumTopic::LIST])]
    private $author;
    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Groups([ForumTopic::LIST])]
    private $postNumber;

    public function __construct()
    {
        $this->creationDatetime = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getForum(): ?Forum
    {
        return $this->forum;
    }

    public function setForum(?Forum $forum): self
    {
        $this->forum = $forum;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIsLocked(): ?bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(bool $isLocked): self
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    public function getLastPost(): ?ForumPost
    {
        return $this->lastPost;
    }

    public function setLastPost(?ForumPost $lastPost): self
    {
        $this->lastPost = $lastPost;

        return $this;
    }

    public function getCreationDatetime(): \DateTime
    {
        return $this->creationDatetime;
    }

    public function setCreationDatetime(\DateTimeInterface $creationDatetime): self
    {
        $this->creationDatetime = $creationDatetime;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPostNumber(): ?int
    {
        return $this->postNumber;
    }

    public function setPostNumber(int $postNumber): self
    {
        $this->postNumber = $postNumber;

        return $this;
    }
}
