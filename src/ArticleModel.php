<?php
declare(strict_types=1);

namespace App;


class ArticleModel
{
    public int $article_id;
    public ?string $headline;
    public ?string $text;
    public float $created;
    public ?string $state;
    public float $last_modified;
    public ?string $format;
    public int $FK_image_id;
    public int $FK_articletag_id;
    public string $image_url;
}
