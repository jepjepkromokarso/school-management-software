<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Result extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin_model', 'Admin_model', true); 
        $this->load->library('pagination'); 
        if(!is_admin_loggedin())
        { 
            redirect('admin');exit;
        } 
        $this->load->model('result_model', 'Result_model', true); 
        $this->load->library('pagination');  
    }
    public function index()
    { 

    }
    public function edit_result()
    { 
       if(isPostBack())
       {
           $arr = $_POST['grade_id'];
           if($arr)
           {
              foreach($arr as $k => $v)
              {
                  $data['marks'] = $_POST['marks'][$k];
                  $data['grade_id'] = $_POST['grade_id'][$k];
                  $data['remarks'] = $_POST['remarks'][$k];
                  $student_result_id = $k;
                  
                  $this->Admin_model->common_update('scl_student_result',$data,$student_result_id,'student_result_id'); 
              }
           }
       }
        
        
        
        
       $class_name_id = 1;
       $section_id = 14;
       $class_id = 126;
       $school_id = $_SESSION['school_id'];
      
       $resultdate = '2013-05-01';
       
       
        
        
        
       $data['class'] = $this->Admin_model->get_distinct_class($_SESSION['school_id']); 
       $data['examList'] = $this->Admin_model->get_exam_list($_SESSION['school_id']); 
       $data['mainContent'] = $this->load->view('admin/result/edit_result', $data, true); 
       $this->load->view('admin/template', $data);
    }
    
    function get_section_ajax()
    {
        $class_name_id = $_POST['class_name_id'];          
        $school_id      = $_SESSION['school_id']; 
        
        $list = $this->Admin_model->get_class_section_list_rs($school_id,$class_name_id);  
        echo $list;
        exit; 
    }
    function get_subject_list_ajax()
    {
        $class_name_id  = $_POST['class_name_id'];  
        $section_id  = $_POST['section_id'];  
        $school_id      = $_SESSION['school_id']; 
        
        $list = $this->Admin_model->get_class_subject_list_rs($school_id,$class_name_id,$section_id);  
        echo $list;
        exit;
    }
    
    function edit_result_list_ajax()
    {
        $class_name_id  = $_POST['class_name_id'];  
        $section_id  = $_POST['section_id']; 
        $class_id  = $_POST['class_id']; 
        $exam_date  = $_POST['exam_date']; 
        $school_id      = $_SESSION['school_id']; 
        
        
        $sql = "SELECT s.student_id,
                      s.name,
                      s.roll_no,
                      r.marks,
                      r.grade_id,
                      r.remarks,
                      r.student_result_id
               FROM scl_student s
               INNER JOIN scl_student_result r ON r.student_id = s.student_id AND r.school_id = s.school_id
               WHERE r.class_name_id = $class_name_id
               AND r.section_id = $section_id
               AND r.class_id = $class_id
               AND r.school_id = $school_id
               AND r.exam_date = '$exam_date'
       ORDER BY s.roll_no ASC";
       
       $query = $this->db->query($sql);
       $data['studentInfo'] = $query->result_array();
       $result = $this->load->view('admin/result/result-edit-ajax', $data, true); 
              
        echo $result;
        exit;
    }
    
    public function XX_generate_pdf_ex()
    {
        $exam_id = $_SESSION['print_examselection'];
        $section_id = $_SESSION['section_id'];
        $class_name_id = $_SESSION['class_name_id'];
        $school_id = $_SESSION['school_id'];
        
        
        $sql = "SELECT DISTINCT student_id 
                FROM scl_student_result 
                WHERE class_name_id = $class_name_id 
                AND section_id = $section_id
                AND exam_id = $exam_id
                AND school_id = $school_id
                AND status = 1                
                ORDER BY student_id ASC";
        $query = $this->db->query($sql);
        $std = $query->result_array();
        unset($_SESSION['result_arr']);
        if($std)
        {
           $i=0;
           foreach($std as $s) 
           {                 
                 $student_id = $s['student_id'];
                 $sql = "SELECT r.marks,
                                r.grade_id,
                                r.remarks,
                                st.student_id,
                                st.name,
                                st.birthdate,
                                st.roll_no,
                                st.gurdian_name,
                                st.gurdian_phon_no,
                                s.school_id,
                                s.school_name,
                                s.mail_id,
                                s.sid,
                                e.exam_id,
                                e.exam_name,
                                sb.subject,
                                cls.classname,
                                sec.section,
                                'A+' as lettergrade,
                                (SELECT MAX(sq.marks) FROM scl_student_result sq WHERE sq.school_id = $school_id AND sq.exam_id = $exam_id AND sq.section_id = $section_id AND sq.class_name_id = $class_name_id) as heightmark
                           FROM scl_student_result r
                           INNER JOIN scl_student st ON st.student_id = r.student_id
                           INNER JOIN scl_school s ON s.school_id = r.school_id
                           INNER JOIN scl_exam e ON e.exam_id = r.exam_id                           
                           INNER JOIN scl_class sb ON sb.class_id = r.class_id                           
                           INNER JOIN scl_class_name cls ON cls.class_name_id = r.class_name_id                           
                           INNER JOIN scl_section sec ON sec.section_id = r.section_id
                           WHERE r.class_name_id = $class_name_id 
                           AND r.section_id = $section_id
                           AND r.exam_id = $exam_id
                           AND r.school_id = $school_id                           
                           AND s.status = 1
                           AND e.status = 1
                           AND r.student_id = $student_id";


                  $query = $this->db->query($sql);
                  $res = $query->result_array(); 
                  
                  $sql = "SELECT COUNT(attendance_id) as total_working_days,
                                 (SELECT COUNT(c.is_present) FROM scl_attendance c WHERE c.is_present = 0 AND c.section_id = $section_id  AND c.student_id = $student_id AND c.school_id = $school_id AND c.class_name_id = $class_name_id) as absent
                          FROM scl_attendance
                          WHERE section_id = $section_id  AND student_id = $student_id AND school_id = $school_id AND class_name_id = $class_name_id"; 
                  
                  $query = $this->db->query($sql);
                  $res2 = $query->result_array(); 
                  $res[0]['total_working_days'] = $res2['total_working_days'];
                  $res[0]['absent'] = $res2['absent'];
                  
                  
                  $_SESSION['result_arr'][$i] = $res;  
                  $i++;
           }
           #header('Location: '.BASEURL.'tcpdf/generate_pdf.php');exit;
           $this->load->view('admin/result/result-html', $data);
        } 
    }
    function set_student_result_summary($info)
    {
        if($info)
        {
           $inc = 0;  
           $sGPA = 0;   
           $marks = 0;
           foreach($info as $inf)
           {
               $grade_id = $inf['grade_id'];
               $gradeInfo = grade_with_points($grade_id);               
               $Obtained_GPA_v2 =  $gradeInfo['Obtained_GPA_v2'];
               $Obtained_GPA =  $gradeInfo['Obtained_GPA']; 
               
               $sGPA = $sGPA + $Obtained_GPA_v2;  
               $marks = $marks + $inf['marks'];
               $inc++;
           }
           $sGPA = $sGPA/($inc);
           $student_id = $info[0]['student_id'];
           $school_id = $info[0]['school_id'];
           $class_name_id = $info[0]['class_name_id'];
           $section_id = $info[0]['section_id'];
                   
           $sql = "INSERT INTO scl_result_summary (`result_summary_id`, `student_id`, `cgpa`, `position`,`school_id`,`class_name_id`,`section_id`,`marks`) VALUES (NULL, '$student_id', '$sGPA', '0','$school_id','$class_name_id','$section_id','$marks')";
           $this->db->query($sql);           
        }
    }
    public function generate_pdf_ex()
    {
        $exam_id = $_SESSION['print_examselection'];
        $section_id = $_SESSION['section_id'];
        $class_name_id = $_SESSION['class_name_id'];
        $school_id = $_SESSION['school_id'];
        
        $sql = "DELETE FROM scl_result_summary WHERE school_id = $school_id";
        $this->db->query($sql);
   
        $sql = "SELECT DISTINCT sr.student_id 
                FROM scl_student_result sr
                INNER JOIN scl_student s ON s.student_id = sr.student_id AND sr.school_id = s.school_id
                WHERE sr.class_name_id = $class_name_id 
                AND sr.section_id = $section_id
                AND sr.exam_id = $exam_id
                AND sr.school_id = $school_id
                AND sr.status = 1                
                ORDER BY s.roll_no ASC";
        $query = $this->db->query($sql);
        $std = $query->result_array();
        unset($_SESSION['result_arr']);
        if($std)
        {
           $i=0;
           foreach($std as $s) 
           {                 
                 $student_id = $s['student_id'];
                 $sql = "SELECT r.marks,
                                r.grade_id,
                                r.remarks,
                                st.student_id,
                                st.display_id,
                                st.name,
                                st.birthdate,
                                st.roll_no,
                                st.gurdian_name,
                                st.gurdian_phon_no,
                                s.school_id,
                                s.school_name,
                                s.mail_id,
                                s.sid,
                                e.exam_id,
                                e.exam_name,
                                sb.subject,
                                cls.classname,
                                sec.section,
                                'A+' as lettergrade,
                                (SELECT MAX(sq.marks) FROM scl_student_result sq WHERE sq.school_id = $school_id AND sq.exam_id = $exam_id AND sq.section_id = $section_id AND sq.class_name_id = $class_name_id) as heightmark
                           FROM scl_student_result r
                           INNER JOIN scl_student st ON st.student_id = r.student_id
                           INNER JOIN scl_school s ON s.school_id = r.school_id
                           INNER JOIN scl_exam e ON e.exam_id = r.exam_id                           
                           INNER JOIN scl_class sb ON sb.class_id = r.class_id                           
                           INNER JOIN scl_class_name cls ON cls.class_name_id = r.class_name_id                           
                           INNER JOIN scl_section sec ON sec.section_id = r.section_id
                           WHERE r.class_name_id = $class_name_id 
                           AND r.section_id = $section_id
                           AND r.exam_id = $exam_id
                           AND r.school_id = $school_id                           
                           AND s.status = 1
                           AND e.status = 1
                           AND r.student_id = $student_id";


                  $query = $this->db->query($sql);
                  $res = $query->result_array(); 
                  
                 /* $sql = "SELECT COUNT(attendance_id) as total_working_days,
                                 (SELECT COUNT(c.is_present) FROM scl_attendance c WHERE c.is_present = 0 AND c.section_id = $section_id  AND c.student_id = $student_id AND c.school_id = $school_id AND c.class_name_id = $class_name_id) as absent
                          FROM scl_attendance
                          WHERE section_id = $section_id  AND student_id = $student_id AND school_id = $school_id AND class_name_id = $class_name_id"; 
                  
                  $query = $this->db->query($sql);
                  $res2 = $query->result_array(); */
                  
                  $sql = "SELECT DISTINCT created_on
                FROM scl_attendance
                WHERE class_name_id = $class_name_id
                AND section_id = $section_id
                AND school_id = $school_id
                ";
	       $query = $this->db->query($sql);
	       $resT = $query->num_rows();
	       
	       $sqla = "SELECT DISTINCT created_on
                FROM scl_attendance
                WHERE student_id = $student_id
                AND class_name_id = $class_name_id
                AND section_id = $section_id
                AND school_id = $school_id
                AND is_present =0
                ";
       $querya = $this->db->query($sqla);
       $resa = $querya->num_rows();
                  
               $res[0]['total_working_days'] = $resT;
               $res[0]['present'] = $resa;   
                  
                /*  $res[0]['total_working_days'] = $res2['total_working_days'];
                  $res[0]['absent'] = $res2['absent']; */
                  
                  
                  $_SESSION['result_arr'][$i] = $res;  
                  $i++;
                  $this->set_student_result_summary($res);
           }
           $this->set_student_position1($school_id);
           #header('Location: '.BASEURL.'tcpdf/generate_pdf.php');exit;

           $this->load->view('admin/result/result-html', $data);
        } 
    }
    
    function send_sms_m()
    {
        $exam_id = $_SESSION['print_examselection'];
        $section_id = $_SESSION['section_id'];
        $class_name_id = $_SESSION['class_name_id'];
        $school_id = $_SESSION['school_id'];
        
        $sql = "DELETE FROM scl_result_summary WHERE school_id = $school_id";
        $this->db->query($sql);
        
        $sql = "SELECT DISTINCT student_id 
                FROM scl_student_result 
                WHERE class_name_id = $class_name_id 
                AND section_id = $section_id
                AND exam_id = $exam_id
                AND school_id = $school_id
                AND status = 1                
                ORDER BY student_id ASC";
        $query = $this->db->query($sql);
        $std = $query->result_array();
        unset($_SESSION['result_arr']);
        if($std)
        {
           $i=0;
           foreach($std as $s) 
           {                 
                 $student_id = $s['student_id'];
                 $sql = "SELECT r.marks,
                                r.grade_id,
                                r.remarks,
                                st.student_id,
                                st.name,
                                st.birthdate,
                                st.roll_no,
                                st.gurdian_name,
                                st.gurdian_phon_no,
                                s.school_id,
                                s.school_name,
                                s.mail_id,
                                s.sid,
                                e.exam_id,
                                e.exam_name,
                                sb.subject,
                                cls.classname,
                                sec.section,
                                'A+' as lettergrade,
                                (SELECT MAX(sq.marks) FROM scl_student_result sq WHERE sq.school_id = $school_id AND sq.exam_id = $exam_id AND sq.section_id = $section_id AND sq.class_name_id = $class_name_id) as heightmark
                           FROM scl_student_result r
                           INNER JOIN scl_student st ON st.student_id = r.student_id
                           INNER JOIN scl_school s ON s.school_id = r.school_id
                           INNER JOIN scl_exam e ON e.exam_id = r.exam_id                           
                           INNER JOIN scl_class sb ON sb.class_id = r.class_id                           
                           INNER JOIN scl_class_name cls ON cls.class_name_id = r.class_name_id                           
                           INNER JOIN scl_section sec ON sec.section_id = r.section_id
                           WHERE r.class_name_id = $class_name_id 
                           AND r.section_id = $section_id
                           AND r.exam_id = $exam_id
                           AND r.school_id = $school_id                           
                           AND s.status = 1
                           AND e.status = 1
                           AND r.student_id = $student_id";


                  $query = $this->db->query($sql);
                  $res = $query->result_array(); 
                  
                  $sql = "SELECT COUNT(attendance_id) as total_working_days,
                                 (SELECT COUNT(c.is_present) FROM scl_attendance c WHERE c.is_present = 0 AND c.section_id = $section_id  AND c.student_id = $student_id AND c.school_id = $school_id AND c.class_name_id = $class_name_id) as absent
                          FROM scl_attendance
                          WHERE section_id = $section_id  AND student_id = $student_id AND school_id = $school_id AND class_name_id = $class_name_id"; 
                  
                  $query = $this->db->query($sql);
                  $res2 = $query->result_array(); 
                  $res[0]['total_working_days'] = $res2['total_working_days'];
                  $res[0]['absent'] = $res2['absent'];
                  
                  
                  $_SESSION['result_arr'][$i] = $res;  
                  $i++;
                  $this->set_student_result_summary($res);
           }
           $this->set_student_position_m($school_id);
           
        }
    }
    function send_sms_g()
    {
        $exam_id = $_SESSION['print_examselection'];
        $section_id = $_SESSION['section_id'];
        $class_name_id = $_SESSION['class_name_id'];
        $school_id = $_SESSION['school_id'];
        
//        $sql = "DELETE FROM scl_result_summary WHERE school_id = $school_id";
        $sql = "DELETE FROM scl_result_summary WHERE school_id = $school_id AND class_name_id = $class_name_id AND section_id = $section_id";
        $this->db->query($sql);
        
        $sql = "SELECT DISTINCT student_id 
                FROM scl_student_result 
                WHERE class_name_id = $class_name_id 
                AND section_id = $section_id
                AND exam_id = $exam_id
                AND school_id = $school_id
                AND status = 1                
                ORDER BY student_id ASC";
        $query = $this->db->query($sql);
        $std = $query->result_array();
        unset($_SESSION['result_arr']);
        if($std)
        {
           $i=0;
           foreach($std as $s) 
           {                 
                 $student_id = $s['student_id'];
                 $sql = "SELECT r.marks,
                                r.grade_id,
                                r.remarks,
                                r.class_name_id,
                                r.section_id,
                                st.student_id,
                                st.name,
                                st.birthdate,
                                st.roll_no,
                                st.gurdian_name,
                                st.gurdian_phon_no,
                                s.school_id,
                                s.school_name,
                                s.mail_id,
                                s.sid,
                                e.exam_id,
                                e.exam_name,
                                sb.subject,
                                cls.classname,
                                sec.section,
                                'A+' as lettergrade,
                                (SELECT MAX(sq.marks) FROM scl_student_result sq WHERE sq.school_id = $school_id AND sq.exam_id = $exam_id AND sq.section_id = $section_id AND sq.class_name_id = $class_name_id) as heightmark
                           FROM scl_student_result r
                           INNER JOIN scl_student st ON st.student_id = r.student_id
                           INNER JOIN scl_school s ON s.school_id = r.school_id
                           INNER JOIN scl_exam e ON e.exam_id = r.exam_id                           
                           INNER JOIN scl_class sb ON sb.class_id = r.class_id                           
                           INNER JOIN scl_class_name cls ON cls.class_name_id = r.class_name_id                           
                           INNER JOIN scl_section sec ON sec.section_id = r.section_id
                           WHERE r.class_name_id = $class_name_id 
                           AND r.section_id = $section_id
                           AND r.exam_id = $exam_id
                           AND r.school_id = $school_id                           
                           AND s.status = 1
                           AND e.status = 1
                           AND r.student_id = $student_id";


                  $query = $this->db->query($sql);
                  $res = $query->result_array(); 
                  
                  $sql = "SELECT COUNT(attendance_id) as total_working_days,
                                 (SELECT COUNT(c.is_present) FROM scl_attendance c WHERE c.is_present = 0 AND c.section_id = $section_id  AND c.student_id = $student_id AND c.school_id = $school_id AND c.class_name_id = $class_name_id) as absent
                          FROM scl_attendance
                          WHERE section_id = $section_id  AND student_id = $student_id AND school_id = $school_id AND class_name_id = $class_name_id"; 
                  
                  $query = $this->db->query($sql);
                  $res2 = $query->result_array(); 
                  $res[0]['total_working_days'] = $res2['total_working_days'];
                  $res[0]['absent'] = $res2['absent'];
                  
                  
                  $_SESSION['result_arr'][$i] = $res;  
                  $i++;
                  $this->set_student_result_summary($res);
           }
           $this->set_student_position_g($school_id,$class_name_id,$section_id);
           
        }
    }
    /*function send_result_sms()
    {
        $school_id = $_SESSION['school_id'];
        
        $sql = "SELECT s.student_id,
                       s.name,
                       s.gurdian_phon_no,
                       s.email,
                       s.cellphone,
                       rs.cgpa,
                       rs.position,
                       sc.school_name,
                       sc.sid
               FROM scl_student s
               INNER JOIN scl_result_summary rs ON rs.student_id = s.student_id
               INNER JOIN scl_school sc ON sc.school_id = s.school_id
               WHERE s.school_id = $school_id";
        
        $query = $this->db->query($sql);
        $res = $query->result_array(); 
        

        if($res)
        {
            $url = ''; 
            $data['user'] = '';
            $data['pass'] = ''; 
            $inc = 0; 
            
            while($res)
            {
               $data['sid'] = $res[$inc]['sid']; 
               $data["sms[$inc][0]"] = $res[$inc]['gurdian_phon_no']; 

               $message .= 'Student :'.$res[$inc]['name'].', '; 
               $message .= 'GPA :'.$res[$inc]['cgpa'].', '; 
               $message .= 'Position :'.$res[$inc]['position']; 
               
               $data["sms[$inc][1]"] = $message;
               unset($res[$inc]);
               $inc++;
               $message = '';
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            #$info = curl_getinfo($ch);
            curl_close($ch); 
            echo 'MESSAGE SENT SUCCESSFULLY';
            exit;
        }
    }*/
    
    function send_result_sms_m()
    {
        $school_id = $_SESSION['school_id'];
        
        $sql = "SELECT s.student_id,
                       s.name,
                       s.gurdian_phon_no,
                       s.email,
                       s.cellphone,
                       rs.cgpa,
                       rs.position,
                       rs.marks,
                       sc.school_name,
                       sc.sid
               FROM scl_student s
               INNER JOIN scl_result_summary rs ON rs.student_id = s.student_id
               INNER JOIN scl_school sc ON sc.school_id = s.school_id
               WHERE s.school_id = $school_id";
        
        $query = $this->db->query($sql);
        $res = $query->result_array(); 
        

        if($res)
        {

         $school_short_name_sms = mysql_result(mysql_query("SELECT school_short_name FROM scl_school WHERE school_id = '$school_id' LIMIT 1"), 0);
            // $url = ''; 
            // $data['user'] = '';
            // $data['pass'] = ''; 
            $username=  '';
            $password=  '';
            $provider=  '';
            $balance_url= '';
            $send_url=  '';   
            $charset= 0;
            $originator=  $school_short_name_sms;            
            $showDLR= 0;
            $msgtype= '';
            $utc= 0;
            $fieldcnt=8;

            $inc = 0; 
            
            while($res)
            {
               $data['sid'] = $res[$inc]['sid']; 
               $data["sms[$inc][0]"] = '88'.$res[$inc]['gurdian_phon_no']; 

               $message .= 'Student :'.$res[$inc]['name'].', '; 
               $message .= 'Marks :'.$res[$inc]['marks'].', ';
               /* $message .= 'GPA :'.$res[$inc]['cgpa'].', ';  */
              /* $message .= 'Position :'.$res[$inc]['position']; */
               
               $data["sms[$inc][1]"] = $message;

              $msgtext= $data["sms[$inc][1]"];
              $phone= $data["sms[$inc][0]"];

              $fieldstring = "username=$username&password=$password&charset=$charset&msgtext=$msgtext&originator=$originator&phone=$phone&provider=$provider&showDLR=$showDLR&msgtype=$msgtype";

              $ch = curl_init();  
              curl_setopt($ch,CURLOPT_URL,$send_url);  
              curl_setopt($ch,CURLOPT_POST,$fieldcnt);  
              curl_setopt($ch,CURLOPT_POSTFIELDS,$fieldstring);  
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
              $res = curl_exec($ch);   
              curl_close($ch);


               unset($res[$inc]);
               $inc++;
               $message = '';
            }
            
            //newly added to save the total no. of sms send
            $school_id_sms = $_SESSION['school_id'];
            //check if the schoo already exists
            $school_exists_query = mysql_query("SELECT count(sent_id) FROM scl_sms_count WHERE school_id = $school_id_sms");
            while($row_s = mysql_fetch_array($school_exists_query))
            {
                $school_exists = $row_s['count(sent_id)'];
            }
            //if school dont exists then insert scholl and set no_of_sms to 0 initially
            if($school_exists < 1)
            {
                $insert_school['no_of_sms']= 0;
                $insert_school['school_id']= $school_id_sms;
                $this->Admin_model->common_insert($insert_school,'scl_sms_count');
            }
            //check the current no_of_sms sent and then update it
            $query_no_of_sms=mysql_query("SELECT no_of_sms FROM scl_sms_count WHERE school_id = $school_id_sms");
            while($row = mysql_fetch_array($query_no_of_sms))
            {
                $total_sms_sent = $row['no_of_sms'];
            }
            $sms_data['no_of_sms'] = $total_sms_sent + $inc;                
            $update_sent_sms = $this->Admin_model->common_update('scl_sms_count',$sms_data,$school_id_sms,'school_id');
            //new code finished here
            
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($ch, CURLOPT_POST, true);

            // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            // $output = curl_exec($ch);
            // #$info = curl_getinfo($ch);
            // curl_close($ch); 
            // echo 'MESSAGE SENT SUCCESSFULLY';
            exit;
        }
    }
    
    function send_result_sms_g()
    {
        $school_id = $_SESSION['school_id'];
        
        $sql = "SELECT s.student_id,
                       s.name,
                       s.gurdian_phon_no,
                       s.email,
                       s.cellphone,
                       rs.cgpa,
                       rs.position,
                       rs.marks,
                       sc.school_name,
                       sc.sid
               FROM scl_student s
               INNER JOIN scl_result_summary rs ON rs.student_id = s.student_id
               INNER JOIN scl_school sc ON sc.school_id = s.school_id
               WHERE s.school_id = $school_id";
        
        $query = $this->db->query($sql);
        $res = $query->result_array(); 
        

        if($res)
        {
         $school_short_name_sms = mysql_result(mysql_query("SELECT school_short_name FROM scl_school WHERE school_id = '$school_id' LIMIT 1"), 0);
            // $url = ''; 
            // $data['user'] = '';
            // $data['pass'] = ''; 

            $username=  '';
            $password=  '';
            $provider=  '';
            $balance_url= '';
            $send_url=  '';    
            $charset= 0;
            $originator=  $school_short_name_sms;            
            $showDLR= 0;
            $msgtype= '';
            $utc= 0;
            $fieldcnt=8;

            $inc = 0; 
            
            while($res)
            {
               $data['sid'] = $res[$inc]['sid']; 
               $data["sms[$inc][0]"] = '88'.$res[$inc]['gurdian_phon_no']; 

               $message .= 'Student :'.$res[$inc]['name'].', '; 
               /*$message .= 'Marks :'.$res[$inc]['marks'].', ';*/
               $message .= 'GPA :'.$res[$inc]['cgpa'].', '; 
               $message .= 'Position :'.$res[$inc]['position']; 
               
               $data["sms[$inc][1]"] = $message;


              $msgtext= $data["sms[$inc][1]"];
              $phone= $data["sms[$inc][0]"];

              $fieldstring = "username=$username&password=$password&charset=$charset&msgtext=$msgtext&originator=$originator&phone=$phone&provider=$provider&showDLR=$showDLR&msgtype=$msgtype";

              $ch = curl_init();  
              curl_setopt($ch,CURLOPT_URL,$send_url);  
              curl_setopt($ch,CURLOPT_POST,$fieldcnt);  
              curl_setopt($ch,CURLOPT_POSTFIELDS,$fieldstring);  
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
              $res = curl_exec($ch);   
              curl_close($ch);

               unset($res[$inc]);
               $inc++;
               $message = '';

            }
            
            //newly added to save the total no. of sms send
            $school_id_sms = $_SESSION['school_id'];
            //check if the schoo already exists
            $school_exists_query = mysql_query("SELECT count(sent_id) FROM scl_sms_count WHERE school_id = $school_id_sms");
            while($row_s = mysql_fetch_array($school_exists_query))
            {
                $school_exists = $row_s['count(sent_id)'];
            }
            //if school dont exists then insert scholl and set no_of_sms to 0 initially
            if($school_exists < 1)
            {
                $insert_school['no_of_sms']= 0;
                $insert_school['school_id']= $school_id_sms;
                $this->Admin_model->common_insert($insert_school,'scl_sms_count');
            }
            //check the current no_of_sms sent and then update it
            $query_no_of_sms=mysql_query("SELECT no_of_sms FROM scl_sms_count WHERE school_id = $school_id_sms");
            while($row = mysql_fetch_array($query_no_of_sms))
            {
                $total_sms_sent = $row['no_of_sms'];
            }
            $sms_data['no_of_sms'] = $total_sms_sent + $inc;                
            $update_sent_sms = $this->Admin_model->common_update('scl_sms_count',$sms_data,$school_id_sms,'school_id');
            //new code finished here
            
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($ch, CURLOPT_POST, true);

            // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            // $output = curl_exec($ch);
            // #$info = curl_getinfo($ch);
            // curl_close($ch); 
            // echo 'MESSAGE SENT SUCCESSFULLY';
            exit;
        } 
        
    }
    
    function set_student_position_m($school_id)
    {
        $sql = "SELECT * FROM scl_result_summary WHERE school_id = $school_id ORDER BY cgpa DESC";
        $query = $this->db->query($sql);
        $res = $query->result_array(); 
        $constant = 1;
        
        if($res)
        {
           foreach($res as $k =>$r)
           {
               $data['position'] = $constant + $k;               
               $this->db->where('result_summary_id',$r['result_summary_id']);
               $this->db->update('scl_result_summary',$data);
           }
        }
        
        $this->send_result_sms_m();
    }
    
    function set_student_position_g($school_id,$class_name_id,$section_id)
    {
        $sql = "SELECT * FROM scl_result_summary WHERE school_id = $school_id AND class_name_id = $class_name_id AND section_id = $section_id ORDER BY cgpa DESC";
        $query = $this->db->query($sql);
        $res = $query->result_array(); 
        $constant = 1;
        
        if($res)
        {
           foreach($res as $k =>$r)
           {
               $data['position'] = $constant + $k;               
               $this->db->where('result_summary_id',$r['result_summary_id']);
               $this->db->update('scl_result_summary',$data);
           }
        }
        
        $this->send_result_sms_g();
    }
    
    function set_student_position1($school_id)
    {
        $position_constants = 0;
        $sql = "SELECT DISTINCT cgpa FROM scl_result_summary WHERE school_id = $school_id ORDER BY cgpa DESC";
        $query = $this->db->query($sql);
        $res = $query->result_array(); 
        if($res)
        {
           foreach($res as $r1)
           {
              $cgpa = $r1['cgpa'];   
              $sql = "SELECT result_summary_id,cgpa,marks FROM scl_result_summary WHERE school_id = $school_id AND cgpa = $cgpa ORDER BY cgpa,marks DESC";              
              
              $query = $this->db->query($sql);
              $res2 = $query->result_array(); 
              if($res2)
              {
                 foreach($res2 as $r2)
                 {
                     $position = $position_constants + 1;  
                     $result_summary_id = $r2['result_summary_id'];
                     $sql = "UPDATE scl_result_summary SET position = '$position' WHERE result_summary_id = $result_summary_id LIMIT 1";  
                     $this->db->query($sql);
                     $position_constants++;  
                 }
              }              
           }                        
        }
    }
    
    
    function save_print_info()
    {
        $_SESSION['class_name_id'] = $_GET['class_name_id'];
        $_SESSION['section_id'] = $_GET['section_id'];
        $_SESSION['print_examselection'] = $_GET['print_examselection'];
        
        $result = 'success';
        $return = array('result' => $result);
        print json_encode($return);
        exit; 
    }
    
    function get_exam_name_list_ajax()
    {
        $class_name_id = $_POST['class_name_id'];
        $section_id = $_POST['section_id'];
        $class_id = $_POST['class_id'];
        $school_id = $_SESSION['school_id'];
        if($class_id)
        {
            $sql = "SELECT DISTINCT exam_id FROM scl_student_result
                    WHERE class_name_id = $class_name_id
                    AND section_id = $section_id
                    AND class_id = $class_id
                    AND school_id =  $school_id";
            $query = $this->db->query($sql);
            $res = $query->result_array(); 
            $condition = '';
            if($res)
            {
                foreach($res as $r)
                {
                    $rArr[] = $r['exam_id'];
                }
                if($rArr)
                {
                   $comma_sepeated_ids = implode(',',$rArr); 
                   $condition = "AND exam_id NOT IN ($comma_sepeated_ids)";
                }
            }
        }
        
        $sql = "SELECT * FROM scl_exam WHERE school_id = $school_id $condition";   
        //echo $sql;exit;
        $query = $this->db->query($sql);
        $res = $query->result_array(); 
        $str = '<option value="">Select One</option>';
        if($res)
        {
            foreach($res as $r)
            {
                $str .= '<option value="'.$r['exam_id'].'">'.$r['exam_name'].'</option>';
            }
        }
        
        echo $str;exit;
    }
}