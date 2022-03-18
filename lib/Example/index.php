<?php
/**
 * lib/Example/index.php
 */
namespace Addequatte\JsonSerializer\Example;


use Addequatte\JsonSerializer\Model\JsonSerializable;
use Addequatte\JsonSerializer\Processor\FieldProcessor;

require_once dirname(__DIR__) . '/../vendor/autoload.php';

class Author extends JsonSerializable
{
    private $id = 1;

    private $name = 'Author name';

    private $gender = 'male';

    private $birthDate;

    private $books;

    public function __construct()
    {
        $this->birthDate = new \DateTime('2000-01-12');

        $this->books[] = new Book();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDate(): \DateTime
    {
        return $this->birthDate;
    }

    /**
     * @return array
     */
    public function getBooks(): array
    {
        return $this->books;
    }
}

class Book extends JsonSerializable
{
    private $id = 1;

    private $name = 'Book Name';

    private $description = 'Book description';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}

$author = new Author();

print_r(json_encode($author, JSON_PRETTY_PRINT) . PHP_EOL);

$processor = new FieldProcessor();

$processor->addClosure(Author::class,'birthDate', function ($value) {
    return $value->format('d.m.Y H:i:s');
});

$processor->addClosure(Book::class,'name', function ($value) {
    return 'Processed book name';
});

$jsonSerializeHandler = new \Addequatte\JsonSerializer\Handlers\JsonSerializeHandler($processor);

$jsonSerializeHandler->addHiddenFields(Author::class, ['gender']);
$jsonSerializeHandler->addHiddenFields(Book::class, ['description']);


print_r(json_encode($jsonSerializeHandler->jsonSerialize($author), JSON_PRETTY_PRINT));
