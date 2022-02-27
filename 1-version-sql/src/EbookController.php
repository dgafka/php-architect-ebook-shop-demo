<?php

namespace Ecotone\App;

use function json_decode;

class EbookController
{
    public function __construct(private EbookService $ebookService)
    {
    }

    public function registerEbook(string $requestAsJson): void
    {
        $data = json_decode($requestAsJson, true, flags: JSON_THROW_ON_ERROR);

        $this->ebookService->registerEbook($data);
    }

    public function getEbook(string $ebookId): string
    {
        return json_encode($this->ebookService->getEbookById($ebookId));
    }
}