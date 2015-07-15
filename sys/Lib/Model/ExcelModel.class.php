<?php 
class ExcelModel extends Model {
	public function output($info){
    
        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        /** Include PHPExcel */
       import("@.ORG.PHPExcel");


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                                     ->setLastModifiedBy("Maarten Balliauw")
                                     ->setTitle("Office 2007 XLSX Test Document")
                                     ->setSubject("Office 2007 XLSX Test Document")
                                     ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Test result file");


        // 设置格式
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '学号')
                    ->setCellValue('B1', '姓名')
                    ->setCellValue('C1', '来源')
                    ->setCellValue('D1', '联系方式')
                    ->setCellValue('E1', '申请国家')
                    ->setCellValue('F1', '语言成绩')
                    ->setCellValue('F2', 'IELTS分数')
                    ->setCellValue('G2', 'IBT分数')
                    ->setCellValue('H2', 'PTE分数')
                    ->setCellValue('I2', 'GRE分数')
                    ->setCellValue('J2', 'GMAT分数')
                    ->setCellValue('K1', '申请学校')
                    ->setCellValue('L1', '申请专业')
                    ->setCellValue('M1', '预科/本/硕')
                    ->setCellValue('N1', '咨询负责人')
                    ->setCellValue('O1', '文案负责人')
                    ->setCellValue('P1', '签证负责人')
                    ->setCellValue('Q1', '开学时间')
                    ->setCellValue('R1', '签证时间')
                    ->setCellValue('S1', '签证结果');
        $objPHPExcel->getActiveSheet()->mergeCells('F1:J1');
        $objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
        $objPHPExcel->getActiveSheet()->mergeCells('B1:B2');
        $objPHPExcel->getActiveSheet()->mergeCells('C1:C2');
        $objPHPExcel->getActiveSheet()->mergeCells('D1:D2');
        $objPHPExcel->getActiveSheet()->mergeCells('E1:E2');
        $objPHPExcel->getActiveSheet()->mergeCells('K1:K2');
        $objPHPExcel->getActiveSheet()->mergeCells('L1:L2');
        $objPHPExcel->getActiveSheet()->mergeCells('M1:M2');
        $objPHPExcel->getActiveSheet()->mergeCells('N1:N2');
        $objPHPExcel->getActiveSheet()->mergeCells('O1:O2');
        $objPHPExcel->getActiveSheet()->mergeCells('P1:P2');
        $objPHPExcel->getActiveSheet()->mergeCells('Q1:Q2');
        $objPHPExcel->getActiveSheet()->mergeCells('R1:R2');
        $objPHPExcel->getActiveSheet()->mergeCells('S1:S2');
        
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('K1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('L1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('M1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('N1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('O1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('P1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('Q1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('R1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('S1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        

//添加数据
        $id=2;//标记下一学生开始的行号
        foreach($info as $vo){ 
            $num=count($vo['school'])+$id;            
            if($num-$id<=1){
                 
                if(!empty($vo['school'])){
                    
                    foreach($vo['school'] as $my){
                            $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$num, $vo['susername'])  
                                            ->setCellValue('B'.$num, $vo['struename'])   
                                            ->setCellValue('C'.$num, $vo['from'])   
                                            ->setCellValue('D'.$num, $vo['link'])   
                                            ->setCellValue('E'.$num, $vo['country'])  
                                            ->setCellValue('F'.$num, $vo['score1'])   
                                            ->setCellValue('G'.$num, $vo['score2'])   
                                            ->setCellValue('H'.$num, $vo['score3']) 
                                            ->setCellValue('I'.$num, $vo['score4'])  
                                            ->setCellValue('J'.$num, $vo['score5'])   
                                            ->setCellValue('K'.$num, $my['school'])  
                                            ->setCellValue('L'.$num, $my['major'])  
                                            ->setCellValue('M'.$num, $vo['degree'])  
                                            ->setCellValue('N'.$num, $vo['tusername1'])  
                                            ->setCellValue('O'.$num, $vo['tusername2'])   
                                            ->setCellValue('P'.$num, $vo['tusername3']) 
                                            ->setCellValue('Q'.$num, $my['ktime'])  
                                            ->setCellValue('R'.$num, $vo['visatime'])   
                                            ->setCellValue('S'.$num, $vo['visaresult']);
                           
                }
            }else{
              $num+=1;
                            $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$num, $vo['susername'])  
                                            ->setCellValue('B'.$num, $vo['struename'])   
                                            ->setCellValue('C'.$num, $vo['from'])   
                                            ->setCellValue('D'.$num, $vo['link'])   
                                            ->setCellValue('E'.$num, $vo['country'])  
                                            ->setCellValue('F'.$num, $vo['score1'])   
                                            ->setCellValue('G'.$num, $vo['score2'])   
                                            ->setCellValue('H'.$num, $vo['score3']) 
                                            ->setCellValue('I'.$num, $vo['score4'])  
                                            ->setCellValue('J'.$num, $vo['score5'])                                                                                   
                                            ->setCellValue('M'.$num, $vo['degree'])  
                                            ->setCellValue('N'.$num, $vo['tusername1'])  
                                            ->setCellValue('O'.$num, $vo['tusername2'])   
                                            ->setCellValue('P'.$num, $vo['tusername3']) 
                                           
                                            ->setCellValue('R'.$num, $vo['visatime'])   
                                            ->setCellValue('S'.$num, $vo['visaresult']);   
            }
                 
                
                
            }else{
            //合并单元格
            $id=$id+1;
            for($s=65;$s<=74;$s++)
            {
                             
            
            $one= chr($s).$id;
            $two= chr($s).$num;
           
            $objPHPExcel->getActiveSheet()->mergeCells($one.':'.$two);
            $objPHPExcel->getActiveSheet()->getStyle($one)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            }
                $j=$id;
                foreach($vo['school'] as $my){
                        
                    
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$j, $vo['susername'])  
                                    ->setCellValue('B'.$j, $vo['struename'])   
                                    ->setCellValue('C'.$j, $vo['from'])   
                                    ->setCellValue('D'.$j, $vo['link'])   
                                    ->setCellValue('E'.$j, $vo['country'])  
                                    ->setCellValue('F'.$j, $vo['score1'])   
                                    ->setCellValue('G'.$j, $vo['score2'])   
                                    ->setCellValue('H'.$j, $vo['score3']) 
                                    ->setCellValue('I'.$j, $vo['score4'])  
                                    ->setCellValue('J'.$j, $vo['score5'])   
                                    ->setCellValue('K'.$j, $my['school'])  
                                    ->setCellValue('L'.$j, $my['major'])  
                                    ->setCellValue('M'.$j, $vo['degree'])  
                                    ->setCellValue('N'.$j, $vo['tusername1'])  
                                    ->setCellValue('O'.$j, $vo['tusername2'])   
                                    ->setCellValue('P'.$j, $vo['tusername3']) 
                                    ->setCellValue('Q'.$j, $my['ktime'])  
                                    ->setCellValue('R'.$j, $vo['visatime'])   
                                    ->setCellValue('S'.$j, $vo['visaresult']);   
                        $j++;
                                   
                }
                
            }
            
            $id=$num;
        }
 
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Info');


        // Set active sheet index to the first sheet, so Excel opens this as- the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.'data'.date("Y-m-d H:i:s").'"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;

    }
}
?>