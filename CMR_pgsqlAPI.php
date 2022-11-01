<?php
    if(isset($_POST['functionname']))
    {
        $paPDO = initDB();
        $paSRID = '4326';
        $paPoint = $_POST['paPoint'];
        $functionname = $_POST['functionname'];
        
        $aResult = "null";
        if ($functionname == 'getGeoCMRToAjax')
            $aResult = getGeoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getInfoCMRToAjax')
            $aResult = getInfoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getResult')
            $aResult = getResult($paPDO, $paSRID, $paPoint);
        echo $aResult;
    
        closeDB($paPDO);
    }

    function initDB()
    {
        // Kết nối CSDL
        $paPDO = new PDO('pgsql:host=localhost; dbname=csdlthl1; port=5433','postgres','ad123465');
        return $paPDO;
    }
    function query($paPDO, $paSQLStr)
    {
        try
        {
            // Khai báo exception
            $paPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Sử đụng Prepare 
            $stmt = $paPDO->prepare($paSQLStr);
            // Thực thi câu truy vấn
            $stmt->execute();
            
            // Khai báo fetch kiểu mảng kết hợp
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            
            // Lấy danh sách kết quả
            $paResult = $stmt->fetchAll();   
            return $paResult;                 
        }
        catch(PDOException $e) {
            echo "Thất bại, Lỗi: " . $e->getMessage();
            return null;
        }       
    }
    function closeDB($paPDO)
    {
        // Ngắt kết nối
        $paPDO = null;
    }
    function example1($paPDO)
    {
        $mySQLStr = "SELECT * FROM \"cmr_adm1\"";
        $result = query($paPDO, $mySQLStr);

        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                echo $item['name_0'] . ' - '. $item['name_1'];
                echo "<br>";
            }
        }
        else
        {
            echo "example1 - null";
            echo "<br>";
        }
    }
    function example2($paPDO)
    {
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"cmr_adm1\"";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                echo $item['geo'];
                echo "<br><br>";
            }
        }
        else
        {
            echo "example2 - null";
            echo "<br>";
        }
    }
    function example3($paPDO,$paSRID,$paPoint)
    {
        echo $paPoint;
        echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        echo $paPoint;
        echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"cmr_adm1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"cmr_adm1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        echo $mySQLStr;
        echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                echo $item['geo'];
                echo "<br><br>";
            }
        }
        else
        {
            echo "example2 - null";
            echo "<br>";
        }
    }
  
    

    function getInfoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"CMR_adm1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT  pointpo.name ,  pointpo.addr_stree
        from  \"pointpo\" 
        where ST_Distance('SRID=4326;$paPoint', pointpo.geom) <= all(select ST_Distance('SRID=4326;$paPoint', pointpo.geom) from \"pointpo\") 
        and ST_Distance('SRID=4326;$paPoint', pointpo.geom) < 0.00025";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        if ($result != null)
        {
            $resFin = '
            <div class="buudien">
            <table >
            ';
            // kết quả
        
            $resFin = $resFin.'<tr><td>Tên Bưu Điện : '.$result[0]['name'].'</td></tr>';
            $resFin = $resFin.'<tr><td>Địa Chỉ : '.$result[0]['addr_stree'].'</td></tr>';

            $resFin = $resFin.'</table>
            </div>';
    
            echo $resFin;
        }
        else
            return "";
    }
    function getSearch($search)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPDO = initDB();
            $mySQLStr = "SELECT  pointpo.name ,  pointpo.addr_stree
                from  \"pointpo\" 
                where  pointpo.name ILIKE '%$search%' or pointpo.addr_stree   ILIKE '%$search%'";
            //echo $mySQLStr;
            //echo "<br><br>";
            $result = query($paPDO, $mySQLStr);
            
        if ($result != null)
        {   
 
            $resFin = '
 
            <div class="navbar_overlay "></div>

            <div class="navbar_select-option ">

            <script>
             const overlay = document.querySelector(".navbar_overlay");
             const model = document.querySelector(".navbar_select-option ");
             
             overlay.addEventListener("click", () => {
                  model.style.display = "none";
                  overlay.style.display = "none";
               });

             
             </script>
             
            <table class="table" >
            
  <thead>
    <tr>
      <th scope="col">Tên Bưu Điện</th>
      <th scope="col">Địa Chỉ</th>
    </tr>
  </thead>
  <tbody>'; 

            
             foreach ($result as $value){
                 $resFin = $resFin.'<tr>
                 <td>'.$value['name'].'</td>';
                 $resFin = $resFin.'<td>'.$value['addr_stree'].'</td>
                 </tr>';
            //     $resFin = $resFin.'<br>'; 
             }
             $resFin = $resFin.'</tbody>
             </table>
             </div>
        
             
             '; 
            
             echo $resFin;
        }
        else
            return "error";
    }
   
    function getShowAll($search)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPDO = initDB();
            $mySQLStr = "SELECT  pointpo.name ,  pointpo.addr_stree
                from  \"pointpo\" ";
            //echo $mySQLStr;
            //echo "<br><br>";
            $result = query($paPDO, $mySQLStr);
            

          
            
        if ($result != null)
        {   
            $resFin = '

            

         

            <div class="navbar_select-option ">


            <table class="table">
  <thead>
    <tr>
      <th scope="col">Tên Bưu Điện</th>
      <th scope="col">Địa Chỉ</th>
    </tr>
  </thead>
  <tbody>'; 
            
             foreach ($result as $value){
                 $resFin = $resFin.'<tr>
                 <td>'.$value['name'].'</td>';
                 $resFin = $resFin.'<td>'.$value['addr_stree'].'</td>
                 </tr>';
            //     $resFin = $resFin.'<br>'; 
             }
             $resFin = $resFin.'</tbody>
             </table>

             </div>


             
             '
             ; 
            
             echo $resFin;
        }


        else
            return "error";
    }
    
?>










