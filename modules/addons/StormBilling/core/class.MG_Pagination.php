<?php

class MG_Pagination
{
    private $pagination_name;
    
    private $amount = 0;
    
    private $settings = array();
    
    public function __construct($pagination_name = 'mg_pagination')
    {
        $this->pagination_name = $pagination_name;
        
        $this->settings = $_SESSION[$this->pagination_name];
        
        //NON AJAX REQUEST
        if(!isset($_REQUEST['ajax']))
        {
            $this->resetFilter();
            $this->resetOrderBy();
        }
    }
    
    public function __destruct()
    {
        $_SESSION[$this->pagination_name] = $this->settings;
    }
    
    public function query($and = false, $where = false, $limit = true)
    {
        $query = ' ';
        
        if($and && count($this->settings['filters']))
        {
            $query = ' AND ';
        }
        
        if($where && count($this->settings['filters']))
        {
            $query = ' WHERE ';
        }
        
        if($this->settings['filters'])
        {
            
            foreach($this->settings['filters'] as $field_name => $field_value)
            {
                if(strpos($field_name, '.') === false)
                {
                    $query .= ' `'.$field_name.'` LIKE \'%'.$field_value.'%\' AND';
                }
                else
                {
                    $field = explode('.', $field_name);
                    $field = '`'.$field[0].'`.`'.$field[1].'`';
                    $query .= ' '.$field.' LIKE \'%'.$field_value.'%\' AND';
                }
            }   
            $query = substr($query, 0, strlen($query)-3);
        }
        
        if(isset($this->settings['order_by']))
        {
            $query .= ' ORDER BY ';
            foreach($this->settings['order_by'] as $field_name => $type)
            {
                $query .= ' '.$field_name.' '.$type.' ';
                break;
            }
        }
        
        if($limit)
        {
            $query .= $this->getLimitAndOffset();
        }

        return $query;
    }
    
    public function getLimitAndOffset()
    {
        if(!$this->settings['offset'])
        {
            $this->settings['offset'] = 0;
        }
        
        if(!$this->settings['limit'])
        {
            $this->settings['limit'] = 10;
        }
        
        $query = ' LIMIT '.$this->settings['offset'].', '.$this->settings['limit'];
        return $query;
    }
    
    public function setAmount($amount)
    {
        $this->settings['amount'] = $amount;
    }
    
    public function getPagination()
    {
        if($this->settings['amount'] <=  $this->settings['limit'])
        {
            $amount = 0;
        }
        else
        {
            $amount = ceil($this->settings['amount'] / $this->settings['limit']);
        }
        
        $i = 0;
        
        $ul = '<form action="" method="post" class="pagination">
                    <input type="hidden" name="parent" value="'.$this->pagination_name.'" />
                    <ul>';
        $ul .= '<li class="prev '.(!$this->isPrev() ? 'disabled' : '').'"><a href="#prev=1&parent='.$this->pagination_name.'">Prev</a></li>';
        
        if($amount > 20)
        {
            $current    =   $this->getCurrentPage();
            $start      =   0;
            $end        =   20;
            $max        =   20;
            
            if($current - round($max / 2) <= 0)
            {
                $start = 0;
                $end = $max;
            }
            elseif($current + round($max / 2) > $amount)
            {
                $start  =   $amount - $max;
                $end    =   $amount;
            }
            else
            {
                $start  =   $current - round($max / 2);
                $end    =   $current + round($max / 2);
            }
            
            if($start > 1)
            {
                $ul .= '<li class="'.(0 == $this->getCurrentPage() ? 'active' : '').'"><a href="#page='.(0).'&parent='.$this->pagination_name.'">'.(1).'</a></li>';
                $ul .= '<li><a href="#">...</a></li>';
            }
            
            while($start < $end)
            {
                $ul .= '<li class="'.($start == $this->getCurrentPage() ? 'active' : '').'"><a href="#page='.$start.'&parent='.$this->pagination_name.'">'.($start+1).'</a></li>';
                $start++;
            }

            if($end < $amount)
            {
                $ul .= '<li><a href="#">...</a></li>';
                $ul .= '<li class="'.($amount - 1 == $this->getCurrentPage() ? 'active' : '').'"><a href="#page='.($amount-1).'&parent='.$this->pagination_name.'">'.($amount).'</a></li>';
            }
            
        }
        else
        {

            while($i < $amount)
            {
                $ul .= '<li class="'.($i == $this->getCurrentPage() ? 'active' : '').'"><a href="#page='.$i.'&parent='.$this->pagination_name.'">'.($i+1).'</a></li>';
                $i++;
            }
        }
        $ul .= '<li class="next '.(!$this->isNext() ? 'disabled' : '').'"><a href="#next=1&parent='.$this->pagination_name.'">Next</a></li>';
        $ul .= '</ul>
            </form>';
        
        return $ul;
    }
    
    public function getCurrentPage()
    {
        return $this->settings['offset'] / $this->settings['limit'];
    }
    /************************* FILTERING ****************************/
    public function addFilter($field_name, $field_value)
    {
        $this->settings['filters'][$field_name] = $field_value;
    }
    
    public function removeFilter($field_name)
    {
        if(isset($this->settings['filters'][$field_name]))
        {
            unset($this->settings['filters'][$field_name]);
        }
    }
    
    public function resetFilter()
    {
        $this->settings['filters'] = null;
        $this->settings['offset'] = 0;
    }
    
    /***************** PAGINATION ***********************/
    public function next()
    {
        $this->settings['offset'] += $this->settings['limit'];
    }
    
    public function prev()
    {
        $this->settings['offset'] -= $this->settings['limit'];
    }
    
    public function isNext()
    {
        if($this->settings['amount'] <=  $this->settings['limit'])
        {
            return false;
        }
        
        if($this->settings['offset'] + $this->settings['limit'] >= $this->settings['amount'])
        {
            return false;
        }
        
        return true;
    }
    
    public function isPrev()
    {
        if($this->settings['offset'] - $this->settings['limit'] <  0)
        {
            return false;
        }
        
        return true;
    }
    
    public function setPage($page)
    {
        if($this->settings['amount'] > $this->settings['limit'] * $page)
        {
            $this->settings['offset'] = $this->settings['limit'] * $page;
        }
    }
    /************* SOME SETTINGS *********************/
    public function setLimit($limit)
    {
        $this->settings['limit'] = $limit;
    }
    
    public function getLimit()
    {
        return $this->settings['limit'];
    }
    
    public function setOffset($offset)
    {
        $this->settings['offset'] = $offset;
    }
    
    public function getOffset($offset)
    {
        return $this->settings['offset'];
    }
    
    public function setOrderBy($field_name, $type = 'ASC')
    { 
        $this->settings['order_by'] = array();
        $this->settings['order_by'][$field_name] = $type;
    }
    
    public function resetOrderBy()
    {
        $this->settings['order_by'] = null;
    }
}