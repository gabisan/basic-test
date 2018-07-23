<?php

namespace app\models;

use yii\base\Model;
use app\lib\Curl;
use app\lib\StringHelper;

class SearchForm extends Model
{

    //@todo endpoint : http://api3.beachinsoft.com/?r=api/search&engine=1&keywords=cat&api_key=testdev&offset=0&limit=10

    /**
     * Request Link
     *
     * @var string
     */
    public $link;

    /**
     * Request Parameters
     *
     * @var array
     */
    public $params = [];

    /**
     * @var $keywords
     */
    public $keywords;

    /**
     * @var int
     */
    public $engine = 1;

    public $api_key = 'testdev';

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['link', 'params', 'keywords'], 'safe'],
        ];
    }

    /**
     * Set Request Link To Rakuten
     *
     * @param $endpoint
     */
    public function setLink($endpoint, $name = null)
    {
        $link = 'http://api3.beachinsoft.com/?r=api/search';
        $this->link = $link.$endpoint;
    }

    /**
     * Get Request Link
     *
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set a value to the given URL parameter or remove it from the array if null.
     *
     * @param $parameter
     * @param $value
     *
     * @return $this
     */
    protected function setParameter($parameter, $value)
    {
        if ($value === null) {
            if (is_string($parameter)) {
                unset($this->params[$parameter]);
            } elseif (is_int($parameter)) {
                $this->params[$parameter] = null;
            }
        } else {
            $this->params[$parameter] = $value;
        }

        return $this;
    }

    /**
     * @param null $keywords
     * @param int $offset
     * @param int $limit
     * @return string
     */
    public function searchList($keywords = null, $offset = 0, $limit = 10)
    {
        $this->setParameter(0, 'engine='.$this->engine);
        $this->setParameter(1, 'api_key='.$this->api_key);

        $this->setParameter(2,'offset='. $offset);
        $this->setParameter(3, 'limit='.$limit);

        if ($keywords)
        {
            $clean = StringHelper::getSearchSafe($keywords, false);

            $this->setParameter(4, 'keywords='.$clean);
        }

        $params = implode('&', $this->params);

        $this->setLink('&'.$params);

//        pr($this->params,0);
//        pr($this->getLink());

        $curl     = new Curl;
        $response = $curl->get($this->getLink(),  '');

        return $response;
    }
}