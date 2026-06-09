<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260607191001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE formula (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, base_price INT NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quote (id INT AUTO_INCREMENT NOT NULL, duration SMALLINT NOT NULL, created_at DATETIME NOT NULL, expired_at DATETIME NOT NULL, status VARCHAR(50) NOT NULL, license_year SMALLINT NOT NULL, birth_date DATE NOT NULL, estimated_price DOUBLE PRECISION NOT NULL, bonus_malus NUMERIC(5, 2) NOT NULL, user_id INT NOT NULL, formula_id INT NOT NULL, vehicle_id INT NOT NULL, INDEX IDX_6B71CBF4A76ED395 (user_id), INDEX IDX_6B71CBF4A50A6386 (formula_id), INDEX IDX_6B71CBF4545317D1 (vehicle_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE vehicle (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, brand VARCHAR(50) NOT NULL, model VARCHAR(40) NOT NULL, license_plate VARCHAR(20) NOT NULL, vehicle_year SMALLINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4A50A6386 FOREIGN KEY (formula_id) REFERENCES formula (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4A76ED395');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4A50A6386');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4545317D1');
        $this->addSql('DROP TABLE formula');
        $this->addSql('DROP TABLE quote');
        $this->addSql('DROP TABLE vehicle');
    }
}
