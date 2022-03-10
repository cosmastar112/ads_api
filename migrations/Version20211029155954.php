<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211029155954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('ad');
        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
            'notnull' => true,
        ]);
        $table->addColumn('text', 'string', [
            'default' => null,
            'length' => 100,
        ]);
        $table->addColumn('price', 'integer', [
            'default' => null,
        ]);
        $table->addColumn('limit', 'integer', [
            'default' => null,
        ]);
        $table->addColumn('banner', 'string', [
            'default' => null,
            'length' => 100,
        ]);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('ad');
    }
}
