<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\filters\AccessControl;
use app\components\AdminAccessControl;
use app\models\NewsResources;
use DateTime;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CronController extends Controller {

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex() {
        $this->layout = false;
        $limit = 10000;
        $newsResources = NewsResources::findeResources($limit);
        $resources = [];
        $urls = [];
        foreach ($newsResources as $resourcesKey => $resourcesValue) {
            $urls[] = $resourcesValue['resource_url'];
        }
        $content = $this->get_data($urls);
        foreach ($content as $c) {
            try {
                libxml_use_internal_errors(true);
                $xmlObject = simplexml_load_string($c);
                $xml = explode("\n", $c);

                if (!$xmlObject) {
                    $errors = libxml_get_errors();

                    foreach ($errors as $error) {
//                            echo display_xml_error($error, $xml);
                    }

                    libxml_clear_errors();
                }
            } catch (Exception $ex) {
                
            }

            $resources[] = $xmlObject;
        }

        $newsArray = [];
        $rows = [];
        $k = 0;
//        print_r($resources);
//        die;
        foreach ($resources as $resource) {
            $j = 0;
            if (is_object($resource) && is_object($resource->channel) && is_object($resource->channel->item)) {
                
                foreach ($resource->channel->item as $item) {
                    if ($j === 15) {
                        break;
                    }
                    $newsArray[$k]['title'] = (string) $item->title;
                    $newsArray[$k]['link'] = (string) $item->link;
                    $newsArray[$k]['site_link'] = (string) $resource->channel->link;
                    $newsArray[$k]['pubDate'] = (isset($item->pubDate) && !empty($item->pubDate)) ? (string) $item->pubDate : (string) '';
                    if (!empty($newsArray[$k]['pubDate'])) {
                        $date = new DateTime($newsArray[$k]['pubDate']);
                        $date->setTimezone(new \DateTimeZone('Europe/London'));
                        $newsArray[$k]['pubDate'] = $date->format("d-m-Y H:i:s");
                    }

                    $cDate = new \yii\db\Expression('NOW()');
                    $rows[] = [
                        'news_title' => (string) $item->title,
                        'news_pub_date' => $newsArray[$k]['pubDate'],
                        'news_url' => (string) $item->link,
                        'site_url' => (string) $resource->channel->link,
                        'site_title' => (string) $resource->channel->title,
                        'created' => $cDate,
                        'modified' => $cDate
                    ];

                    $k++;
                    $j++;
                }
            } else if (is_object($resource) && is_object($resource->entry)) {
                foreach ($resource->entry as $item) {
                    
                    if ($j === 15) {
                        break;
                    }
                    $newsArray[$k]['title'] = (string) $item->title;
                    $newsArray[$k]['link'] = (string) $item->link;
                    $newsArray[$k]['pubDate'] = (isset($item->published) && !empty($item->published)) ? (string) $item->published : (string) '';
                    if (!empty($newsArray[$k]['pubDate'])) {
                        $date = new DateTime($newsArray[$k]['pubDate']);
                        $date->setTimezone(new \DateTimeZone('Europe/London'));
                        $newsArray[$k]['pubDate'] = $date->format("d-m-Y H:i:s");
                    }

                    $cDate = new \yii\db\Expression('NOW()');
                    $rows[] = [
                        'news_title' => (string) $item->title,
                        'news_pub_date' => $newsArray[$k]['pubDate'],
                        'news_url' => (string) $item->link['href'],
                        'site_url' => (string) $resource->link['href'],
                        'site_title' => (string) $resource->title,
                        'created' => $cDate,
                        'modified' => $cDate
                    ];

                    $k++;
                    $j++;
                }
            }
        }

        $model = new \app\models\News();
        $model->deleteAll();
        \Yii::$app->db->createCommand('ALTER TABLE news AUTO_INCREMENT = 1')->execute();
        \Yii::$app->db->createCommand()->batchInsert('news', ['news_title', 'news_pub_date', 'news_url','site_url','site_title', 'created', 'modified'], $rows)->execute();
    }

    protected function get_data($data) {
        // array of curl handles
        $curly = array();
        // data to be returned
        $result = array();

        // multi handle
        $mh = curl_multi_init();

        // loop through $data and create curl handles
        // then add them to the multi-handle
        foreach ($data as $id => $d) {

            $curly[$id] = curl_init();

            $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
            curl_setopt($curly[$id], CURLOPT_URL, $url);
            curl_setopt($curly[$id], CURLOPT_HEADER, 0);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);

            // post?
            if (is_array($d)) {
                if (!empty($d['post'])) {
                    curl_setopt($curly[$id], CURLOPT_POST, 1);
                    curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
                }
            }

            // extra options?
            if (!empty($options)) {
                curl_setopt_array($curly[$id], $options);
            }

            curl_multi_add_handle($mh, $curly[$id]);
        }

        // execute the handles
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);


        // get content and remove handles
        foreach ($curly as $id => $c) {
            $result[$id] = curl_multi_getcontent($c);
            curl_multi_remove_handle($mh, $c);
        }

        // all done
        curl_multi_close($mh);

        return $result;
    }

}
