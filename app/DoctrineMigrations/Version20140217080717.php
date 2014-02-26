<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140217080717 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE Entry (id INT AUTO_INCREMENT NOT NULL, startDate DATE NOT NULL, endDate DATE NOT NULL, reason LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE entry_character (entry_id INT NOT NULL, character_id INT NOT NULL, INDEX IDX_EB133E76BA364942 (entry_id), INDEX IDX_EB133E761136BE75 (character_id), PRIMARY KEY(entry_id, character_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE entry_character ADD CONSTRAINT FK_EB133E76BA364942 FOREIGN KEY (entry_id) REFERENCES Entry (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE entry_character ADD CONSTRAINT FK_EB133E761136BE75 FOREIGN KEY (character_id) REFERENCES player (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE entry_character DROP FOREIGN KEY FK_EB133E76BA364942");
        $this->addSql("DROP TABLE Entry");
        $this->addSql("DROP TABLE entry_character");
    }
}
