<?php

namespace Ecotone\App;

use Ecotone\App\Infrastructure\EbookRepository;
use Ecotone\App\Model\Ebook;
use Ecotone\App\Model\Price;
use Ecotone\Messaging\Gateway\Converter\Serializer;
use function json_decode;

class EbookController
{
    public function __construct(private EbookRepository $ebookRepository, private Serializer $serializer)
    {
    }

    public function registerEbook(string $requestAsJson): void
    {
        $data = json_decode($requestAsJson, true, flags: JSON_THROW_ON_ERROR);

        $data["price"] = new Price($data["price"]);

        $this->ebookRepository->save(new Ebook($data));
    }

    public function getEbook(string $ebookId): string
    {
        return $this->serializer->convertFromPHP(
            $this->ebookRepository->getById($ebookId),
            "application/json"
        );
    }
}