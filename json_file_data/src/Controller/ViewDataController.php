<?php

namespace Drupal\json_file_data\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Drupal\Core\Render\Markup;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Form\FormStateInterface;
/**
 * Provides route responses for the Example module.
 */
class ViewDataController extends ControllerBase
{
    /**
     * Returns a simple page.
     *
     * @return array
     *               A simple renderable array
     */
    public function json_key_val_listing()
    {

        //Get parameter value while submitting filter form
        $keys = \Drupal::request()->query->get('key');
        $lang_id = \Drupal::request()->query->get('lang_id');
        $json_file_name = \Drupal::request()->query->get('json_file_name');

        //====load filter controller
        $form['form'] = \Drupal::formBuilder()->getForm('Drupal\json_file_data\Form\Filterform');

        $querytblfmt = \Drupal::database()->select('json_key_val_tbl', 'u');
        $querytblfmt->fields('u', ['jkv_id', 'lang_id', 'json_key', 'jn_id']);

        if (empty($keys) && empty($lang_id) && empty($json_file_name )) {
          $results = $querytblfmt->execute()->fetchAll();
        } elseif (!empty($keys)) {
            $querytblfmt->condition('json_key', $keys);
            $results = $querytblfmt->execute()->fetchAll();
        } else if (!empty($lang_id))  {
            $querytblfmt->condition('lang_id', $lang_id);
            $results = $querytblfmt->execute()->fetchAll();
        }else if (!empty($json_file_name))  {
            $querytblfmt->condition('jn_id', $json_file_name);
            $results = $querytblfmt->execute()->fetchAll();
        }else{
            $results = $querytblfmt->execute()->fetchAll();
        }

        $output = array();
        foreach ($results as $result) {

          $json_names = $result->jn_id;
            $output[] = [
              'jkv_id' => $result->jkv_id,     // 'userid' was the key used in the header
              'json_key' => $result->json_key,    // 'email' was the key used in the header
              'jn_id' => $this->getnamejsonfile($json_names),    // 'email' was the key used in the header
              'lang_id' => $result->lang_id, // 'Username' was the key used in the header
              'operations' =>  Markup::create('<a href="edit/'.$result->jkv_id.'">Edit</a> | <a href="delete/'.$result->jkv_id.'">Delete</a>' ),    // 'email' was the key used in the header
            ];
        }

        $header = [
           'jkv_id' => t('ID'),
           'json_key' => t('Key'),
           'jn_id' => t('JSON File Name'),
           'lang_id' => t('Language'),
           'operations' => t('OPERATIONS'),
        ];

        $form['table'] = [
          '#type' => 'table',
          '#header' => $header,
          '#rows' => $output,
          '#empty' => t('No Key found'),
        ];

        return $form;
    }

    public function getDetails($lang)
    {
        //fetch data from employee table.
        $db = \Drupal::database();
        $query = $db->select('json_key_val_tbl', 'n');
        $query->condition('lang_id', $lang, '=');
        $query->fields('n');
        $response = $query->execute()->fetchAll();

        foreach ($response as $jsonvalue) {
            $json_key[] = $jsonvalue->json_key;
           if((!is_null($jsonvalue->key_value_img))&&(!is_null($jsonvalue->key_value_img_alt))){
                $file = \Drupal\file\Entity\File::load($jsonvalue->key_value_img);
               if(!is_null($file)){
                $uri = $file->getFileUri();
                $url = \Drupal\Core\Url::fromUri(file_create_url($uri))->toString();
               }else{
                   $uri = "";
                   $url = "";
               }

                $val['src']=$url;
                $val['alt']= $jsonvalue->key_value_img_alt;
            }

            if(!is_null($jsonvalue->key_value_fmt)){
                $key_value_fmt =html_entity_decode($jsonvalue->key_value_fmt, ENT_QUOTES);
            }

            if(!is_null($jsonvalue->key_value_text)){
                $key_value_text = $jsonvalue->key_value_text;
            }
            $key_value[] = array_filter([array_filter($val),$key_value_fmt, $key_value_text]);
        }

        foreach ($key_value as  $newvalue) {
           foreach ($newvalue as $finalvalue) {
              $json_val[]= $finalvalue;
           }
        }

        $datajson = array_combine($json_key, $json_val);

        return new JsonResponse($datajson);

    }

    public function getnamejsonfile($json_names){

        $query = \Drupal::database()->select('json_name_tbl', 'cont');
        $query->condition('json_name_id', $json_names);
        $query->fields('cont', ['page_name']);
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
                $json_names = $row['page_name'];
        }
        return $json_names;
    }
}
