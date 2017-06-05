<?php
namespace CoreBundle\Security;

use CoreBundle\Entity\Post;
use CoreBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends  Voter
{

    const EDIT = 'edit';
    const VIEW = 'view';
    const DELETE = 'delete';

    private $decisionManager;

    /**
     * PostVoter constructor.
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
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
        if (!in_array($attribute, array(self::VIEW, self::EDIT,self::DELETE))) {
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


        /** @var Post $post */
        $post = $subject;

        switch ($attribute){
            case self::VIEW:
                return $this->canView($post,$user);
            case self::EDIT:
                return $this->canEdit($token,$post,$user);
            case self::DELETE:
                return $this->canDelete($post,$user);
        }


        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Post $post,User $user)
    {
        return true;
    }

    private  function canEdit($token,Post $post,User $user)
    {
        if($post->getAuthor() == $user)
            return true;
        return false;
    }

    private function canDelete(Post $post,User $user)
    {
        return false;
    }
}
