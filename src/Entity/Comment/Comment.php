<?php

namespace App\Entity\Comment;

use DateTimeInterface;
use DateTime;
use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Contracts\AuthorableEntityInterface;
use App\Entity\User;
use App\Repository\Comment\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(operations: [
    new Get(normalizationContext: ['groups' => [Comment::ITEM]]),
    new GetCollection(normalizationContext: ['groups' => [Comment::LIST]], name: 'api_comments_get_collection'),
    new Post(
        normalizationContext: ['groups' => [Comment::ITEM]],
        denormalizationContext: ['groups' => [Comment::POST]],
        name: 'api_comments_post_collection'
    )
])]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['thread' => SearchFilterInterface::STRATEGY_EXACT])]
class Comment implements AuthorableEntityInterface
{
    const LIST = 'COMMENT_LIST';
    const ITEM = 'COMMENT_ITEM';
    const POST = 'COMMENT_POST';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[Groups([Comment::ITEM, Comment::LIST])]
    private int $id;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: CommentThread::class, inversedBy: "comments")]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([Comment::POST])]
    private CommentThread $thread;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([Comment::ITEM, Comment::LIST])]
    private User $author;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups([Comment::ITEM, Comment::LIST])]
    private DateTimeInterface $creationDatetime;

    #[Assert\NotBlank(message: 'Le commentaire est vide')]
    #[ORM\Column(type: Types::TEXT)]
    #[Groups([Comment::ITEM, Comment::POST, Comment::LIST])]
    private string $content;

    public function __construct()
    {
        $this->creationDatetime = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getThread(): CommentThread
    {
        return $this->thread;
    }

    public function setThread(CommentThread $thread): static
    {
        $this->thread = $thread;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getCreationDatetime(): DateTimeInterface
    {
        return $this->creationDatetime;
    }

    public function setCreationDatetime(DateTimeInterface $creationDatetime): static
    {
        $this->creationDatetime = $creationDatetime;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = trim($content);

        return $this;
    }
}
