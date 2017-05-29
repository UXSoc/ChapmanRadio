<?php

trait UserDataFixture
{
    /**
     * @param \Faker\Generator $faker
     * @return \CoreBundle\Entity\User
     */
    protected function generateUser($faker)
    {
        $user = new \CoreBundle\Entity\User();
        $user->setName($faker->unique()->name);
        $user->setEmail($faker->unique()->email);
        $user->setStudentId($faker->numerify("#########"));
        $user->updateLastLogin();
        $user->setConfirmed(true);
        $user->setUsername($faker->userName);

        $password = $this->container->get('security.password_encoder')
            ->encodePassword($user, 'password');
        $user->setPassword($password);
        $user->setPhone($faker->phoneNumber);

        return $user;
    }
}