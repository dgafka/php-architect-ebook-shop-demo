<?php

namespace Ecotone\App\Infrastructure;

use Ecotone\App\Model\Ebook\Ebook;
use Ecotone\Modelling\Attribute\Repository;
use Ecotone\Modelling\StandardRepository;

#[Repository]
class EcotoneRepository implements StandardRepository
{
    public function __construct(private EbookRepository $ebookRepository) {}

    public function canHandle(string $aggregateClassName): bool
    {
        return in_array($aggregateClassName, [Ebook::class]);
    }

    public function findBy(string $aggregateClassName, array $identifiers): ?object
    {
        $id = array_pop($identifiers);

        return $this->ebookRepository->getById($id);
    }

    public function save(array $identifiers, object $aggregate, array $metadata, ?int $versionBeforeHandling): void
    {
        $this->ebookRepository->save($aggregate);
    }
}