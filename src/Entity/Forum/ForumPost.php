<?php

namespace App\Entity\Forum;

use ApiPlatform\Doctrine\Common\Filter\OrderFilterInterface;
use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\User;
use App\Repository\Forum\ForumPostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ForumPostRepository::class)]
#[ApiResource(operations: [
    new Get(normalizationContext: ['groups' => [ForumPost::ITEM]]),
    new GetCollection(normalizationContext: ['groups' => [ForumPost::LIST]], name: 'api_forum_posts_get_collection')
], paginationItemsPerPage: 10
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['topic' => SearchFilterInterface::STRATEGY_EXACT])]
#[ApiFilter(filterClass: OrderFilter::class, properties: ['creationDatetime' => OrderFilterInterface::DIRECTION_ASC])]
class ForumPost
{
    final const LIST = 'FORUM_POST_LIST';
    final const ITEM = 'FORUM_POST_ITEM';

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'guid')]
    #[ORM\GeneratedValue(strategy: 'UUID')]
    #[Groups([ForumPost::LIST, ForumTopic::LIST])]
    private $id;
    #[ORM\Column(type: 'datetime')]
    #[Groups([ForumPost::LIST, ForumTopic::LIST])]
    private $creationDatetime;
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups([ForumPost::LIST])]
    private $updateDatetime;
    #[ORM\Column(type: 'text')]
    #[Groups([ForumPost::LIST])]
    private $content;
    #[ORM\ManyToOne(targetEntity: ForumTopic::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $topic;
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([ForumPost::LIST, ForumTopic::LIST])]
    private $creator;

    public function __construct()
    {
        $this->creationDatetime = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getUpdateDatetime(): ?\DateTimeInterface
    {
        return $this->updateDatetime;
    }

    public function setUpdateDatetime(?\DateTimeInterface $updateDatetime): self
    {
        $this->updateDatetime = $updateDatetime;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getTopic(): ?ForumTopic
    {
        return $this->topic;
    }

    public function setTopic(?ForumTopic $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }
}