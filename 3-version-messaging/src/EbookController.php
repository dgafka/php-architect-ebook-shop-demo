<?php

namespace Ecotone\App;

use Ecotone\App\Infrastructure\EbookRepository;
use Ecotone\App\Model\Ebook\Ebook;
use Ecotone\App\Model\Ebook\Price;
use Ecotone\Messaging\Gateway\Converter\Serializer;
use Ecotone\Modelling\CommandBus;
use Ecotone\Modelling\QueryBus;
use function json_decode;

class EbookController
{
    public function __construct(private CommandBus $commandBus, private QueryBus $queryBus)
    {
    }

    public function registerEbook(string $requestAsJson): void
    {
        $this->commandBus->sendWithRouting("registerEbook", $requestAsJson, "application/json");
    }

    public function getEbook(string $ebookId): string
    {
        return $this->queryBus->sendWithRouting("getEbook", $ebookId, expectedReturnedMediaType: "application/json");
    }
}