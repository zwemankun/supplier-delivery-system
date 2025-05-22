<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250522083454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery ADD order_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery ADD CONSTRAINT FK_3781EC108D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3781EC108D9F6D38 ON delivery (order_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery DROP FOREIGN KEY FK_3781EC108D9F6D38
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_3781EC108D9F6D38 ON delivery
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery DROP order_id
        SQL);
    }
}
