<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200919170758 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog_post_category (blog_post_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_CA275A0CA77FBEAF (blog_post_id), INDEX IDX_CA275A0C12469DE2 (category_id), PRIMARY KEY(blog_post_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_post_category ADD CONSTRAINT FK_CA275A0CA77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_post_category ADD CONSTRAINT FK_CA275A0C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA5AE01D727ACA70');
        $this->addSql('DROP INDEX IDX_BA5AE01D727ACA70 ON blog_post');
        $this->addSql('ALTER TABLE blog_post DROP parent_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blog_post_category DROP FOREIGN KEY FK_CA275A0C12469DE2');
        $this->addSql('DROP TABLE blog_post_category');
        $this->addSql('DROP TABLE category');
        $this->addSql('ALTER TABLE blog_post ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01D727ACA70 FOREIGN KEY (parent_id) REFERENCES blog_post (id)');
        $this->addSql('CREATE INDEX IDX_BA5AE01D727ACA70 ON blog_post (parent_id)');
    }
}
