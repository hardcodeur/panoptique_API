<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423234038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE mission DROP FOREIGN KEY FK_9067F23C4584665A
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE location_note (id INT AUTO_INCREMENT NOT NULL, location_id INT NOT NULL, title VARCHAR(100) NOT NULL, note LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_4FB94F5464D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shift (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, mission_id INT NOT NULL, start DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', end DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', activity VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_A50B3B45A76ED395 (user_id), INDEX IDX_A50B3B45BE6CAE90 (mission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location_note ADD CONSTRAINT FK_4FB94F5464D218E FOREIGN KEY (location_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shift ADD CONSTRAINT FK_A50B3B45A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shift ADD CONSTRAINT FK_A50B3B45BE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customer ADD location_id INT NOT NULL, ADD product VARCHAR(100) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customer ADD CONSTRAINT FK_81398E0964D218E FOREIGN KEY (location_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_81398E0964D218E ON customer (location_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mission DROP FOREIGN KEY FK_9067F23C64D218E
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9067F23C4584665A ON mission
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9067F23C64D218E ON mission
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mission ADD team_id INT NOT NULL, DROP product_id, DROP location_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mission ADD CONSTRAINT FK_9067F23C296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9067F23C296CD8AE ON mission (team_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD profil_picture_path VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location_note DROP FOREIGN KEY FK_4FB94F5464D218E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shift DROP FOREIGN KEY FK_A50B3B45A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE shift DROP FOREIGN KEY FK_A50B3B45BE6CAE90
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE location_note
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shift
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mission DROP FOREIGN KEY FK_9067F23C296CD8AE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9067F23C296CD8AE ON mission
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mission ADD location_id INT NOT NULL, CHANGE team_id product_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mission ADD CONSTRAINT FK_9067F23C4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mission ADD CONSTRAINT FK_9067F23C64D218E FOREIGN KEY (location_id) REFERENCES location (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9067F23C4584665A ON mission (product_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9067F23C64D218E ON mission (location_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP profil_picture_path
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customer DROP FOREIGN KEY FK_81398E0964D218E
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_81398E0964D218E ON customer
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customer DROP location_id, DROP product
        SQL);
    }
}
