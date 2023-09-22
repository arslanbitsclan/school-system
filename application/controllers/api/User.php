<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH. '/libraries/SignatureInvalidException.php';
require APPPATH . '/libraries/JWT.php';
require APPPATH. '/libraries/BeforeValidException.php';
require APPPATH. '/libraries/ExpiredException.php';

use Restserver\Libraries\REST_Controller;
use \Firebase\JWT\JWT;

class User extends REST_Controller {

    function __construct() {

        // Construct the parent class
        parent::__construct();

        $this->methods['user_get']['limit'] = 500000; // 500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100000; // 100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; // 50 requests per hour per user/key
        $this->load->helper('api');
         $this->load->helper('general');


    }

    public function leave_post() 
    {

        $token = $this->input->get_request_header('Authorization');
        $token = explode(' ', $token)[1];
        
        try{
            $user_data = JWT::decode($token, "user_auth",array('HS256'));
            $leave_category = $this->input->post('leave_category');
            $daterange = $this->input->post('daterange');
            $reason = $this->input->post('reason');

            $this->form_validation->set_rules('leave_category', 'leave_category', 'required');
            $this->form_validation->set_rules('daterange', 'daterange', 'required');
            $this->form_validation->set_rules('reason','reason', 'required');
            if ($this->form_validation->run() === false) {
                $this->response(array('status' => false, 'message' => $_POST), REST_Controller::HTTP_NOT_FOUND);
            }else{

                $daterange = explode(' - ', $daterange);
                $start_date = date("Y-m-d", strtotime($daterange[0]));
                $end_date = date("Y-m-d", strtotime($daterange[1]));
                $apply_date = date("Y-m-d H:i:s");
                $datetime1 = new DateTime($start_date);
                $datetime2 = new DateTime($end_date);
                $leave_days = $datetime2->diff($datetime1)->format("%a") + 1;
                $orig_file_name = '';
                $enc_file_name = '';


                if (isset($_FILES["attachment_file"]) && !empty($_FILES['attachment_file']['name'])) {
                    $config['upload_path']      = './uploads/attachments/leave/';
                    $config['allowed_types']    = "gif|jpg|jpeg|png|pdf";
                    $config['max_size']         = '2024';
                    $config['encrypt_name']     = true;
                    $this->upload->initialize($config);
                    
                    if($this->upload->do_upload("attachment_file")){
                        $orig_file_name = $this->upload->data('orig_name');
                        $enc_file_name  = $this->upload->data('file_name');

                        $leaveData = array(
                            'user_id'           => $user_data->loggedin_userid,
                            'role_id'           => $user_data->loggedin_role_id,
                            'session_id'        => $user_data->set_session_id,
                            'category_id'       => $leave_category,
                            'reason'            => $reason,
                            'branch_id'         => $user_data->loggedin_branch,
                            'start_date'        => date("Y-m-d", strtotime($start_date)),
                            'end_date'          => date("Y-m-d", strtotime($end_date)),
                            'leave_days'        => $leave_days,
                            'status'            => 1,
                            'orig_file_name'    => $orig_file_name,
                            'enc_file_name'     => $enc_file_name,
                            'apply_date'        => $apply_date,
                        );
                        $res = $this->db->insert('leave_application', $leaveData);
                        if($res){
                            $this->set_response(array('status' => TRUE, 'message' => translate('leave_requested_successfully')), REST_Controller::HTTP_OK);
                        }else{
                            $this->response(array('status' => FALSE, 'message' => translate('something_went_wrong')), REST_Controller::HTTP_NOT_FOUND);
                        }

                    }else{
                        
                        $this->response(array('status' => false, 'message' => $this->upload->display_errors()), REST_Controller::HTTP_NOT_FOUND);

                    }

                }

            }
 
        } catch (Exception $ex) {
            $response = array("status" => FALSE,"message" => 'Unauthorized token');
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        
        }   
    }

