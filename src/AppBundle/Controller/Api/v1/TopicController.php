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
 * Class TopicController for API
 * @package AppBundle\Controller\Api\v1
 */
class TopicController extends Controller
{

    /**
     * This API method will save/create a new topic. We can use pdo transaction as well with rollback effect in error
     * eg. http://cms.dev/api/v1/topic/new
     * header: Content-Type:application/json; charset=UTF-8
     * body: {"title": "This is a good topic"}
     *
     * @Route("/api/v1/topic/new")
     * @Method("POST")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request) {

        try {

            $body = $request->getContent();
            $data = json_decode($body, true);

            $title = $data['title'];

            try {

                $pdo = $this->container->get('db');
                $sql = "INSERT INTO topics(topic_title) VALUES (:title)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':title', $title);
                $stmt->execute();
                $result = $pdo->lastInsertId();

            } catch( PDOExecption $e ) {
                error_log($e->getMessage());
            }

            if(!$result) {
                return new JsonResponse('Topic could not be created', 400);
            }

            return new JsonResponse('New Topic created successfully. Latest topic id is ' . $result, 201);

        } catch(\Exception $e) {
            error_log($e->getMessage());
        }

    }


    /**
     * This API method will fetch a specific topic by id
     * eg. http://cms.dev/api/v1/topic/2
     * header: Content-Type:application/json; charset=UTF-8
     * response: {"message":"Topic found successfully","data":[{"topic_id":"22","topic_title":"This is a good topic for test","article_topic_id":"2","article_topic_article_id":"2","article_topic_topic_id":"22","article_id":"2","article_title":"Good Article","article_author_id":"1","article_body":"This is a good article by Dhiraj and now we are testing it.","author_id":"1","author_name":"Dhiraj"}]}
     *
     * @Route("/api/v1/topic/{id}")
     * @Method("GET")
     * @param $id
     * @return Response
     */
    public function getAction($id) {
        try {
            $result = $this->getDetailsTopic($id);

            if(!$result) {
                return new JsonResponse('Topic could not found', 400);
            }

            $data = array("message" => "Topic found successfully", "data" => $result);

            return new JsonResponse(json_encode($data), 200);

        } catch(\Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * This is reusable pvt function to fetch details of a topic along with its related tables
     *
     * @param $id
     * @return mixed
     */
    private function getDetailsTopic($id) {
        try {
            $result = array();
            $pdo = $this->container->get('db');
            $sql = "SELECT * FROM topics t " .
                "LEFT JOIN article_topics at ON at.article_topic_topic_id = t.topic_id " .
                "LEFT JOIN articles a ON a.article_id = at.article_topic_article_id " .
                "LEFT JOIN authors au ON au.author_id = a.article_author_id WHERE t.topic_id = :id";
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
     * This will count if the association of an article with more than one topic
     * @param $articleId
     * @return array
     */
    private function getCountOfTopicForArticle($articleId) {
        try {
            $count = 0;
            $pdo = $this->container->get('db');
            $sql = "SELECT count(at.article_topic_id) as cnt FROM topics t " .
                "INNER JOIN article_topics at ON at.article_topic_topic_id = t.topic_id " .
                "INNER JOIN articles a ON a.article_id = at.article_topic_article_id " .
                "WHERE a.article_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $articleId);
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
     * This API method will fetch all topics
     * eg. http://cms.dev/api/v1/getalltopic
     * header: Content-Type:application/json; charset=UTF-8
     * response: {"message":"Topic found successfully","data":[{"topic_id":"22","topic_title":"This is a good topic for test"},{"topic_id":"23","topic_title":"This is a good topic for test again"}]}
     *
     * @Route("/api/v1/getalltopic")
     * @Method("GET")
     * @return Response
     */
    public function getalltopicAction() {
        try {

            try {

                $pdo = $this->container->get('db');
                $sql = "SELECT * FROM topics ORDER BY topic_title";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll($pdo::FETCH_OBJ);

            } catch( PDOExecption $e ) {
                error_log($e->getMessage());
            }

            if(!$result) {
                return new JsonResponse('No Topic found', 400);
            }

            $data = array("message" => "Topic found successfully", "data" => $result);

            return new JsonResponse($data, 200);

        } catch(\Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * This API method will delete a topic.
     * eg. http://cms.dev/api/v1/topic/{id}
     * header: Content-Type:application/json; charset=UTF-8
     * method: DELETE
     * response: status 204
     *
     * @Route("/api/v1/topic/{id}")
     * @Method("DELETE")
     * @param $id
     * @return Response
     */
    public function deleteAction($id) {

        try {

            try {
                $pdo = $this->container->get('db');

                $pdo->beginTransaction();

                // all article of this topic would also deleted
                // there are some constraint for cascade delete due both way foreign key on article_topics table
                $result = $this->getDetailsTopic($id);

                if(!empty($result)) {
                    foreach ($result as $row) {

                        // if article is related to another topic then can't delete
                        if ((int)$this->getCountOfTopicForArticle($row->article_id) == 1) {
                            // author not deleting here
                            $sql = "DELETE FROM articles WHERE article_id = ".$row->article_id."";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':id', $id);
                            $stmt->execute();
                        }
                    }
                } else {
                    return new JsonResponse('Topic could not found.', 400);
                }

                $sql = "DELETE FROM article_topics WHERE article_topic_topic_id = ".$id."";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                $sql = "DELETE FROM topics WHERE topic_id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                $pdo->commit();

            } catch( PDOExecption $e ) {
                $pdo->rollback();
                error_log($e->getMessage());
                return new JsonResponse('Topic could not be deleted.', 400);
            }


            return new JsonResponse(null, 204);

        } catch(\Exception $e) {
            error_log($e->getMessage());
        }

    }
}