<?php
/**
 * Created by PhpStorm.
 * User: mobilution
 * Date: 11/10/17
 * Time: 9:40 AM
 */

namespace AppBundle\Controller\Api\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ArticleController for API
 * @package AppBundle\Controller\Api\v1
 */
class ArticleController extends Controller
{

    /**
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
     *
     * @Route("/api/v1/article/new")
     * @Method("POST")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request) {

        try {

            $body = $request->getContent();
            $data = json_decode($body, true);

            $title = $data['title'];
            $author = $data['author']; // if same name not exist then will create new author as well
            $body = $data['body'];
            $topic = $data['topicid'];

            try {
                $pdo = $this->container->get('db');

                // check topic id
                $sql = "SELECT * FROM topics WHERE topic_id = :id LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $topic);
                $stmt->execute();
                $result = $stmt->fetchAll($pdo::FETCH_OBJ);

                if(empty($result)) {
                    return new JsonResponse('Topic could not found', 400);
                }
                
                // checkc author is already there
                $sql = "SELECT * FROM authors WHERE author_name = :author";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':author', $author);
                $stmt->execute();
                $result = $stmt->fetch($pdo::FETCH_OBJ);

                if(empty($result)) {
                    $sql = "INSERT INTO authors(author_name) VALUES (:author)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':author', $author);
                    $stmt->execute();
                    $authorId = $pdo->lastInsertId();
                } else {
                    $authorId = $result->author_id;
                }

                $sql = "INSERT INTO articles(article_title, article_author_id, article_body) VALUES (:title, :authorid, :body)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':authorid', $authorId);
                $stmt->bindParam(':body', $body);
                $stmt->execute();
                $articleId = $pdo->lastInsertId();

                $sql = "INSERT INTO article_topics(article_topic_article_id, article_topic_topic_id) VALUES (:articleid, :topicid)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':articleid', $articleId);
                $stmt->bindParam(':topicid', $topic);
                $stmt->execute();
                //$articleTopicId = $pdo->lastInsertId();

            } catch( PDOExecption $e ) {
                error_log($e->getMessage());
            }

            if(!$articleId) {
                return new JsonResponse('Article could not be created', 400);
            }

            return new JsonResponse('New Article created successfully. Latest article id is ' . $articleId, 201);

        } catch(\Exception $e) {
            error_log($e->getMessage());
        }

    }


    /**
     * This API method will fetch a specific article by id
     * eg. http://cms.dev/api/v1/article/2
     * header: Content-Type:application/json; charset=UTF-8
     * response: {"message":"Article found successfully","data":[{"topic_id":"33","topic_title":"good the quicik brown fox jumps over the lazy dog","article_topic_id":"9","article_topic_article_id":"3","article_topic_topic_id":"33","article_id":"3","article_title":"Another topic to good good create more articles","article_author_id":"1","article_body":"this is a good article","author_id":"1","author_name":"Dhiraj"}]}
     *
     * @Route("/api/v1/article/{id}")
     * @Method("GET")
     * @param $id
     * @return Response
     */
    public function getAction($id) {
        try {
            $result = $this->getDetailsArticle($id);

            if(!$result) {
                return new JsonResponse('Article could not found', 400);
            }

            $data = array("message" => "Article found successfully", "data" => $result);

            return new JsonResponse(json_encode($data), 200);

        } catch(\Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * This is reusable pvt function to fetch details of a article along with its related tables
     *
     * @param $id
     * @return mixed
     */
    private function getDetailsArticle($id) {
        try {
            $result = array();
            $pdo = $this->container->get('db');
            $sql = "SELECT * FROM topics t " .
                "INNER JOIN article_topics at ON at.article_topic_topic_id = t.topic_id " .
                "INNER JOIN articles a ON a.article_id = at.article_topic_article_id " .
                "INNER JOIN authors au ON au.author_id = a.article_author_id WHERE a.article_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch($pdo::FETCH_OBJ);

        } catch( PDOExecption $e ) {
            error_log($e->getMessage());
        }

        return $result;
    }


    /**
     * This API method will fetch all articles
     * eg. http://cms.dev/api/v1/getallarticle
     * header: Content-Type:application/json; charset=UTF-8
     * response: {"message":"Article found successfully","data":[{"article_id":"3","article_title":"Another topic to good good create more articles","article_author_id":"1","article_body":"this is a good article"},{"article_id":"5","article_title":"Another topic to good good create more articles","article_author_id":"3","article_body":"this is a good article"},{"article_id":"6","article_title":"Good for","article_author_id":"1","article_body":"good and bad"}]}
     *
     * @Route("/api/v1/getallarticle")
     * @Method("GET")
     * @return Response
     */
    public function getallarticleAction() {
        try {

            try {

                $pdo = $this->container->get('db');
                $sql = "SELECT * FROM articles ORDER BY article_title";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll($pdo::FETCH_OBJ);

            } catch( PDOExecption $e ) {
                error_log($e->getMessage());
            }

            if(!$result) {
                return new JsonResponse('No Article found', 400);
            }

            $data = array("message" => "Article found successfully", "data" => $result);

            return new JsonResponse(json_encode($data), 200);

        } catch(\Exception $e) {
            error_log($e->getMessage());
        }
    }


    /**
     * This will count if the association of an author with more than one article
     * @param $authorId
     * @return array
     */
    private function getCountOfArticleForAuthor($authorId) {
        try {
            $count = 0;
            $pdo = $this->container->get('db');
            $sql = "SELECT count(article_id) as cnt FROM articles WHERE article_author_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $authorId);
            $stmt->execute();
            $result = $stmt->fetch($pdo::FETCH_OBJ);

            if(!empty($result)) {
                $count = $result->cnt;
            }

        } catch( PDOExecption $e ) {
            error_log($e->getMessage());
        }

        return $count;
    }

    /**
     * This API method will delete an article.
     * eg. http://cms.dev/api/v1/article/{id}
     * header: Content-Type:application/json; charset=UTF-8
     * method: DELETE
     * response: status 204
     *
     * @Route("/api/v1/article/{id}")
     * @Method("DELETE")
     * @param $id
     * @return Response
     */
    public function deleteAction($id) {

        try {

            try {
                $pdo = $this->container->get('db');

                $pdo->beginTransaction();

                // article topic entries related to article would also deleted
                // there are some constraint for cascade delete due both way foreign key on article_topics table
                $result = $this->getDetailsArticle($id);

                if(!empty($result)) {
                    // if author is related to other articles then cant delete
                    if ((int)$this->getCountOfArticleForAuthor($result->author_id) == 1) {

                        $sql = "DELETE FROM authors WHERE author_id = ".$result->author_id."";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                    }

                } else {
                    return new JsonResponse('Article could not found.', 400);
                }

                $sql = "DELETE FROM article_topics WHERE article_topic_article_id = ".$id."";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                $sql = "DELETE FROM articles WHERE article_id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                $pdo->commit();

            } catch( PDOExecption $e ) {
                $pdo->rollback();
                error_log($e->getMessage());
                return new JsonResponse('Article could not be deleted.', 400);
            }


            return new JsonResponse(null, 204);

        } catch(\Exception $e) {
            error_log($e->getMessage());
        }

    }
}