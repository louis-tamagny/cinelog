<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204165552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, movie_id INT DEFAULT NULL, comment_user_id INT DEFAULT NULL, note INT NOT NULL, message LONGTEXT NOT NULL, date DATE NOT NULL, INDEX IDX_9474526C8F93B6FC (movie_id), INDEX IDX_9474526C541DB185 (comment_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie (id INT AUTO_INCREMENT NOT NULL, tmdb_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE response (id INT AUTO_INCREMENT NOT NULL, comment_id INT DEFAULT NULL, user_response_id INT DEFAULT NULL, message LONGTEXT NOT NULL, date DATE NOT NULL, INDEX IDX_3E7B0BFBF8697D13 (comment_id), INDEX IDX_3E7B0BFB52E8E1D5 (user_response_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, birth_date DATE NOT NULL, email VARCHAR(255) NOT NULL, is_disabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE watch_later_movies (user_id INT NOT NULL, movie_id INT NOT NULL, INDEX IDX_6B14A42BA76ED395 (user_id), INDEX IDX_6B14A42B8F93B6FC (movie_id), PRIMARY KEY(user_id, movie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favourite_movies (user_id INT NOT NULL, movie_id INT NOT NULL, INDEX IDX_E6D01D02A76ED395 (user_id), INDEX IDX_E6D01D028F93B6FC (movie_id), PRIMARY KEY(user_id, movie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C541DB185 FOREIGN KEY (comment_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFBF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFB52E8E1D5 FOREIGN KEY (user_response_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE watch_later_movies ADD CONSTRAINT FK_6B14A42BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE watch_later_movies ADD CONSTRAINT FK_6B14A42B8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favourite_movies ADD CONSTRAINT FK_E6D01D02A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favourite_movies ADD CONSTRAINT FK_E6D01D028F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C8F93B6FC');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C541DB185');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFBF8697D13');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFB52E8E1D5');
        $this->addSql('ALTER TABLE watch_later_movies DROP FOREIGN KEY FK_6B14A42BA76ED395');
        $this->addSql('ALTER TABLE watch_later_movies DROP FOREIGN KEY FK_6B14A42B8F93B6FC');
        $this->addSql('ALTER TABLE favourite_movies DROP FOREIGN KEY FK_E6D01D02A76ED395');
        $this->addSql('ALTER TABLE favourite_movies DROP FOREIGN KEY FK_E6D01D028F93B6FC');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE watch_later_movies');
        $this->addSql('DROP TABLE favourite_movies');
    }
}
