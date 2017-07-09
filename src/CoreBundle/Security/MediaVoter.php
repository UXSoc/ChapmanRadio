<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/8/17
 * Time: 11:19 PM
 */

namespace CoreBundle\Security;


use CoreBundle\Entity\Media;
use CoreBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MediaVoter extends  Voter
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

        if(!$subject instanceof Media)
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


        /** @var Media $post */
        $media = $subject;

        switch ($attribute){
            case self::VIEW:
                return $this->canView($media,$user);
            case self::EDIT:
                return $this->canEdit($token,$media,$user);
            case self::DELETE:
                return $this->canDelete($media,$user);
        }


        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Media $media,User $user)
    {
        return true;
    }

    private  function canEdit($token,Media $media,User $user)
    {
        if($media->getAuthor() === $user)
            return true;
        return false;
    }

    private function canDelete(Media $media,User $user)
    {
        return false;
    }
}