    public function leaves_get()
    {
        $token = $this->input->get_request_header('Authorization');
        $token = explode(' ', $token)[1];
        
        try{
            $user_data = JWT::decode($token, "user_auth",array('HS256'));

            $user_id = $this->get('userID');
            $user_role = $this->get('userRole');

            $this->db->select('la.*,c.name as category_name,r.name as role');
            $this->db->from('leave_application as la');
            $this->db->join('leave_category as c', 'c.id = la.category_id', 'left');
            $this->db->join('roles as r', 'r.id = la.role_id', 'left');
            $this->db->where('la.user_id', $user_id);
            $this->db->where('la.role_id', $user_role);
            $leaves =  $this->db->get()->result();

            if(!empty($leaves)){

                $this->set_response(array('status' => TRUE, 'leaves' => $leaves), REST_Controller::HTTP_OK);
            }else{
                $this->response(array('status' => FALSE, 'message' => translate('no_record_found')), REST_Controller::HTTP_NOT_FOUND);
            }

        } catch (Exception $ex) {
            $response = array("status" => FALSE,"message" => 'Unauthorized token');
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);

        }
    }

    public function events_get()
    {
        $token = $this->input->get_request_header('Authorization');
        $token = explode(' ', $token)[1];
        
        try{
            $user_data = JWT::decode($token, "user_auth",array('HS256'));

            $date = $this->get('date');

            if(empty($date)){
                $this->response(array('status' => FALSE, 'message' => translate('date_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->db->where('start_date', $date);  
                $this->db->where('branch_id', $user_data->loggedin_branch);       
                $this->db->order_by('id', 'desc');
                $events = $this->db->get('event')->row();

                if(!empty($events)){

                    $events->image_path = base_url('uploads/frontend/events/'.$events->image);
                    $events->remark = strip_tags($events->remark);
                    $this->set_response(array('status' => TRUE, 'events' => $events), REST_Controller::HTTP_OK);
                }else{
                    $this->response(array('status' => FALSE, 'message' => translate('no_record_found')), REST_Controller::HTTP_NOT_FOUND);
                }
            }

        } catch (Exception $ex) {
            $response = array("status" => FALSE,"message" => 'Unauthorized token');
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        
        }
    }

    public function examSchedule_get()
    {
        $token = $this->input->get_request_header('Authorization');
        $token = explode(' ', $token)[1];
        
        try{
            $user_data = JWT::decode($token, "user_auth",array('HS256'));

            $classID = $this->get('classID');
            $sectionID = $this->get('sectionID');

            if(empty($classID)){
                $this->response(array('status' => FALSE, 'message' => translate('please_add_class_id')), REST_Controller::HTTP_NOT_FOUND);
            }else if(empty($sectionID)){
                $this->response(array('status' => FALSE, 'message' => translate('please_add_section_id')), REST_Controller::HTTP_NOT_FOUND);
            }else {

                $this->db->select('t.*,b.name as branch_name');
                $this->db->from('timetable_exam as t');
                $this->db->join('branch as b', 'b.id = t.branch_id', 'left');
                $this->db->where('t.class_id', $classID);
                $this->db->where('t.section_id', $sectionID);
                $this->db->order_by('t.id', 'asc');
                $this->db->group_by('t.exam_id');
                $exams =  $this->db->get()->result();

                if(!empty($exams)){

                    foreach ($exams as $e => $exam) {
                        $mark_distribution = json_decode($exam->mark_distribution, true);
                        $mark_distribution = array_values($mark_distribution);

                        $exam->full_mark = $mark_distribution[0]['full_mark'];
                        $exam->pass_mark = $mark_distribution[0]['pass_mark'];

                        $exam->exam_name = exam_name_by_id($exam->exam_id);
                        $exam->class_name = class_name_by_id($exam->class_id);
                        $exam->section_name = section_name_by_id($exam->section_id);
                        $exam->hall_name = hall_name_by_id($exam->hall_id);
                        unset($exam->mark_distribution);
                    }


                    $this->set_response(array('status' => TRUE, 'exams' => $exams), REST_Controller::HTTP_OK);
                }else{
                    $this->response(array('status' => FALSE, 'message' => translate('no_record_found')), REST_Controller::HTTP_NOT_FOUND);
                }
            }



        } catch (Exception $ex) {
            $response = array("status" => FALSE,"message" => 'Unauthorized token');
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        
        }
    }

    public function examScheduleDetails_get()
    {
        $token = $this->input->get_request_header('Authorization');
        $token = explode(' ', $token)[1];
        
        try{
            $user_data = JWT::decode($token, "user_auth",array('HS256'));

            $user_id = $this->get('userID'); 
            $classID = $this->get('classID');
            $sectionID = $this->get('sectionID');
            $examID = $this->get('examID');


            if(empty($user_id)){
                $this->response(array('status' => FALSE, 'message' => translate('user_id_is_missing.')), REST_Controller::HTTP_NOT_FOUND);

            }else if(empty($classID)){
                $this->response(array('status' => FALSE, 'message' => translate('class_id_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            }else if(empty($sectionID)){
                $this->response(array('status' => FALSE, 'message' => translate('section_id_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            }else if(empty($examID)){
                $this->response(array('status' => FALSE, 'message' => translate('exam_id_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            }else{

                $this->db->select('t.*,s.name as subject_name,eh.hall_no,m.mark as obtain_marks');
                $this->db->from('timetable_exam as t');
                $this->db->join('subject as s', 's.id = t.subject_id', 'left');
                $this->db->join('mark as m', 'm.subject_id = t.subject_id', 'left');
                $this->db->join('exam_hall as eh', 'eh.id = t.hall_id', 'left');
                $this->db->where('t.exam_id', $examID);
                $this->db->where('t.class_id', $classID);
                $this->db->where('t.section_id', $sectionID);
                $this->db->where('m.student_id', $user_id);
                $exam_details =  $this->db->get()->result();

                $response =  array();
                $output = array();
                $total_obtain_marks = 0;
                $total_full_marks = 0;

                if(!empty($exam_details)){

                    foreach ($exam_details as $e => $exam) {
                        $mark_distribution = json_decode($exam->mark_distribution, true);
                        $obtainedMark = json_decode($exam->obtain_marks, true);

                        $mark_distribution = array_values($mark_distribution);
                        $obtainedMark = array_values($obtainedMark);

                        $response[$e]['full_mark'] = $mark_distribution[0]['full_mark'];
                        $response[$e]['pass_mark'] = $mark_distribution[0]['pass_mark'];
                        $response[$e]['obtain_mark'] = $obtainedMark[0];
                        $response[$e]['exam_name'] = exam_name_by_id($exam->exam_id);
                        $response[$e]['class_name'] = class_name_by_id($exam->class_id);
                        $response[$e]['section_name'] = section_name_by_id($exam->section_id);
                        $response[$e]['subject_name'] = $exam->subject_name;
                        $response[$e]['hall_no'] = $exam->hall_no;
                        $response[$e]['time_start'] = $exam->time_start;
                        $response[$e]['time_end'] = $exam->time_end;
                        $total_obtain_marks+=$response[$e]['obtain_mark'];
                        $total_full_marks+=$response[$e]['full_mark'];

                    }
                    $percentage = ($total_obtain_marks * 100) / $total_full_marks;
                    $avg = number_format($percentage, 2, '.', '') . '%';

                    $output = array(
                        'exams_details' => $response,
                        'grand_full_marks' => $total_full_marks,
                        'grand_obtain_marks' => $total_obtain_marks,
                        'avg' => $avg
                    );


                    $this->set_response(array('status' => TRUE, 'exams' => $output), REST_Controller::HTTP_OK);
                }else{
                    $this->response(array('status' => FALSE, 'message' => translate('no_record_found')), REST_Controller::HTTP_NOT_FOUND);
                }
            }

        } catch (Exception $ex) {
            $response = array("status" => FALSE,"message" => 'Unauthorized token');
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        
        }
    }


    public function homework_get()
    {
        $token = $this->input->get_request_header('Authorization');
        $token = explode(' ', $token)[1];
        
        try{
            $user_data = JWT::decode($token, "user_auth",array('HS256'));

            $user_id = $this->get('userID');
            $date = $this->get('date');

            if(empty($user_id)){
                $this->response(array('status' => FALSE, 'message' => translate('user_id_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            } else if(empty($date)){
                $this->response(array('status' => FALSE, 'message' => translate('date_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            }else{

                $user_details = get_user_enroll($user_id);

                $classID = $user_details['class_id'];
                $sectionID = $user_details['section_id'];


                $this->db->select('homework.*,subject.name as subject_name,class.name as class_name,section.name as section_name,staff.name as creator_name');
                $this->db->from('homework');
                $this->db->join('subject', 'subject.id = homework.subject_id', 'left');
                $this->db->join('class', 'class.id = homework.class_id', 'left');
                $this->db->join('section', 'section.id = homework.section_id', 'left');
                $this->db->join('staff', 'staff.id = homework.created_by', 'left');
                $this->db->where('homework.class_id', $classID);
                $this->db->where('homework.section_id', $sectionID);
                $this->db->where('homework.date_of_homework', $date);
                $this->db->order_by('homework.id', 'desc');
                $homeworks = $this->db->get()->result();

                if(!empty($homeworks)){
                    foreach ($homeworks as $key => $homework) {
                        
                        $name     = get_type_name_by_id('homework', $homework->id, 'document');
                        $ext      = explode(".", $name);
                        $homework->document_path = base_url('uploads/attachments/homework/'.$homework->id. '.' . $ext[1]);
                        $homework->description = strip_tags($homework->description);
                    }
                    
                    $this->set_response(array('status' => TRUE, 'homework' => $homeworks), REST_Controller::HTTP_OK);
                }else{
                    $this->response(array('status' => FALSE, 'message' => translate('no_record_found')), REST_Controller::HTTP_NOT_FOUND);
                }
            }

        } catch (Exception $ex) {
            $response = array("status" => FALSE,"message" => 'Unauthorized token');
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);

        }
    }


    public function attendance_get()
    {
        $token = $this->input->get_request_header('Authorization');
        $token = explode(' ', $token)[1];
        
        try{
            $user_data = JWT::decode($token, "user_auth",array('HS256'));

            $user_id = $this->get('userID');
            $timestamp = $this->get('timestamp');

            if(empty($user_id)){
                $this->response(array('status' => FALSE, 'message' => translate('user_id_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            }else if (empty($timestamp)) {
                $this->response(array('status' => FALSE, 'message' => translate('timestamp_is_missing..')), REST_Controller::HTTP_NOT_FOUND);
            }
            else{

                $total_present = 0;
                $total_absent = 0;
                $total_late = 0;
                $total_hospital = 0;
                $response = array();

                $response['month'] = $month = date('m', strtotime($this->get('timestamp')));
                $response['year'] = $year = date('Y', strtotime($this->get('timestamp')));
                $response['days'] = $days = cal_days_in_month(CAL_GREGORIAN, $response['month'], $response['year']);

                for ($i = 1; $i <= $days; $i++) {
                    $attendance = new stdClass();
                    $date = date('Y-m-d', strtotime($year . '-' . $month . '-' . $i));

                    $this->db->select('*');
                    $this->db->from('student_attendance');
                    $this->db->where('student_id', 6);
                    $this->db->where('date', $date);
                    $atten =  $this->db->get()->row_array();

                    if ($atten['status'] == 'A') {
                        $total_absent++;
                    } else if ($atten['status'] == 'P') {
                        $total_present++;
                    } else if ($atten['status'] == 'L') {
                        $total_late++;
                    } else if ($atten['status'] == 'H') {
                        $total_hospital++;
                    }

                    if ($atten['status'] == '') {
                        $attendance->status = '-';
                    } else {
                        $attendance->status = $atten['status'];
                    }

                    $attendance->remark = "{$atten['remark']}";
                    $adate = $atten['date'];
                    $cdate = $atten['created_at'];
                    $time = new DateTime($cdate);
                    $date = $time->format('d-m-Y');
                    $time = $time->format('H:i');

                    $attendance->attendance_date = "{$adate}";
                    $attendance->creation_date = "{$date}";
                    $attendance->creation_time = "{$time}";

                    $response['attendance'][] = $attendance;
                }


                $response['total_absent'] = $total_absent;
                $response['total_present'] = $total_present;
                $response['total_late'] = $total_late;
                $response['total_hospital'] = $total_hospital;

                $this->set_response(array('status' => TRUE, 'attendance' => $response), REST_Controller::HTTP_OK);
            }
            

        } catch (Exception $ex) {
            $response = array("status" => FALSE,"message" => 'Unauthorized token');
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        
        }
    }

    public function studentprofile_get()
    {
        $token = $this->input->get_request_header('Authorization');
        $token = explode(' ', $token)[1];
        
        try{
            $user_data = JWT::decode($token, "user_auth",array('HS256'));

            $student_id = $this->get('student_id');

            if(empty($student_id)){
                $this->response(array('status' => FALSE, 'message' => translate('student_id_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            } else {

                $this->db->select('s.register_no,s.admission_date,s.first_name,s.last_name,s.gender,s.birthday,s.religion,s.caste,s.blood_group,s.mother_tongue,s.current_address,s.permanent_address,s.city,s.state,s.mobileno,s.category_id,s.email,s.parent_id,s.route_id,s.vehicle_id,s.hostel_id,s.room_id,s.previous_details,s.photo,s.created_at,s.updated_at
                    ,l.username,l.active,e.class_id,e.section_id,e.id as enrollid,e.roll,e.branch_id,e.session_id,c.name as class_name,se.name as section_name,sc.name as category_name');
                $this->db->from('enroll as e');
                $this->db->join('student as s', 'e.student_id = s.id', 'left');
                $this->db->join('login_credential as l', 'l.user_id = s.id and l.role = 7', 'inner');
                $this->db->join('class as c', 'e.class_id = c.id', 'left');
                $this->db->join('section as se', 'e.section_id = se.id', 'left');
                $this->db->join('student_category as sc', 's.category_id=sc.id', 'left');
                $this->db->where('s.id', $student_id);
                $student =  $this->db->get()->row();

                if(!empty($student)){
                    $this->set_response(array('status' => TRUE, 'student' => $student), REST_Controller::HTTP_OK);
                }else{
                    $this->response(array('status' => FALSE, 'message' => translate('no_record_found')), REST_Controller::HTTP_NOT_FOUND);
                }
            }

        } catch (Exception $ex) {
            $response = array("status" => FALSE,"message" => 'Unauthorized token');
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        
        }
    }


    public function parentprofile_get() {
       $token = $this->input->get_request_header('Authorization');
        $token = explode(' ', $token)[1];
        
        try{
            $user_data = JWT::decode($token, "user_auth",array('HS256'));

            $id = $this->get('id');

            if(empty($id)){
                $this->response(array('status' => FALSE, 'message' => translate('id_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            }else{

                $this->db->select('parent.*,login_credential.role as role_id,login_credential.active,login_credential.username,login_credential.id as login_id, roles.name as role');
                $this->db->from('parent');
                $this->db->join('login_credential', 'login_credential.user_id = parent.id and login_credential.role = "6"', 'inner');
                $this->db->join('roles', 'roles.id = login_credential.role', 'left');
                $this->db->where('parent.id', $id);
                $response['parent'] = $this->db->get()->result();


                $this->db->select('s.id,s.photo, CONCAT(s.first_name, " ", s.last_name) as fullname,c.name as class_name,se.name as section_name');
                $this->db->from('enroll as e');
                $this->db->join('student as s', 'e.student_id = s.id', 'inner');
                $this->db->join('login_credential as l', 'l.user_id = s.id and l.role = 7', 'inner');
                $this->db->join('class as c', 'e.class_id = c.id', 'left');
                $this->db->join('section as se', 'e.section_id=se.id', 'left');
                $this->db->where('s.parent_id', $id);
                $this->db->where('l.active', 1);
                $response['childs'] = $this->db->get()->result();

                $this->set_response(array('status' => TRUE, 'profile' => $response), REST_Controller::HTTP_OK);
            }
            
        } catch (Exception $ex) {
            $response = array("status" => FALSE,"message" => 'Unauthorized token');
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        
        }
       
    }

    public function allStudents_get() {
       $token = $this->input->get_request_header('Authorization');
        $token = explode(' ', $token)[1];
        
        try{
            $user_data = JWT::decode($token, "user_auth",array('HS256'));

            $branchID = $this->get('branchID');
            $classID = $this->get('classID');

            if(empty($branchID)){
                $this->response(array('status' => FALSE, 'message' => translate('branch_id_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            }else if(empty($classID)){
                $this->response(array('status' => FALSE, 'message' => translate('class_id_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            }else{


                $sql = "SELECT e.*, s.photo, CONCAT(s.first_name, ' ', s.last_name) as fullname, s.register_no, s.parent_id, s.email, s.mobileno, s.blood_group, s.birthday, s.admission_date, l.active, c.name as class_name, se.name as section_name, sc.name as category FROM enroll as e INNER JOIN student as s ON e.student_id = s.id INNER JOIN login_credential as l ON l.user_id = s.id and l.role = 7 LEFT JOIN class as c ON e.class_id = c.id LEFT JOIN section as se ON e.section_id=se.id LEFT JOIN student_category as sc ON sc.id=s.category_id WHERE e.class_id = " . $this->db->escape($classID) . " AND e.branch_id = " . $this->db->escape($branchID) ."";

                $students =  $this->db->query($sql)->result();

                if(!empty($students)){

                    $this->set_response(array('status' => TRUE, 'students' => $students), REST_Controller::HTTP_OK);
                }else{
                    $this->response(array('status' => FALSE, 'message' => translate('no_record_found')), REST_Controller::HTTP_NOT_FOUND);
                }
            }
 
        } catch (Exception $ex) {
            $response = array("status" => FALSE,"message" => 'Unauthorized token');
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
        
        }
       
    }


    public function fees_get() {
        $this->load->model('fees_model');
        $token = $this->input->get_request_header('Authorization');
        $token = explode(' ', $token)[1];
        
        try{
            $user_data = JWT::decode($token, "user_auth",array('HS256'));
            $timestamp = $this->get('timestamp');
            $student_id = $this->get('userID');

            if(empty($timestamp)){
                $this->response(array('status' => FALSE, 'message' => translate('timestamp_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            }else if(empty($student_id)){
                $this->response(array('status' => FALSE, 'message' => translate('user_id_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            }else{

                $response = array();
                $month = date('m', strtotime($this->get('timestamp')));
                $year = date('Y', strtotime($this->get('timestamp')));

                $this->db->select('fee_vouchers.*');
                $this->db->from('fee_vouchers');
                $this->db->join('fee_voucher_months','fee_voucher_months.fee_voucher_id = fee_vouchers.id');
                $this->db->where('fee_vouchers.student_id', $student_id);
                $this->db->where('fee_voucher_months.fee_month', $month);
                $this->db->where('fee_voucher_months.fee_year', $year);
                $voucher = $this->db->get()->row();
                if(!empty($voucher)){

                    $this->db->select('*');
                    $this->db->from('fee_voucherables');
                    $this->db->where('voucher_id', $voucher->id);
                    $voucherables = $this->db->get()->result();

                    $total_fine = 0;
                    $total_discount = 0;
                    $total_paid = 0;
                    $total_balance = 0;
                    $total_amount = 0;
                    $previous_open_balance = previous_open_balance($voucher->student_id);

                    if($voucher->carry_balance == 1){
                        $previous_balance = previous_balance($voucher->student_id);
                    }else{
                        $previous_balance = 0;
                    }

                    $response['fee_heads'] = array();
                    foreach ($voucherables as $row) {

                        $fee_head = array(
                            'fee_head' => $row->fee_head,
                            'amount' => $row->amount 
                        );

                        array_push($response['fee_heads'],$fee_head);

                        $type_discount = 0;
                        $deposit = $this->fees_model->getStudentFeeDeposit($row->allocation_id, $row->fee_type_id);

                        $check_month = check_voucher_month($voucher->id,$row->fee_type_id);
                        $discount_info = get_fee_type_discount($voucher->student_id,$row->fee_type_id);

                        if(!empty($discount_info)){
                            if(!empty($discount_info->discount)){
                                $type_discount = number_format(($discount_info->discount*$check_month), 2, '.', '');

                            }else{
                                $total_discount += 0;
                                $type_discount = number_format(0, 2, '.', '');
                            }
                        }

                        $type_fine = number_format(0, 2, '.', '');
                        $fine = getBalanceByType($row->allocation_id, $row->fee_type_id,$voucher->voucher_no);

                        if($fine > 0){
                            $type_fine =  number_format($fine, 2, '.', '');
                        }

                        $balance = ($row->amount*$check_month - $type_discount) + $type_fine;
                        $total_discount += $type_discount;
                        $total_fine += $type_fine;
                        $total_balance += $balance;
                        $total_amount += $row->amount*$check_month;

                        if ($balance != 0) {
                            $typeData[$row->allocation_id . "|" . $row->fee_type_id] = $row->fee_head;
                        }
                    }

                    $fee_amount = number_format($total_balance-$total_paid+$previous_balance+$previous_open_balance, 2, '.', '');


                    $this->db->select('*');
                    $this->db->from('fee_voucher_payments');
                    $this->db->where('voucher_id', $voucher->id);
                    $payment = $this->db->get()->row();

                    $total_due = 0;
                    if(!empty($payment)){
                        $fee_amount = $payment->amount;
                        $total_paid = $payment->total_paid;
                        $total_discount = $payment->total_discount;
                        $total_fine = $payment->total_fine;
                        $total_due = $payment->total_due;
                        $previous_open_balance = $payment->previous_opening_balance;
                        $previous_balance = $payment->previous_balance;
                    }

                    $response['total_payable_amount'] = number_format($fee_amount,2, '.', '');
                    $response['total_discount'] = number_format($total_discount,2, '.', '');
                    $response['total_paid'] = number_format($total_paid,2, '.', '');
                    $response['total_fine'] = number_format($total_fine,2, '.', '');
                    $response['total_due'] = number_format($total_due,2, '.', '');
                    $response['previous_open_balance'] = number_format($previous_open_balance,2, '.', '');
                    $response['previous_balance'] = number_format($previous_balance,2, '.', '');
                    $response['total_due'] = number_format($total_due,2, '.', '');
                    


                    $this->set_response(array('status' => TRUE, 'fees' => $response), REST_Controller::HTTP_OK);
                    
                }else{

                    $this->response(array('status' => FALSE, 'message' => translate('no_record_found')), REST_Controller::HTTP_NOT_FOUND);

                }
            }

        } catch (Exception $ex) {
            $response = array("status" => FALSE,"message" => 'Unauthorized token');
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);

        }
       
    }


    public function payments_get() {
        $this->load->model('fees_model');
        $token = $this->input->get_request_header('Authorization');
        $token = explode(' ', $token)[1];
        
        try{
            $user_data = JWT::decode($token, "user_auth",array('HS256'));
            $timestamp = $this->get('timestamp');
            $student_id = $this->get('userID');

            if(empty($student_id)){
                $this->response(array('status' => FALSE, 'message' => translate('userID_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            }else if(empty($timestamp)){
                $this->response(array('status' => FALSE, 'message' => translate('timestamp_is_missing.')), REST_Controller::HTTP_NOT_FOUND);
            }else{

                $response = array();
                $month = date('m', strtotime($this->get('timestamp')));
                $year = date('Y', strtotime($this->get('timestamp')));

                $this->db->select('fee_vouchers.*');
                $this->db->from('fee_vouchers');
                $this->db->join('fee_voucher_months','fee_voucher_months.fee_voucher_id = fee_vouchers.id');
                $this->db->where('fee_vouchers.student_id', $student_id);
                $this->db->where('fee_voucher_months.fee_month', $month);
                $this->db->where('fee_voucher_months.fee_year', $year);
                $voucher = $this->db->get()->row();

                if(!empty($voucher)){

                    $this->db->select('*');
                    $this->db->from('fee_voucher_payments');
                    $this->db->where('voucher_id', $voucher->id);
                    $paid = $this->db->get()->row();

                    if(!empty($paid)){
                        $response['status'] = 'Paid';
                        $response['paid_via'] = 'Bank';
                    }else if(date('Y-m-d') >= $voucher->due_date && date('Y-m-d') < $voucher->valid_date){
                        $response['status'] = 'Pending Fee';
                        $response['paid_via'] = 'N/A';
                    }else if(date('Y-m-d') >= $voucher->valid_date){
                        $response['status'] = 'Fee Defaulter';
                        $response['paid_via'] = 'N/A';
                    }

                    $this->db->select('*');
                    $this->db->from('fee_voucherables');
                    $this->db->where('voucher_id', $voucher->id);
                    $voucherables = $this->db->get()->result();

                    $total_fine = 0;
                    $total_discount = 0;
                    $total_paid = 0;
                    $total_balance = 0;
                    $total_amount = 0;

                    if($voucher->carry_balance == 1){
                        $previous_balance = previous_balance($voucher->student_id);
                    }else{
                        $previous_balance = 0;
                    }

                    foreach ($voucherables as $row) {

                        $type_discount = 0;
                        $deposit = $this->fees_model->getStudentFeeDeposit($row->allocation_id, $row->fee_type_id);

                        $check_month = check_voucher_month($voucher->id,$row->fee_type_id);
                        $discount_info = get_fee_type_discount($voucher->student_id,$row->fee_type_id);

                        if(!empty($discount_info)){
                            if(!empty($discount_info->discount)){
                                $type_discount = number_format(($discount_info->discount*$check_month), 2, '.', '');

                            }else{
                                $total_discount += 0;
                                $type_discount = number_format(0, 2, '.', '');
                            }
                        }

                        $type_fine = number_format(0, 2, '.', '');
                        $fine = getBalanceByType($row->allocation_id, $row->fee_type_id,$voucher->voucher_no);

                        if($fine > 0){
                            $type_fine =  number_format($fine, 2, '.', '');
                        }



                        $balance = ($row->amount*$check_month - $type_discount) + $type_fine;

                        $total_discount += $type_discount;
                        $total_fine += $type_fine;


                        $total_balance += $balance;
                        $total_amount += $row->amount*$check_month;

                        if ($balance != 0) {
                            $typeData[$row->allocation_id . "|" . $row->fee_type_id] = $row->fee_head;
                        }
                    }

                    $fee_amount = number_format($total_balance-$total_paid+$previous_balance, 2, '.', '');


                    $this->db->select('*');
                    $this->db->from('fee_voucher_payments');
                    $this->db->where('voucher_id', $voucher->id);
                    $payment = $this->db->get()->row();

                    $total_due = 0;
                    if(!empty($payment)){
                        $fee_amount = $payment->amount;
                        $total_paid = $payment->total_paid;
                        $total_discount = $payment->total_discount;
                        $total_fine = $payment->total_fine;
                        $total_due = $payment->total_due;
                        $previous_open_balance = $payment->previous_opening_balance;
                        $previous_balance = $payment->previous_balance;
                    }



                    $response['total_payable_amount'] = number_format($fee_amount,2, '.', '');
                    $response['total_discount'] = number_format($total_discount,2, '.', '');
                    $response['total_paid'] = number_format($total_paid,2, '.', '');
                    $response['total_fine'] = number_format($total_fine,2, '.', '');
                    $response['total_due'] = number_format($total_due,2, '.', '');
                    if($previous_open_balance > 0){
                        $response['previous_open_balance'] = number_format($previous_open_balance,2, '.', '');
                    }else{
                        $response['previous_open_balance'] = number_format($previous_open_balance,2, '.', '');
                    }
                    if($previous_balance > 0){
                        $response['previous_balance'] = number_format($previous_balance,2, '.', '');
                    }else{
                        $response['previous_balance'] = number_format($previous_balance,2, '.', '');
                    }

                    $d=strtotime($voucher->due_date);
                    $due_date =  date("dS F, Y", $d);
                    $response['due_date'] = $due_date;
                    $d=strtotime($voucher->valid_date);
                    $valid_date =  date("dS F, Y", $d);
                    $response['valid_date'] = $valid_date;
                    $this->set_response(array('status' => TRUE, 'fees' => $response), REST_Controller::HTTP_OK);

                    
                }else{

                    $this->response(array('status' => FALSE, 'message' => translate('no_record_found')), REST_Controller::HTTP_NOT_FOUND);
                }
            }

        } catch (Exception $ex) {
            $response = array("status" => FALSE,"message" => 'Unauthorized token');
            $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);

        }

    }

}
