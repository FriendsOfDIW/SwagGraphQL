<?php declare(strict_types=1);

namespace SwagGraphQL\Tests\_fixtures;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ManyToOneEntity extends EntityDefinition
{

    public static function getEntityName(): string
    {
        return 'many_to_one';
    }

    protected static function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->setFlags(new PrimaryKey(), new Required()),
            new OneToManyAssociationField(
                'association',
                AssociationEntity::class,
                'many_to_one_id'
            )
        ]);
    }
}
