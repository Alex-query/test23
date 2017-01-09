<?php
class Handler
{

    private static $limit=2; //сколько за раз брать из таблицы записей для записи в файл
    private static $rate=1000; //как часто очищать буыер вывода

    private function __construct()
    {

    }

    public static function PrepareTables()
    {
        $sql="CREATE TABLE IF NOT EXISTS `test` (
                `id` int(11) NOT NULL,
                `str` varchar(255) NOT NULL,
                `deci` int(11) NOT NULL DEFAULT '0'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `test` ADD PRIMARY KEY (`id`);
                ALTER TABLE `test`MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5";
        $answer=DatabaseHandler::Execute($sql);
        $sql = "TRUNCATE test";
        $answer=DatabaseHandler::Execute($sql);
        return 1;
    }
    public static function Run($file_inname,$file_outname)
    {
        if (($handle_f = fopen($file_inname, "r")) !== FALSE)
        {
            $i=0;
            while ( ($data_f = fgetcsv($handle_f, 0, ","))!== FALSE) {
                $sql = "select * from test where str like :str";
                $params=array(':str'=>$data_f[0]);
                $answer0=DatabaseHandler::GetRow($sql, $params);
                if(isset($answer0['id']))
                {
                    $sql = "update test set `deci`=`deci`+ :deci where id=:id";
                    $params=array(':id'=>$answer0['id'],':deci'=>$data_f[1]);
                    $answer=DatabaseHandler::Execute($sql, $params);
                }else{
                    $sql = "insert into test (str,deci) values (:str,:deci)";
                    $params=array(':str'=>$data_f[0],':deci'=>$data_f[1]);
                    $answer=DatabaseHandler::Execute($sql, $params);
                }
                if(!strstr($i/self::$rate,'.')){
                    flush();
                    ob_flush();
                }
                $i++;
            }
            fclose($handle_f);
        } else {$err = 1; echo "Не получилось открыть файл";}
        $offset=0;
        $limit=self::$limit;
        $fp = fopen($file_outname, 'w');
        while(0==0)
        {
            $sql="SELECT * FROM `test` ORDER BY `test`.`deci` DESC  limit ".$offset.','.$limit;
            $answera=DatabaseHandler::GetAll($sql);
            if(count($answera)==0)
            {
                break;
            }
            foreach($answera as $value)
            {
                fwrite($fp, $value['str'].','.$value['deci']. PHP_EOL);
            }
            $offset+=$limit;
        }
        fclose($fp);
        return 1;
    }
}