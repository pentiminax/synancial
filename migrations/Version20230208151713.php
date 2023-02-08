<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230208151713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, connection_id INT NOT NULL, type_id INT NOT NULL, INDEX IDX_7D3656A4A76ED395 (user_id), UNIQUE INDEX UNIQ_7D3656A4DD03F01 (connection_id), INDEX IDX_7D3656A4C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE account_type (id INT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, is_invest TINYINT(1) NOT NULL, product VARCHAR(50) NOT NULL, INDEX IDX_4DD083727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE connection (id INT NOT NULL, connector_id INT NOT NULL, user_id INT DEFAULT NULL, last_update DATETIME DEFAULT NULL, INDEX IDX_29F773664D085745 (connector_id), INDEX IDX_29F77366A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE connector (id INT NOT NULL, uuid VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, hidden TINYINT(1) DEFAULT NULL, charged TINYINT(1) NOT NULL, code VARCHAR(100) DEFAULT NULL, beta TINYINT(1) NOT NULL, color VARCHAR(6) DEFAULT NULL, slug VARCHAR(4) DEFAULT NULL, sync_frequency INT DEFAULT NULL, months_to_fetch INT DEFAULT NULL, siret VARCHAR(14) DEFAULT NULL, restricted TINYINT(1) NOT NULL, products LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(3) NOT NULL, name VARCHAR(10) NOT NULL, sign VARCHAR(3) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dividend (id INT AUTO_INCREMENT NOT NULL, symbol_id INT NOT NULL, ex_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', pay_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', amount DOUBLE PRECISION DEFAULT NULL, currency VARCHAR(100) DEFAULT NULL, INDEX IDX_2D0D0909C0F75674 (symbol_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(2) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE symbol (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, symbol VARCHAR(255) NOT NULL, isin VARCHAR(255) NOT NULL, dividend_frequency VARCHAR(255) NOT NULL, dividend_currency VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE time_serie (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, id_account INT NOT NULL, value NUMERIC(10, 2) NOT NULL, date DATE NOT NULL, INDEX IDX_EF487248A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, language_id INT NOT NULL, currency_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, bearer_token VARCHAR(128) DEFAULT NULL, last_sync DATETIME DEFAULT NULL, is_secret_mode_enabled TINYINT(1) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D64982F1BAF4 (language_id), INDEX IDX_8D93D64938248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4DD03F01 FOREIGN KEY (connection_id) REFERENCES connection (id)');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4C54C8C93 FOREIGN KEY (type_id) REFERENCES account_type (id)');
        $this->addSql('ALTER TABLE account_type ADD CONSTRAINT FK_4DD083727ACA70 FOREIGN KEY (parent_id) REFERENCES account_type (id)');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F773664D085745 FOREIGN KEY (connector_id) REFERENCES connector (id)');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F77366A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE dividend ADD CONSTRAINT FK_2D0D0909C0F75674 FOREIGN KEY (symbol_id) REFERENCES symbol (id)');
        $this->addSql('ALTER TABLE time_serie ADD CONSTRAINT FK_EF487248A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64982F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64938248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');

        $this->addSql('INSERT INTO currency VALUES (1, "EUR", "Euro", "€"), (2, "USD", "US Dollar", "$")');
        $this->addSql('INSERT INTO language VALUES (1, "EN", "English"), (2, "FR", "Français")');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A4A76ED395');
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A4DD03F01');
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A4C54C8C93');
        $this->addSql('ALTER TABLE account_type DROP FOREIGN KEY FK_4DD083727ACA70');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F773664D085745');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F77366A76ED395');
        $this->addSql('ALTER TABLE dividend DROP FOREIGN KEY FK_2D0D0909C0F75674');
        $this->addSql('ALTER TABLE time_serie DROP FOREIGN KEY FK_EF487248A76ED395');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64982F1BAF4');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64938248176');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE account_type');
        $this->addSql('DROP TABLE connection');
        $this->addSql('DROP TABLE connector');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE dividend');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE symbol');
        $this->addSql('DROP TABLE time_serie');
        $this->addSql('DROP TABLE `user`');
    }
}
