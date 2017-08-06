<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use AppBundle\Entity\PersonalInfo;
use AppBundle\Entity\Post;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="user")
     */
    private $posts;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\PersonalInfo", inversedBy="user")
     */
    private $personalInfo;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Add post
     *
     * @param Post $post
     *
     * @return User
     */
    public function addPost(Post $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param Post $post
     */
    public function removePost(Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set personalInfo
     *
     * @param PersonalInfo $personalInfo
     *
     * @return User
     */
    public function setPersonalInfo(PersonalInfo $personalInfo = null)
    {
        $this->personalInfo = $personalInfo;

        return $this;
    }

    /**
     * Get personalInfo
     *
     * @return PersonalInfo
     */
    public function getPersonalInfo()
    {
        return $this->personalInfo;
    }
}
