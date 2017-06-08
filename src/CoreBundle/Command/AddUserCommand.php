<?php
namespace CoreBundle\Command;

use CoreBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/7/17
 * Time: 11:49 PM
 */
class AddUserCommand extends Command
{
    const MAX_ATTEMPTS = 5;
    private $entityManager;
    private $passwordEncoder;
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            // a good practice is to use the 'app:' prefix to group all your custom application commands
            ->setName('chapman-radio:add-user')
            ->setDescription('Creates users and stores them in the database')
            ->setHelp($this->getCommandHelp())
            // commands can optionally define arguments and/or options (mandatory and optional)
            // see https://symfony.com/doc/current/components/console/console_arguments.html
            ->addArgument('username', InputArgument::OPTIONAL, 'The username of the new user')
            ->addArgument('password', InputArgument::OPTIONAL, 'The plain password of the new user')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the new user')
            ->addArgument('full-name', InputArgument::OPTIONAL, 'The full name of the new user');
    }
    /**
     * This method is executed after initialize() and before execute(). Its purpose
     * is to check if some of the options/arguments are missing and interactively
     * ask the user for those values.
     *
     * This method is completely optional. If you are developing an internal console
     * command, you probably should not implement this method because it requires
     * quite a lot of work. However, if the command is meant to be used by external
     * users, this method is a nice way to fall back and prevent errors.
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('username') && null !== $input->getArgument('password') && null !== $input->getArgument('email') && null !== $input->getArgument('full-name')) {
            return;
        }
        // multi-line messages can be displayed this way...
        $output->writeln('');
        $output->writeln('Add User Command Interactive Wizard');
        $output->writeln('-----------------------------------');
        // ...but you can also pass an array of strings to the writeln() method
        $output->writeln([
            '',
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:add-user username password email@example.com',
            '',
        ]);
        $output->writeln([
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
            '',
        ]);
        // See https://symfony.com/doc/current/components/console/helpers/questionhelper.html
        $console = $this->getHelper('question');
        // Ask for the username if it's not defined
        $username = $input->getArgument('username');
        if (null === $username) {
            $question = new Question(' > <info>Username</info>: ');
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new \RuntimeException('The username cannot be empty');
                }
                return $answer;
            });
            $question->setMaxAttempts(self::MAX_ATTEMPTS);
            $username = $console->ask($input, $output, $question);
            $input->setArgument('username', $username);
        } else {
            $output->writeln(' > <info>Username</info>: '.$username);
        }
        // Ask for the password if it's not defined
        $password = $input->getArgument('password');
        if (null === $password) {
            $question = new Question(' > <info>Password</info> (your type will be hidden): ');
            $question->setValidator([$this, 'passwordValidator']);
            $question->setHidden(true);
            $question->setMaxAttempts(self::MAX_ATTEMPTS);
            $password = $console->ask($input, $output, $question);
            $input->setArgument('password', $password);
        } else {
            $output->writeln(' > <info>Password</info>: '.str_repeat('*', mb_strlen($password)));
        }
        // Ask for the email if it's not defined
        $email = $input->getArgument('email');
        if (null === $email) {
            $question = new Question(' > <info>Email</info>: ');
            $question->setValidator([$this, 'emailValidator']);
            $question->setMaxAttempts(self::MAX_ATTEMPTS);
            $email = $console->ask($input, $output, $question);
            $input->setArgument('email', $email);
        } else {
            $output->writeln(' > <info>Email</info>: '.$email);
        }
        // Ask for the full name if it's not defined
        $fullName = $input->getArgument('full-name');
        if (null === $fullName) {
            $question = new Question(' > <info>Full Name</info>: ');
            $question->setValidator([$this, 'fullNameValidator']);
            $question->setMaxAttempts(self::MAX_ATTEMPTS);
            $fullName = $console->ask($input, $output, $question);
            $input->setArgument('full-name', $fullName);
        } else {
            $output->writeln(' > <info>Full Name</info>: '.$fullName);
        }
    }
    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);
        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('password');
        $email = $input->getArgument('email');
        $fullName = $input->getArgument('full-name');
        // make sure to validate the user data is correct
        $this->validateUserData($username, $plainPassword, $email, $fullName);
        // create the user and encode its password
        $user = new User();
        $user->setName($fullName);
        $user->setUsername($username);
        $user->setEmail($email);
        // See https://symfony.com/doc/current/book/security.html#security-encoding-password
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $output->writeln('');
        $output->writeln(sprintf('[OK] %s was successfully created: %s (%s)', 'User', $user->getUsername(), $user->getEmail()));
        if ($output->isVerbose()) {
            $finishTime = microtime(true);
            $elapsedTime = $finishTime - $startTime;
            $output->writeln(sprintf('[INFO] New user database id: %d / Elapsed time: %.2f ms', $user->getId(), $elapsedTime * 1000));
        }
    }
    /**
     * @internal
     */
    public function passwordValidator($plainPassword)
    {
        if (empty($plainPassword)) {
            throw new \Exception('The password can not be empty.');
        }
        if (mb_strlen(trim($plainPassword)) < 6) {
            throw new \Exception('The password must be at least 6 characters long.');
        }
        return $plainPassword;
    }
    /**
     * @internal
     */
    public function emailValidator($email)
    {
        if (empty($email)) {
            throw new \Exception('The email can not be empty.');
        }
        if (false === mb_strpos($email, '@')) {
            throw new \Exception('The email should look like a real email.');
        }
        return $email;
    }
    /**
     * @internal
     */
    public function fullNameValidator($fullName)
    {
        if (empty($fullName)) {
            throw new \Exception('The full name can not be empty.');
        }
        return $fullName;
    }
    private function validateUserData($username, $plainPassword, $email, $fullName)
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        // first check if a user with the same username already exists.
        $existingUser = $userRepository->findOneBy(['username' => $username]);
        if (null !== $existingUser) {
            throw new \RuntimeException(sprintf('There is already a user registered with the "%s" username.', $username));
        }
        // validate password and email if is not this input means interactive.
        $this->passwordValidator($plainPassword);
        $this->emailValidator($email);
        $this->fullNameValidator($fullName);
        // check if a user with the same email already exists.
        $existingEmail = $userRepository->findOneBy(['email' => $email]);
        if (null !== $existingEmail) {
            throw new \RuntimeException(sprintf('There is already a user registered with the "%s" email.', $email));
        }
    }
    /**
     * The command help is usually included in the configure() method, but when
     * it's too long, it's better to define a separate method to maintain the
     * code readability.
     */
    private function getCommandHelp()
    {
        return <<<'HELP'
The <info>%command.name%</info> command creates new users and saves them in the database:
  <info>php %command.full_name%</info> <comment>username password email</comment>
By default the command creates regular users. 
  <info>php %command.full_name%</info> username password email
If you omit any of the three required arguments, the command will ask you to
provide the missing values:
  # command will ask you for the email
  <info>php %command.full_name%</info> <comment>username password</comment>
  # command will ask you for the email and password
  <info>php %command.full_name%</info> <comment>username</comment>
  # command will ask you for all arguments
  <info>php %command.full_name%</info>
HELP;
    }
}