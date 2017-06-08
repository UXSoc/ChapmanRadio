<?php
namespace CoreBundle\Command;

use CoreBundle\Entity\User;
use CoreBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/7/17
 * Time: 11:55 PM
 */
class DeleteUserCommand extends  Command
{
    const MAX_ATTEMPTS = 5;
    private $entityManager;
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->entityManager = $em;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('chapman-radio:delete-user')
            ->setDescription('Deletes users from the database')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of an existing user')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command deletes users from the database:
  <info>php %command.full_name%</info> <comment>username</comment>
If you omit the argument, the command will ask you to
provide the missing value:
  <info>php %command.full_name%</info>
HELP
            );
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('username')) {
            return;
        }
        $output->writeln('');
        $output->writeln('Delete User Command Interactive Wizard');
        $output->writeln('-----------------------------------');
        $output->writeln([
            '',
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:delete-user username',
            '',
        ]);
        $output->writeln([
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
            '',
        ]);
        $helper = $this->getHelper('question');
        $question = new Question(' > <info>Username</info>: ');
        $question->setValidator([$this, 'usernameValidator']);
        $question->setMaxAttempts(self::MAX_ATTEMPTS);
        $username = $helper->ask($input, $output, $question);
        $input->setArgument('username', $username);
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $this->usernameValidator($username);
        /** @var UserRepository $repository */
        $repository = $this->entityManager->getRepository(User::class);
        /** @var User $user */
        $user = $repository->findOneByUsernameOrEmail($username);
        if (null === $user) {
            throw new \RuntimeException(sprintf('User with username "%s" not found.', $username));
        }
        // After an entity has been removed its in-memory state is the same
        // as before the removal, except for generated identifiers.
        // See http://docs.doctrine-project.org/en/latest/reference/working-with-objects.html#removing-entities
        $userId = $user->getId();
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $output->writeln('');
        $output->writeln(sprintf('[OK] User "%s" (ID: %d, email: %s) was successfully deleted.', $user->getUsername(), $userId, $user->getEmail()));
    }
    /**
     * This internal method should be private, but it's declared public to
     * maintain PHP 5.3 compatibility when using it in a callback.
     *
     * @internal
     */
    public function usernameValidator($username)
    {
        if (empty($username)) {
            throw new \Exception('The username can not be empty.');
        }
        if (1 !== preg_match('/^[a-z_]+$/', $username)) {
            throw new \Exception('The username must contain only lowercase latin characters and underscores.');
        }
        return $username;
    }

}