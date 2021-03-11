<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210311161740 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, questions_evaluation LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', nom_evaluation VARCHAR(30) NOT NULL, date_evaluation DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matiere (id INT AUTO_INCREMENT NOT NULL, libelle_matiere VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, type_question_id INT DEFAULT NULL, thematique_question_id INT DEFAULT NULL, matiere_question_id INT DEFAULT NULL, contenu_question VARCHAR(255) NOT NULL, propositions_question LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', reponses_question LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_B6F7494E553E212E (type_question_id), INDEX IDX_B6F7494E3BBB1A0 (thematique_question_id), INDEX IDX_B6F7494E2F9BB0DB (matiere_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_evaluation (question_id INT NOT NULL, evaluation_id INT NOT NULL, INDEX IDX_74F73AFD1E27F6BF (question_id), INDEX IDX_74F73AFD456C5646 (evaluation_id), PRIMARY KEY(question_id, evaluation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thematique (id INT AUTO_INCREMENT NOT NULL, libelle_thematique VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_question (id INT AUTO_INCREMENT NOT NULL, libelle_type VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E553E212E FOREIGN KEY (type_question_id) REFERENCES type_question (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E3BBB1A0 FOREIGN KEY (thematique_question_id) REFERENCES thematique (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E2F9BB0DB FOREIGN KEY (matiere_question_id) REFERENCES matiere (id)');
        $this->addSql('ALTER TABLE question_evaluation ADD CONSTRAINT FK_74F73AFD1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question_evaluation ADD CONSTRAINT FK_74F73AFD456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question_evaluation DROP FOREIGN KEY FK_74F73AFD456C5646');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E2F9BB0DB');
        $this->addSql('ALTER TABLE question_evaluation DROP FOREIGN KEY FK_74F73AFD1E27F6BF');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E3BBB1A0');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E553E212E');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE question_evaluation');
        $this->addSql('DROP TABLE thematique');
        $this->addSql('DROP TABLE type_question');
    }
}
