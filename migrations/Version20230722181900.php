<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230722181900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE crowdlending (id INT AUTO_INCREMENT NOT NULL, platform_id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, invested_amount DOUBLE PRECISION NOT NULL, current_value DOUBLE PRECISION NOT NULL, duration INT DEFAULT NULL, investment_date DATE DEFAULT NULL, annual_yield DOUBLE PRECISION DEFAULT NULL, INDEX IDX_8B1F9F78FFE6496F (platform_id), INDEX IDX_8B1F9F787E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crowdlending_platform (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE crowdlending ADD CONSTRAINT FK_8B1F9F78FFE6496F FOREIGN KEY (platform_id) REFERENCES crowdlending_platform (id)');
        $this->addSql('ALTER TABLE crowdlending ADD CONSTRAINT FK_8B1F9F787E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crowdlending DROP FOREIGN KEY FK_8B1F9F78FFE6496F');
        $this->addSql('ALTER TABLE crowdlending DROP FOREIGN KEY FK_8B1F9F787E3C61F9');
        $this->addSql('DROP TABLE crowdlending');
        $this->addSql('DROP TABLE crowdlending_platform');
    }
}
