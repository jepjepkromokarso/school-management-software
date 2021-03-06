<!DOCTYPE html>

<html>

<head>
	<style>
        body{font-family:Myriad Pro;color:#000;background-color:#ddd;margin:0;padding:0;font-size: 16px;}
input[type=text],textarea{border:1px solid #CFCECD}
table {
    border-collapse: collapse;border-spacing: 0; 
}



.header{}
.bg{background-color:#fff;width:595px;margin:0 auto 10px auto;padding-left:20px;padding-right:20px;border-radius:5px;}
.structure{width:595px;margin:0 auto;}
.fix{overflow:hidden;}
.logo{float:left;width:200px;}
.logo img{margin-top: 30px;margin-right:5px;width: 80px;}
.header_text{}
/*.header_text h1{color: #000000;font-size: 19px;line-height: 12px;margin-top: 40px;}*/
.header_text h1{color: #000000;font-size: 19px; text-align: center; margin: 0;}
.header_text h2{color: #000000;font-size: 15px;line-height: 5px;text-align: center;}
.upper_main_content{}
.header_title{margin-bottom: 0px;margin-top: 0px;}
.header_title h1{color: #000000;font-size: 20px;text-align: center;}
.header_title h2{color: #000000;font-size: 15px;text-align: center;}
.header_title span hr{margin-top: -12px;width: 181px;}
.header_title  hr{margin-top: -12px;width: 90px;}

/*.upper_main_content_left_side{float: left;width: 280px;}*/
.upper_main_content_left_side{float: left;width: 414px;}

.upper_main_content_left_side h1{font-size:15px;font-style: italic;}
.upper_main_content_left_side h2{font-style:italic;font-size:15px;line-height:10px;border-radius:10px;}
.upper_main_content_left_side h3{font-style:italic;font-size:15px;line-height:10px;border-radius:10px;}
.upper_main_content_right_side{float:right;}
.upper_main_content_right_side table{margin-top:0px;width:180px;}
.upper_main_content_right_side table tr,th{text-align:center;}

.clase_event{margin-bottom: 14px;margin-top: -70px;}
.clase_event table{border-radius: 20px 20px 20px 20px;width: 365px;}
.clase_event table th,td{text-align:center}

.upper_main_content_left_side{}
.main_content{float:center;margin-bottom:10px;}
.main_content table{width: 100%;height:10px;}

.second_table  table {}
.second_table  table td{}
.second_table  table td:first-child{font-weight: bold;width: 367px;}
.second_table  table td:last-child{width: 133px;}

.final_grade{border: 1px solid #000000;margin-bottom: 0px;margin-left: 1px;width: 588px;}
.final_grade form{float:left;}
.final_grade form p {float:left;margin-right:5px;}
.final_grade form p:first-child {margin-left:4px;}
.final_grade form p input[type=text] {margin-right: 0px;width: 35px;}
.remarks{padding:2px;}
.remarks textarea{border: 1px solid #000000;padding: 0px;width: 560px;}
.footer{margin-top:0px;}
.class_teacher{float: left;margin-right: 20px;margin-top: 20px;width: 170px;}
.class_teacher:first-child{float:left;margin-right:20px;margin-left:20px;}
.class_teacher:last-child{float:left;margin-left:0px;}
.class_teacher p{text-align:center;}

table.maintable { page-break-inside:auto }
tr    { page-break-inside:avoid; page-break-after:auto }

.maintable	{ display: block; page-break-after: always; }

        </style>
</head>

<body>
		
<?php   
$str = '';
if($_SESSION['result_arr'])
{
    
    $school_id = $_SESSION['school_id'];  
    #dumpVar($_SESSION['result_arr']);  
    
   $result= mysql_query("SELECT logo FROM scl_school_logo WHERE school_id = '$school_id' LIMIT 1");
     while($row=mysql_fetch_array($result))
    {
    	$img=$row['logo'];
    }
    
    foreach($_SESSION['result_arr'] as $k => $v)
    {
        #dumpVar($v);
        $student_id = $v[0]['student_id'];
        $school_id = $v[0]['school_id'];
        $totalInc = 0;       
        $school = $v[0]['school_name']; 
        $exam = $v[0]['exam_name']; 
        $name = $v[0]['name']; 
        $roll = $v[0]['roll_no']; 
        $class = $v[0]['classname']; 
        $section = $v[0]['section']; 
        $birthdate = date('Y-m-d',strtotime($v[0]['birthdate'])); 
        $date = date('Y-m-d',time());
        
        $remarks = $v[0]['remarks'];
        
        $total_working_days = $v[0]['total_working_days']; 
        $absent = $v[0]['present']; 
         

       // $total_working_days = $v[0]['total_working_days'] ? $v[0]['total_working_days'] : 0; 
      //  $absent = $v[0]['absent'] ? $v[0]['absent'] : 0; 
        $position = student_position($student_id,$school_id);
?>   
    <table cellpadding="0" cellspacing="0" width="100%" class="maintable" align="center">
        <tr>
            
            <td>
                <div class="bg">
                <div class="header structure fix">
            
            	    	
            
                    <div class="header_text">
                    
                    <div class="schoolImg">
			             <img width="136" height="81" style="" src="http://xenontech.net/school/images/logo/thumb/<?php echo $img; ?>" alt="">
		              </div>
                    
                    
                            <h1><?php echo $school?></h1>
                    </div>

                    </div>
                    <div class="upper_main_content structure fix">
                    <div class="header_title">
                            <h1>Academic Transcript</h1>
                            <span><hr></span>

                            <h2 style="padding:0px;margin-top: 0px;"><?php echo $exam;?></h2>
                            <div style="clear:both;"></div>
                            <hr>
                    </div>
                    <div class="upper_main_content_left_side fix">
                            
                        <table>
                            <tr>
                                <td style="text-align:left;">Student ID</td>
                               <td  style="text-align:left;" colspan="5">: <?php echo $v[0]['display_id']?></td>
                            </tr>
                            <tr>
                               <td  style="text-align:left;">Name</td>
                               <td  style="text-align:left;" colspan="5">: <?php echo $v[0]['name']?></td>
                            </tr>
                            <tr>
                               <td  style="text-align:left;">Class</td><td  style="text-align:left;">: <?php echo $class?>&nbsp;&nbsp;</td>
                               <td  style="text-align:left;">Section</td><td  style="text-align:left;">: <?php echo $section?>&nbsp;&nbsp;</td>
                               <td  style="text-align:left;">Roll</td><td  style="text-align:left;">: <?php echo $roll?></td>
                            </tr>
                        </table>  
                    </div>
                <div class="upper_main_content_right_side fix" style="font-size:10px;">
                            <table border="1">
                                    <tr>
                                            <th>Letter Grade</th>
                                            <th>Grade Point</th>
                                    </tr>
                                    <tr>
                                            <td>A+</td>
                                            <td>5</td>
                                    </tr>
                                    <tr>
                                            <td>A</td>
                                            <td>4.5</td>
                                    </tr>
                                    <tr>
                                            <td>A-</td>
                                            <td>4</td>
                                    </tr>
                                    <tr>
                                            <td>B+</td>
                                            <td>3.5</td>
                                    </tr><tr>
                                            <td>B</td>
                                            <td>3</td>
                                    </tr>
                                    <tr>
                                            <td>C</td>
                                            <td>2.5</td>
                                    </tr>
                                    <tr>
                                            <td>F</td>
                                            <td>0</td>
                                    </tr>
                            </table>
                    </div>


            </div>
                  <div style="clear:both;"></div>
                  <div style="height:10px;"></div>
                  <div style="clear:both;"></div>

            <div class="main_content structure fix">
                    <table border="1">
                            <tr>
                                    <th>Sl</th>
                                    <th>Subject</th>

                                    <th>Marks</th>
                                    <th>Letter Grade</th>
                                    <th>Grade Point</th>

                            </tr>
 
                            <?php
                            $inc = 0; 
                            $inc2 = 1;
                            foreach($v as $k2 => $v2)
                            {
                                $grade_id = $v2['grade_id'];
                                $gradeInfo = grade_with_points($grade_id);
                                $Obtained_GPA_v2 =  $gradeInfo['Obtained_GPA_v2'];
                                $Obtained_GPA =  $gradeInfo['Obtained_GPA']; 
                            ?>
                            <tr>
                                <td><?php echo $inc2?></td>
                                <td><?php echo $v2['subject']?></td>
                                <td><?php echo $v2['marks'];?></td>
                                <td><?php echo $Obtained_GPA;?></td>
                                <td><?php echo $Obtained_GPA_v2;?></td>                                    
                            </tr>
                            <?php
                                $totalInc = $totalInc + $v2['marks'];
                                $sGPA = $sGPA + $Obtained_GPA_v2;
                                $inc++;
                            }
                            $sGPA = $sGPA/($inc);
                            ?>                            
                    </table>
                    <div class="second_table fix">
                            <table border>
                                    <tr>
                                        <td colspan="2" style="text-align:left;">Total Marks : <?php echo $totalInc;?></td>
                                        <td colspan="3" style="text-align:left;" nowrap="nowrap">Grade Point Average(GPA) : <?php echo round($sGPA,2);?></td>
                                    </tr>
                            </table>
                    </div>


            </div>
            <div class="final_grade structure fix">
                    <form>
                            <p>Working days<input type="text" value="<?php echo $total_working_days?>"></p>
                            <p>Present <input type="text" value="<?php echo ($total_working_days-$absent);?>"></p>
                            <p>Position <input type="text" value="<?php echo $position ? $position : 'null';?>"></p>
                            <p>Final Grade <input type="text" value="<?php echo get_letter_grade($sGPA);?>"></p>
                    </form>

            </div>
            <div class="remarks structure fix">
                    <textarea><?php echo $remarks;?></textarea>
            </div>
            <div class="footer structure fix" >
                    <div class="class_teacher  fix">
                            <hr>
                            <p>Class Teacher</p>
                    </div>
                    <div class="class_teacher  fix">
                            <hr>
                            <p>Principal</p>
                    </div>
                    <div class="class_teacher  fix">
                            <hr>
                            <p>Guardian</p>

                    </div>

            </div>
	</div>  
                
            </td>
        </tr>
    </table>
<?php
    unset($sGPA,$inc,$inc2);
    }
}
?>   
</body>
</html>