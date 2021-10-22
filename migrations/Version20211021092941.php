<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211021092941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historique_quizz (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, user_id INT NOT NULL, score INT NOT NULL, INDEX IDX_32F74D4EBCF5E72D (categorie_id), INDEX IDX_32F74D4EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE historique_quizz ADD CONSTRAINT FK_32F74D4EBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE historique_quizz ADD CONSTRAINT FK_32F74D4EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE categorie CHANGE name name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE question ADD categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_B6F7494EBCF5E72D ON question (categorie_id)');
        $this->addSql('ALTER TABLE reponse ADD question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC71E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC71E27F6BF ON reponse (question_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historique_quizz DROP FOREIGN KEY FK_32F74D4EA76ED395');
        $this->addSql('DROP TABLE historique_quizz');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('ALTER TABLE categorie CHANGE name name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EBCF5E72D');
        $this->addSql('DROP INDEX IDX_B6F7494EBCF5E72D ON question');
        $this->addSql('ALTER TABLE question DROP categorie_id');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC71E27F6BF');
        $this->addSql('DROP INDEX IDX_5FB6DEC71E27F6BF ON reponse');
        $this->addSql('ALTER TABLE reponse DROP question_id');
    }
}
