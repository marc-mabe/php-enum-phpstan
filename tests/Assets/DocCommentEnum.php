<?php

declare(strict_types=1);

namespace MabeEnumPHPStanTest\Assets;

use MabeEnum\Enum;

class DocCommentEnum extends Enum
{
    /**
     * With doc block
     *
     * @var string
     */
    const WITH_DOC_BLOCK = 'with doc block';

    const WITHOUT_DOC_BLOCK = 'without doc block';
}
