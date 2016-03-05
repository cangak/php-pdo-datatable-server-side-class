<?php
//error_reporting(0);
/*
 * Script:    DataTables PDO server-side script for PHP and MySQL
 * CopyLeft: March 2016 - wildantea, wildantea.com
 * Email : wildannudin@gmail.com
 */
class DTable extends Database
{
    private $total_filtered;
    private $record_total;
    public $offset;
    public $data = array();
    public $request;
    

    
    //filter data
    public function get_column($col)
    {
        foreach ($col as $key) {
            $keys   = $key . " LIKE ?";
            $mark[] = $keys;
        }
        
        $im = implode(' OR  ', $mark);
        return $im;
    }
    
    public function get_value($col, $value)
    {
        foreach ($col as $key) {
            $val      = '%' . $value . '%';
            $result[] = $val;
        }
        
        return $result;
    }
    
    //custom query datatable
    public function get_custom($sql, $columns)
    {
        //all data request
        $requestData   = $_REQUEST;
        $this->request = $requestData;
        
        $result = $this->pdo->prepare($sql);
        $result->execute();
        $result->setFetchMode(PDO::FETCH_OBJ);
        
        if (strpos($sql, 'where') !== false || strpos($sql, 'WHERE') !== false) {
            $status_condition = "and";
        } else {
            $status_condition = "where";
        }
        
        //save total record
        $this->record_total = $result->rowCount();
        //total filtered default
        
        $this->total_filtered = $result->rowCount();
        
        $offset       = $requestData['start'];
        $offsets      = $offset ? $offset : 0;
        $this->offset = $offsets;
        
        if (!empty($requestData['search']['value'])) {
            
            if (strpos($sql, 'where') !== false || strpos($sql, 'WHERE') !== false) {
                $condition = "and";
            } else {
                $condition = "where";
            }
            
            
            $sql = $sql;
            $sql .= " $condition " . $this->get_column($columns);
            $result = $this->pdo->prepare($sql);
            
            //offset
            $offset       = $requestData['start'];
            $offsets      = $offset ? $offset : 0;
            $this->offset = $offsets;
            
            $result->execute($this->get_value($columns, $requestData['search']['value']));
            $result->setFetchMode(PDO::FETCH_OBJ);
            $this->total_filtered = $result->rowCount();
            
            $sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . " LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
            
            $result = $this->pdo->prepare($sql);
            $result->execute($this->get_value($columns, $requestData['search']['value']));
            $result->setFetchMode(PDO::FETCH_OBJ);
        } else {
            //offset
            $offset       = $requestData['start'];
            $offsets      = $offset ? $offset : 0;
            $this->offset = $offsets;
            
            $sql = $sql;
            $sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "   LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
            $result = $this->pdo->prepare($sql);
            $result->execute();
            $result->setFetchMode(PDO::FETCH_OBJ);
        }
        
        //$data = $this->table_data($result,$columns);
        //
        return $result;
    }
    
    public function get_offset()
    {
        return $this->offset;
    }
    
    public function create_data()
    {
        $data      = $this->data;
        $json_data = array(
            "draw" => intval($this->request['draw']),
            "recordsTotal" => intval($this->record_total),
            "recordsFiltered" => intval($this->total_filtered),
            "data" => $data // total data array
        );
        echo json_encode($json_data);
        // send data as json format
    }
    
    public function set_data($data)
    {
        $this->data = $data;
    }
    
}

?>