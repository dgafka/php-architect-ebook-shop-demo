<?php
use Doctrine\DBAL\DriverManager;

return DriverManager::getConnection(['url' => 'pgsql://ecotone:secret@database:5432/ecotone']);