<?php

namespace App\Security;

use App\Entity\Resource;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * It grants or denies permissions for actions related to blog posts (such as
 * showing, editing and deleting posts).
 *
 * See https://symfony.com/doc/current/security/voters.html
 */
class ResourceVoter extends Voter
{
    // Defining these constants is overkill for this simple application, but for real
    // applications, it's a recommended practice to avoid relying on "magic strings"
    private const SHOW = 'show';
    private const EDIT = 'edit';
    private const DELETE = 'delete';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        // this voter is only executed for three specific permissions on Resource objects
        return $subject instanceof Resource && \in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $resource, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny permission
        if (!$user instanceof User) {
            return false;
        }

        // the logic of this voter is pretty simple: if the logged user is the
        // author of the given resource, grant permission; otherwise, deny it.
        // (the supports() method guarantees that $post is a Resource object)
        return $user === $resource->getAuthor();
    }
}
