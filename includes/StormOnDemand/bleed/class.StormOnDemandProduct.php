<?php

require_once 'class.StormOnDemandConnection.php';

if(!class_exists('StormOnDemandProduct'))
{
    class StormOnDemandProduct extends StormOnDemandConnection
    {
        public function details($alias, $code = '')
        {
            $data = array();

            if($alias)
            {
                $data['alias'] = $alias;
            }

            if($code)
            {
                $data['code'] = $code;
            }

            return $this->__request('Product/details', $data, __METHOD__);
        }

        public function lists($page_size = 20, $page_num = 1, $category = '', $series = '')
        {
            $data['page_size'] = $page_size;
            $data['page_num'] = $page_num;

            if($category)
            {
                $data['category'] = $category;
            }

            if($series)
            {
                $data['series'] = $series;
            }

            return $this->__request('Product/list', $data, __METHOD__);
        }

        public function detailList($alias, $code = '', $page_size = 20, $page_num = 1)
        {
            $data['page_size'] = $page_size;
            $data['page_num'] = $page_num;

            if($alias)
            {
                $data['alias'] = $alias;
            }

            if($code)
            {
                $data['code'] = $code;
            }

            return $this->__request('Product/detailList', $data, __METHOD__);
        }
    }
}
