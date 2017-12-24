<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class DoctrineStorage implements StorageInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function save($fixture)
    {
        $metadata = $this->manager->getClassMetadata(get_class($fixture));
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        if ($metadata->isEmbeddedClass) {
            return;
        }

        $this->manager->persist($fixture);
        $this->manager->flush();
        $this->manager->refresh($fixture);
    }
}
