<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Services;

class ModifyHtmlText
{
    /**
     * @param string $tag
     * @param string $contentToInsert
     * @param string $subject
     *
     * @return string
     */
    public function insertAfterTag(string $tag, string $contentToInsert, string $subject): string
    {
        return str_replace("</{$tag}>", "{$contentToInsert}</{$tag}>", $subject);
    }
}
