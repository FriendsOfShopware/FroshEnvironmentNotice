<?php declare(strict_types=1);

namespace FroshEnvironmentNotice\Services;

class NoticeMarkupBuilder
{
    /**
     * @param array  $styleRules
     * @param string $message
     *
     * @return string
     */
    public function buildNotice(array $styleRules, string $message): string
    {
        return "<div style=\"{$this->buildStyleRules($styleRules)}\">{$message}</div>";
    }

    /**
     * @param array $styleRules
     *
     * @return string
     */
    public function buildStyleRules(array $styleRules): string
    {
        return join(';', array_map([$this, 'buildStyleSetter'], array_keys($styleRules), $styleRules));
    }

    /**
     * @param string $property
     * @param string $value
     *
     * @return string
     */
    public function buildStyleSetter(string $property, string $value): string
    {
        return "{$property}: {$value}";
    }
}
