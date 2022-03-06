<?php

namespace Ecotone\App;

use Ecotone\App\Infrastructure\EbookRepository;
use Ecotone\App\Model\Ebook\Ebook;
use Ecotone\App\Model\Ebook\Price;
use Ecotone\Messaging\Gateway\Converter\Serializer;
use Ecotone\Modelling\CommandBus;
use function json_decode;

class EbookController
{
    public function __construct(private CommandBus $commandBus, private EbookRepository $ebookRepository, private Serializer $serializer)
    {
    }

    public function registerEbook(string $requestAsJson): void
    {
        $this->commandBus->sendWithRouting("ebook.register", $requestAsJson, "application/json");
    }

    public function getEbook(string $ebookId): string
    {
        return $this->serializer->convertFromPHP(
            $this->ebookRepository->getById($ebookId),
            "application/json"
        );
    }
}