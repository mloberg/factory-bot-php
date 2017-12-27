<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Test\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(unique=true)
     *
     * @var string
     */
    private $username;

    /**
     * @ORM\Column()
     *
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(unique=true)
     *
     * @var string
     */
    private $email;

    /**
     * @ORM\Embedded(class="Mlo\FactoryBot\Test\Model\Name", columnPrefix="name_")
     *
     * @var Name
     */
    private $name;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = new Name();
    }

    /**
     * Get Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get Password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get Name
     *
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }
}
