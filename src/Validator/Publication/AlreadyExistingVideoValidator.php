<?php

namespace App\Validator\Publication;

use App\Entity\Publication;
use App\Repository\PublicationRepository;
use App\Service\Google\YoutubeUrlHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AlreadyExistingVideoValidator extends ConstraintValidator
{
    public function __construct(
        private readonly YoutubeUrlHelper      $youtubeUrlHelper,
        private readonly PublicationRepository $publicationRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof AlreadyExistingVideo) {
            throw new UnexpectedTypeException($constraint, AlreadyExistingVideo::class);
        }
        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }
        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }
        $videoId = $this->youtubeUrlHelper->getVideoId($value);
        if (!$this->publicationRepository->findOneBy(['content' => $videoId, 'type' => Publication::TYPE_VIDEO])) {
            return;
        }
        // the argument must be a string or an object implementing __toString()
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}