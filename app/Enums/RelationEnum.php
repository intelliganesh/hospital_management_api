<?php
namespace App\Enums;

enum RelationEnum: string {
    case AUNT        = 'Aunt';
    case UNCLE       = 'Uncle';
    case OTHER       = 'Other';
    case CHILD       = 'Child';
    case MOTHER      = 'Mother';
    case FATHER      = 'Father';
    case SPOUSE      = 'Spouse';
    case SISTER      = 'Sister';
    case COUSIN      = 'Cousin';
    case BROTHER     = 'Brother';
    case SIBLING     = 'Sibling';
    case GRANDPARENT = 'Grandparent';
}
