<?php
declare(strict_types=1);

namespace App;

use \DateTime;

use PDO;
use PDOStatement;
// Handles communication with DB. Has all functions(, , etc)
class FNService
{
    private PDO $pdo;

    /**
     * CounterService constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->createDatabaseTable();
    }

    /**
     * @param string $query
     * @return bool|PDOStatement
     */
    private function prepare(string $query)
    {
        return $this->pdo->prepare($query);
    }

    public function getArticles(): array
    {
        $query = "select * from article ORDER BY created DESC";
        $statement = $this->prepare($query);
        $statement->execute();

        $articles = array();
        while ($entry = $statement->fetchObject(ArticleModel::class)) {
            $articles[] = $entry;
        }
        return $articles;
    }

    public function editArticle(int $article_id, string $headline, string $text): ?ArticleModel
    {
        $query = "update article set headline=:headline, text=:text where article_id=:article_id";
        $statement = $this->prepare($query);
        $statement->bindParam(':headline', $headline);
        $statement->bindParam(':text', $text);
        $statement->bindParam(':article_id', $article_id);
        $statement->execute();

        return $this->getArticle((int)$article_id);
    }

    public function getArticle(int $article_id): ?ArticleModel
    {
        $query = "select * from article where article_id=:article_id";
        $statement = $this->prepare($query);
        $statement->bindParam(':article_id', $article_id);
        $statement->execute();
        return $statement->fetchObject(ArticleModel::class) ?: null;
    }
    public function deleteArticle(int $article_id): ?ArticleModel
    {
        $query = "delete from article where article_id=:article_id";
        $statement = $this->prepare($query);
        $statement->bindParam(':article_id', $article_id);
        $statement->execute();
        return null;
    }

    private function createSlug(string $headline){
        // replace non letter or digits by -
        $headline = preg_replace('~[^\pL\d]+~u', '-', $headline);

        // transliterate
        $headline = iconv('utf-8', 'us-ascii//TRANSLIT', $headline);

        // remove unwanted characters
        $headline = preg_replace('~[^-\w]+~', '', $headline);

        // trim
        $headline = trim($headline, '-');

        // remove duplicate -
        $headline = preg_replace('~-+~', '-', $headline);

        // lowercase
        $headline = strtolower($headline);

        if (empty($headline)) {
            return 'n-a';
        }
        return "/".$headline."/";
    }
    private function createExcerpt(string $text){
        $limit = 20;
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos   = array_keys($words);
            $text  = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }
    public function createArticle(string $headline, string $text, string $image): ArticleModel{
        $timestamp_now = (new DateTime())->getTimestamp();
        $created = $timestamp_now;
        $last_modified = $timestamp_now;
        $state = "ACTIVE";
        $format = "PLAIN";
        $FK_articletag_id = -1;
        $FK_image_id = -1;
        $slug = $this->createSlug($headline);
        $excerpt = $this->createExcerpt($text);
        $query = "insert into article (headline, text, created, state, last_modified, format, FK_image_id, FK_articletag_id, image_url, slug, excerpt) values (:headline, :text, :created, :state, :last_modified, :format, :FK_image_id, :FK_articletag_id, :image_url, :slug, :excerpt);";
        $statement = $this->prepare($query);
        $statement->bindParam(':headline', $headline);
        $statement->bindParam(':text', $text);
        $statement->bindParam(':created', $created);
        $statement->bindParam(':state', $state);
        $statement->bindParam(':last_modified', $last_modified);
        $statement->bindParam(':format', $format);
        $statement->bindParam(':FK_image_id', $FK_image_id);
        $statement->bindParam(':FK_articletag_id', $FK_articletag_id);
        $statement->bindParam(':image_url', $image);
        $statement->bindParam(':slug', $slug);
        $statement->bindParam(':excerpt', $excerpt);
        $statement->execute();

        $id = (int)$this->pdo->lastInsertId();
        return $this->getArticle($id);
    }

    public function createDatabaseTable(): void
    {
        $ddl = <<<EOF
        create table IF NOT EXISTS article
        (
            article_id int auto_increment
            primary key,
            headline varchar(255),
            text longtext,
            created_date varchar(255),
            state varchar(255),
            last_modified varchar(255),
            format varchar(45),
            FK_image_id int
        );
EOF;
        $this->pdo->exec($ddl);
    }
}

/*Un-used functions*/

    // private function getImageURL($image_id){

    //     $query = "select url from  image where image_id = :image_id;";
    //     $statement = $this->prepare($query);
    //     $statement->bindParam(':image_id', $image_id);
    //     $statement->execute();
    //     $url = $statement->fetchColumn(0);
    //     return $url;



    // }

    // private function createImage($image){
    //     $timestamp_now = (new DateTime())->getTimestamp();
    //     $created = $timestamp_now;
    //     $alt_text = "";
    //     $width = 0;
    //     $height = 0;
    //     $query = "insert into image (url, alt_text, created_date, width, height) values (:url, :alt_text, :created_date, :width, :height);";
    //     $statement = $this->prepare($query);
    //     $statement->bindParam(':url', $url);
    //     $statement->bindParam(':alt_text', $alt_text);
    //     $statement->bindParam(':created_date', $created_date);
    //     $statement->bindParam(':width', $width);
    //     $statement->bindParam(':height', $height);
    //     $statement->execute();
    //     $id = (int)$this->pdo->lastInsertId();
    //     return $id;
    // }
