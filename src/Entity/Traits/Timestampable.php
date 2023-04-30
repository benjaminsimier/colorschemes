<?php

namespace App\Entity\Traits;

use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableMethodsTrait;
use Symfony\Component\Serializer\Annotation\Groups;

trait Timestampable
{
    use TimestampableMethodsTrait;

    /**
     * @var DateTimeInterface
     */
    #[Groups([
        'color:item',
        'color:collection'
    ])]
    protected $createdAt;

    /**
     * @var DateTimeInterface
     */
    #[Groups([
        'color:item',
        'color:collection'
    ])]
    protected $updatedAt;
}
