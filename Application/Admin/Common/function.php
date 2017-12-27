<?php
/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author NineSongs
 */
function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * Created by NineSongs
 * Date: 15-08-27
 * 功能:分页
 * @param $count 数据的总数
 * @param $num 每页显示的数据数量
 * @return 分页数据
 */
// function gp_page($count,$num){
//     $page = new \Think\Page($count,$num);
//     return $page -> bj_show();
// }

/**
 * 检测用户是否登录
 * @return uid username mobile
 * @author NineSongs
 */
function is_login(){
    return session('admin_user');
}

/**
 * 获取对应key
 */
function get_string_array($key){
    $string_array = [
        '00:00' => 0,'01:00' => 1,'02:00' => 2,'03:00' => 3,'04:00' => 4,'05:00' => 5,'06:00' => 6,'07:00' => 7,'08:00' => 8,'09:00' => 9,'10:00' => 10,'11:00' => 11,'12:00' => 12,'13:00' => 13,'14:00' => 14,'15:00' => 15,'16:00' => 16,'17:00' => 17,'18:00' => 18,'19:00' => 19,'20:00' => 20,'21:00' => 21,'22:00' => 22,'23:00' => 23,'24:00' => 24,
    ];
    return $string_array[$key];
}

/**
 * 导出excel表
 * @param  string $fileTitle 生成文件名称
 * @param  string $title 名称
 * @return [type]        [description]
 */
function importExcelData($fileName,$title,$valueArray='',$tableTopArray=''){

    Vendor('phpexcel.PHPExcel');

    $objPHPExcel = new PHPExcel();

    // work start
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle($title);
    $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // Needs to be set to true in order to enable any worksheet protection!
    //$objPHPExcel->getActiveSheet()->protectCells('A3:E13', 'PHPExcel');


    // 处理表格开始
    // 表格头
    $cols = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $num = 1; // execl从1开始
    foreach ($tableTopArray as $k => $v) {
        $key_i = $cols{$k}.$num;
        $objPHPExcel->getActiveSheet()->setCellValue($key_i,$v); // 设置第一行为表头
        $objPHPExcel->getActiveSheet()->getStyle($key_i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // 第一行全部居中
        $objPHPExcel->getActiveSheet()->getStyle($key_i)->getFont()->setBold(true); // 第一行字体加粗
        $objPHPExcel->getActiveSheet()->getColumnDimension($key_i)->setAutoSize(true);
    }

    // 设置数据，表格具体数据
    foreach ($valueArray as $k => $v) {
        $i = $k+2;
        $j = 0;
        foreach ($v as $value) {
            $objPHPExcel->getActiveSheet()->setCellValue($cols{$j} . $i, $value);
            $j++;
        }
    }
    // 处理表格结束


    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=".$fileName.".xls");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}

