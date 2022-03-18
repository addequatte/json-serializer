### Simply JsonSerializable interface realization to convert models to json

## Features
* Convert children models to json.
* Hide fields you want.
* Process result field

## Installation
 ```bash	
 composer require addequatte/json-serializer
 ```

## For example

```php
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
```
## Result for first case
```json
{
  "id": 1,
  "name": "Author name",
  "gender": "male",
  "birthDate": {
    "date": "2000-01-12 00:00:00.000000",
    "timezone_type": 3,
    "timezone": "Asia\/Krasnoyarsk"
  },
  "books": [
    {
      "id": 1,
      "name": "Book Name",
      "description": "Book description"
    }
  ]
}
```

## Result for second case
```json
{
  "id": 1,
  "name": "Author name",
  "birthDate": "12.01.2000 00:00:00",
  "books": [
    {
      "id": 1,
      "name": "Processed book name"
    }
  ]
}

```
* you can get all properties your models having getter
* How you can see it is pretty simple to use, just extend your model using
**Addequatte\JsonSerializer\Model\JsonSerializable**.
* If you want to hide some fields you can use **setHiddenFields(array $hiddenFields): void** method
* You can hide fields from children model easily just using **$jsonSerializeHandler->addHiddenFields(Author::class, ['gender'])**
* You can change model fields easily just using **$processor->addClosure(Author::class,'birthDate', function ($value) {
  return $value->format('d.m.Y H:i:s');
  })**
* You can write your own processor implement **ProcessorInterface**