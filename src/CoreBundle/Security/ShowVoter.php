<?php
namespace CoreBundle\Security;

use CoreBundle\Entity\Comment;
use CoreBundle\Entity\Post;
use CoreBundle\Entity\Show;
use CoreBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ShowVoter extends  Voter
{
    const EDIT = 'edit';
    const VIEW = 'view';

    private $decisionManager;

    function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }

        if(!$subject instanceof Post)
        {
            return false;
        }
        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token)
    {
        $user = $token->getUser();
        if(!$user instanceof User) {
            //user must be logged in
            return false;
        }

        if ($this->decisionManager->decide($token, array('ROLE_STAFF'))) {
            return true;
        }

        /** @var Show $show */
        $show = $subject;

        switch ($attribute){
            case self::VIEW:
                return $this->canView($show,$user);
            case self::EDIT:
                return $this->canEdit($token,$show,$user);
        }


        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Show $show,User $user)
    {
        return true;
    }

    private  function canEdit($token,Show $show,User $user)
    {
        if(!$user->isDj())
            return false;
        return $show->getDjs()->contains($user->getDj());
    }
}