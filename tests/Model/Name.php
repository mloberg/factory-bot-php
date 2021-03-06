<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Test\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Name
{
    /**
     * @ORM\Column(nullable=true)
     *
     * @var string
     */
    private $first;

    /**
     * @ORM\Column(nullable=true)
     *
     * @var string
     */
    private $last;

    /**
     * Get First
     *
     * @return string
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Get Last
     *
     * @return string
     */
    public function getLast()
    {
        return $this->last;
    }
}
