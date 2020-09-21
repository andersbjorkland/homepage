<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200918095307 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog_image (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, blog_post_id INT DEFAULT NULL, ordering INT NOT NULL, subtext LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_35D247973DA5256D (image_id), INDEX IDX_35D24797A77FBEAF (blog_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_post (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, entered DATETIME NOT NULL, modified DATETIME DEFAULT NULL, publish_time DATETIME DEFAULT NULL, is_releasable TINYINT(1) NOT NULL, INDEX IDX_BA5AE01DA76ED395 (user_id), INDEX IDX_BA5AE01D727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, alt VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE portfolio_entry (id INT AUTO_INCREMENT NOT NULL, tech_id INT DEFAULT NULL, image_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, text_description VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, filename VARCHAR(255) DEFAULT NULL, INDEX IDX_7679B73564727BFC (tech_id), INDEX IDX_7679B7353DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE run (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, date DATE NOT NULL, weight_pre DOUBLE PRECISION NOT NULL, weight_post DOUBLE PRECISION NOT NULL, weight_diff DOUBLE PRECISION NOT NULL, distance DOUBLE PRECISION NOT NULL, time TIME NOT NULL, INDEX IDX_5076A4C0C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE run_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tech (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_image ADD CONSTRAINT FK_35D247973DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE blog_image ADD CONSTRAINT FK_35D24797A77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01D727ACA70 FOREIGN KEY (parent_id) REFERENCES blog_post (id)');
        $this->addSql('ALTER TABLE portfolio_entry ADD CONSTRAINT FK_7679B73564727BFC FOREIGN KEY (tech_id) REFERENCES tech (id)');
        $this->addSql('ALTER TABLE portfolio_entry ADD CONSTRAINT FK_7679B7353DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE run ADD CONSTRAINT FK_5076A4C0C54C8C93 FOREIGN KEY (type_id) REFERENCES run_type (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blog_image DROP FOREIGN KEY FK_35D24797A77FBEAF');
        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA5AE01D727ACA70');
        $this->addSql('ALTER TABLE blog_image DROP FOREIGN KEY FK_35D247973DA5256D');
        $this->addSql('ALTER TABLE portfolio_entry DROP FOREIGN KEY FK_7679B7353DA5256D');
        $this->addSql('ALTER TABLE run DROP FOREIGN KEY FK_5076A4C0C54C8C93');
        $this->addSql('ALTER TABLE portfolio_entry DROP FOREIGN KEY FK_7679B73564727BFC');
        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA5AE01DA76ED395');
        $this->addSql('DROP TABLE blog_image');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE portfolio_entry');
        $this->addSql('DROP TABLE run');
        $this->addSql('DROP TABLE run_type');
        $this->addSql('DROP TABLE tech');
        $this->addSql('DROP TABLE user');
    }
}
