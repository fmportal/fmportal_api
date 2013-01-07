FM Portal API
=====================================

This API is designed to be used to interact with the Football Manager Portal. Sites built using an existing CMS such as Wordpress can use a specifically built plugin, while the API is designed for use by custom built websites.

Using the API
--------
Using the API is very easy, simply include the php file like so:

```php
include('fmportal_api.php');
```

Then select your articles and loop through adding each article one be one.

Below is an example, the actual usage will differ depending on how your database is organised.

```php
$api = new fmportal_api();

foreach($articles as $article)
{
    $api->add_content(
        $article->type,
		$article->permalink,
        $article->title,
        $article->id,
        $article->date_added,
        $article->fulltext,
        $article->description,
		$article->tags
    );
}

$api->send_output();

exit;
```

Identifying a request
---------------------

When a request is made from the FM Portal the useragent will be set to "fmportal". In PHP you can check if like so:

```php
if($_SERVER['HTTP_USER_AGENT'] == "fmportal")
```

Using the add_content() function
--------------------------------

The add content function expects the following data to be sent:

**type**: The type of content this may be one of the following: "article", "video", "podcast", "download", "forumtopic"

**permalink**: The url where your content can be found, this must be publically accessible.

**title**: The title of your content

**id**: Some sort of unique ID to identify your content. This may be its URL, but most sites have an incremental unique identifier to identify content which may be used instead. This may be any string upto 255 characters, so if numeric incremental unique id's have the potential to clash you may prefix them such as "download_3", "article_3" and "topic_3" rather than "3".

**date_added**: This should be the date your content was first published online. It may be either a timestamp or date string. If a date string is given it will be assumed to be GMT unless otherwise specified

**fulltext**: This should be the fulltext of your content. Please provide the complete article and not just an excerpt. If it's a video then a description is fine.

**description**: [optional] This is an optional short description of your content. If not supplied we will generate this based on the "fulltext" supplied.

**tags**: [optional] This should be an array of tags and categories associated with your content. Please refer to the section on including tags below.

Custom Requests
---------------

In order to import all of your content the FM Portal will periodically make a call to the API hosted on your server. When making a request the FM Portal will send certain request data and you should respond with an appropiate response.

The following data may be sent as a request (All requests will be sent as a post, so should be accessble via the $_POST array within your application)

**count**: This is the number of articles the FM Portal expects you to respond with.

**offset**: This is the article number to start with. If you have 25 articles and a request is sent with a count of 10 and an offset of 10, you should respond with articles 11-20.

**orderby**: This may either be "date_created" or "date_modified". Use to order the request you make to your database and is important when used with offset

**order_dir**: This is the direction you should order your articles in and may be either "DESC" or "ASC"

An example of how you may make a database call based on the request made by FM Portal can be found below. However please remember that this will not work on any specific site and will need to be customised depending on how your database is organised.

It is important to define defaults just incase no requests are sent.

```php
$count = (isset($_POST['count'])) ? (int)$_POST['count'] : 10;
$offset = (isset($_POST['offset'])) ? (int)$_POST['offset'] : 0;
$orderby = (isset($_POST['date_modified']) && $_POST['date_modified']) ? 'date_edited' : 'date_added';
$order_dir = (isset($_POST['order_dir']) && $_POST['order_dir'] == 'ASC') ? 'ASC' : 'DESC';


mysql_query('SELECT * FROM articles ORDER BY '.$orderby.' '.$order_dir.' LIMIT '.$offset.','.$count);
```

If the FM Portal was to request 10 items with an offset of 10 ordered by the date it was first published in descending order the SQL query generated would look like this:

```php
mysql_query('SELECT * FROM articles ORDER BY date_added DESC LIMIT 10,10');
```

Tags
----
Tags should be sent to the add_content() function using an array with each tag as a string like so:

```php
$tags = array(
    'Tag One',
    'Tag Two',
    'Tag Three',
    'Tag Four'
);
```