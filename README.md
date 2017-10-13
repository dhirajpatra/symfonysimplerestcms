# symfonysimplerestcms
This is a simple CMS made with symfony and REST along with PDO [ORM not used intentionally]

### Main features 

 * Create a topic
 * Delete a topic
 * List all topics
 * Show a specific topic

 * Create an article for a topic
 * Delete an article
 * List all articles from a topic
 * Show a specific article

The domain objects have the following attributes:

A topic has a title and a list of assigned articles. An article has a title, an author and a text.

The system implemented with RESTful web services and stored in a mysql database.

Also created some php unit tests. All protocols are JSON format and implemented as objects.

### How to install
Clone or download code into your document root. I have created a virtual host named `cms.dev` You can take help here how to create virtual host if you have not done already `https://www.digitalocean.com/community/tutorials/how-to-set-up-apache-virtual-hosts-on-ubuntu-16-04`

Change the configuration file: `parameters.yml` inside `/app/config` folder.
Give proper permissions.
Run `composer install` from your root folder of the application.

Create Mysql Database named `cms` and run the sql script kept in `/documents` folder kindly check the `setup_db.sql` into nely created database `cms`. You can use mysqlworkbench or phpmyadmin what ever you used to.

### How to run
Hope you have already run your Apache server and Mysql server. So go to browser and type `http://cms.dev` you will get the home page. But as this application is the back end process for contain managemetnt process via REST API so no more browser pages here.

### REST API
You have to run all those APIs to test from any rest api client eg. `postman` or from `curl` command you can get some help `http://www.codingpedia.org/ama/how-to-test-a-rest-api-from-command-line-with-curl/` if you need to learn first time.

### API lists
Whole code are well documented so you will get all details in code comments. 

#### Topic related APIs

 * This API method will save/create a new topic. We can use pdo transaction as well with rollback effect in error
 * eg. http://cms.dev/api/v1/topic/new
 * header: Content-Type:application/json; charset=UTF-8
 * body: {"title": "This is a good topic"}
 
 ===============================================================================================================
 * This API method will fetch a specific topic by id
 * eg. http://cms.dev/api/v1/topic/2
 * header: Content-Type:application/json; charset=UTF-8
 * response: {"message":"Topic found successfully","data":[{"topic_id":"22","topic_title":"This is a good topic for test","article_topic_id":"2","article_topic_article_id":"2","article_topic_topic_id":"22","article_id":"2","article_title":"Good Article","article_author_id":"1","article_body":"This is a good article by Dhiraj and now we are testing it.","author_id":"1","author_name":"Dhiraj"}]}
 
 ===========================================================================================================
 * This API method will fetch all topics
 * eg. http://cms.dev/api/v1/getalltopic
 * header: Content-Type:application/json; charset=UTF-8
 * response: {"message":"Topic found successfully","data":[{"topic_id":"22","topic_title":"This is a good topic for test"},{"topic_id":"23","topic_title":"This is a good topic for test again"}]}
 
 ============================================================================================================
 * This API method will delete a topic.
 * eg. http://cms.dev/api/v1/topic/{id}
 * header: Content-Type:application/json; charset=UTF-8
 * method: DELETE
 * response: status 204
 
 ============================================================================================================
 
 
#### Article related APIs

 * This API method will save/create a new article along with author if needed.
 * We can use pdo transaction as well with rollback effect in error
 * eg. http://cms.dev/api/v1/article/new
 * header: Content-Type:application/json; charset=UTF-8
 * body: {
    "title": "Another topic to good good create more articles",
    "author": "Dhiraj Patra",
    "body": "this is a good article",
    "topicid": 33
    }
 * response: New Article created successfully. Latest article id is 5
 
 ===========================================================================================================
* This API method will fetch a specific article by id
* eg. http://cms.dev/api/v1/article/2
* header: Content-Type:application/json; charset=UTF-8
* response: {"message":"Article found successfully","data":[{"topic_id":"33","topic_title":"good the quicik brown fox jumps over the lazy dog","article_topic_id":"9","article_topic_article_id":"3","article_topic_topic_id":"33","article_id":"3","article_title":"Another topic to good good create more articles","article_author_id":"1","article_body":"this is a good article","author_id":"1","author_name":"Dhiraj"}]}

===========================================================================================================
* This API method will fetch all articles
* eg. http://cms.dev/api/v1/getallarticle
* header: Content-Type:application/json; charset=UTF-8
* response: {"message":"Article found successfully","data":[{"article_id":"3","article_title":"Another topic to good good create more articles","article_author_id":"1","article_body":"this is a good article"},{"article_id":"5","article_title":"Another topic to good good create more articles","article_author_id":"3","article_body":"this is a good article"},{"article_id":"6","article_title":"Good for","article_author_id":"1","article_body":"good and bad"}]}

==============================================================================================================
* This API method will delete an article.
* eg. http://cms.dev/api/v1/article/{id}
* header: Content-Type:application/json; charset=UTF-8
* method: DELETE
* response: status 204
==============================================================================================================

### How to run test
*pre requisits 

You have to get the details ids from database like topic_id and article_id which are present for few test. you will get all details comments in test code.

*run

From root command promt use `php vendor/bin/phpunit` also I have provided screen shot in /documents folder.
If you are not getting all green then it must have problem with `ids` so carefully check the test cases and will get the details with comments to change the ids there as per your database data.

#### Any question kindly reply me Thanks
