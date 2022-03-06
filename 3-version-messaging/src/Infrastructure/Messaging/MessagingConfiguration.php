<?php

namespace Ecotone\App\Infrastructure\Messaging;

use Ecotone\Dbal\DbalBackedMessageChannelBuilder;
use Ecotone\Messaging\Attribute\ServiceContext;

class MessagingConfiguration
{
    #[ServiceContext]
    public function registerMessageChannel()
    {
        return DbalBackedMessageChannelBuilder::create("order_channel");
    }
}