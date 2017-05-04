<?php

namespace Application\Migrations;

use AppBundle\Entity\Users;
use ChapmanRadio\Util;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170430182316 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE users RENAME TO user;');

        $this->addSql('ALTER TABLE user ADD confirmed TINYINT');
        $this->addSql('ALTER TABLE user ADD confirmation_token VARCHAR(30)');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(30)');

        $this->addSql('ALTER TABLE user DROP fname');
        $this->addSql('ALTER TABLE user DROP lname');
        $this->addSql('ALTER TABLE user DROP verifycode');

        $this->addSql('ALTER TABLE user MODIFY fbid BIGINT(20) unsigned');
        $this->addSql('ALTER TABLE user MODIFY phone VARCHAR(30)');
        $this->addSql('ALTER TABLE user MODIFY staffgroup VARCHAR(200)');
        $this->addSql('ALTER TABLE user MODIFY staffposition VARCHAR(200)');
        $this->addSql('ALTER TABLE user MODIFY staffemail VARCHAR(200)');
        $this->addSql('ALTER TABLE user MODIFY seasons VARCHAR(140);');
        $this->addSql('ALTER TABLE user MODIFY lastlogin DATETIME;');
        $this->addSql('ALTER TABLE user MODIFY djname VARCHAR(120);');
        $this->addSql('ALTER TABLE user MODIFY gender VARCHAR(100);');
        $this->addSql('ALTER TABLE user MODIFY lastip VARCHAR(30);');
        $this->addSql('ALTER TABLE user MODIFY password VARCHAR(255) NOT NULL;');
        $this->addSql('CREATE UNIQUE INDEX user_email_uindex ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX user_username_uindex ON user (username)');

        $this->addSql('UPDATE user set confirmed=1');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE user RENAME TO users;');

        $this->addSql('ALTER TABLE users DROP confirmed;');
        $this->addSql('ALTER TABLE users DROP confirmation_token;');
        $this->addSql('ALTER TABLE users DROP username;');

        $this->addSql('ALTER TABLE users ADD fname varchar(100) NOT NULL');
        $this->addSql('ALTER TABLE users ADD lname varchar(100) NOT NULL,');
        $this->addSql('ALTER TABLE users ADD verifycode varchar(30) NOT NULL,');

        $this->addSql('ALTER TABLE users MODIFY fbid BIGINT(20) unsigned NOT NULL');
        $this->addSql('ALTER TABLE users MODIFY phone VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE users MODIFY staffgroup VARCHAR(200) NOT NULL');
        $this->addSql('ALTER TABLE users MODIFY staffposition VARCHAR(200) NOT NULL');
        $this->addSql('ALTER TABLE users MODIFY staffemail VARCHAR(200) NOT NULL');
        $this->addSql('ALTER TABLE users MODIFY seasons VARCHAR(140) NOT NULL');
        $this->addSql('ALTER TABLE users MODIFY lastlogin DATETIME NOT NULL');
        $this->addSql('ALTER TABLE users MODIFY djname VARCHAR(120) NOT NULL');
        $this->addSql('ALTER TABLE users MODIFY gender VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE users MODIFY lastip VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE users MODIFY password VARCHAR(48) NOT NULL;');
        $this->addSql('ALTER TABLE users CHANGE id userid BIGINT(20) unsigned NOT NULL AUTO_INCREMENT;');


        $this->addSql('DROP INDEX users_email_uindex ON users;');
        $this->addSql('DROP INDEX users_username_uindex ON users;');

    }

    public function postUp(Schema $schema)
    {
        $batch_size = 20;
        $i = 0;

        $encoder = $this->container->get('security.password_encoder');

        $em =$this->container->get('doctrine')->getEntityManager();
        $repo = $em->getRepository('AppBundle:Users');
        $qb = $repo->createQueryBuilder('i');

        $it = $qb->getQuery()->iterate();
        foreach ($it as $row) {
            /** @var Users $ent */
            $ent = $row[0];

            echo $ent->getEmail() . "\n";


            $password = Util::decrypt($ent->getPassword());
            $p = $encoder->encodePassword($ent, $password);
            $ent->setPassword($p);
            if (($i % $batch_size) === 0) {
                $em->flush(); // Executes all updates.
                $em->clear(); // Detaches all objects from Doctrine!
            }
            ++$i;
        }
        $em->flush();

    }

}
