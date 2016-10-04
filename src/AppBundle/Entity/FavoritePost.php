<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FavoritePost
 *
 * @ORM\Table(name="favorite_post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FavoritePostRepository")
 */
class FavoritePost
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="string", length=255)
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="post_id", type="integer")
     */
    private $postId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="saved_at", type="datetime")
     */
    private $savedAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="faved")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     * private $post;
     **/


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return FavoritePost
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set postId
     *
     * @param integer $postId
     *
     * @return FavoritePost
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;

        return $this;
    }

    /**
     * Get postId
     *
     * @return int
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * Set savedAt
     *
     * @param \DateTime $savedAt
     *
     * @return FavoritePost
     */
    public function setSavedAt($savedAt)
    {
        $this->savedAt = $savedAt;

        return $this;
    }

    /**
     * Get savedAt
     *
     * @return \DateTime
     */
    public function getSavedAt()
    {
        return $this->savedAt;
    }
}

