Request converter
=================

*Sorry, there's no catchy name :P*

Convert API requests from plain PHP-arrays to request objects.

###### Example

In your controller action you can have: 
```php
public function addBlogPostAction(PostRequest $post)
{
    $this->commandBus->execute(new NewPost($post));
}
```

Request will automatically be converted from plain array form to the
`PostRequest` class instance and validated:
```php

use RequestConverter\Annotation\Type;
use RequestConverter\Annotation\Optional;
use RequestConverter\Annotation\Request;

/**
 * @Request()
 **/
class PostRequest
{
    /**
     * @Type("string")
     **/
    private $title;
    
    /**
     * @Type("string")
     **/
    private $text;
    
    /**
     * @Type("string")
     * @Optional()
     **/
    private $author;
}
```


