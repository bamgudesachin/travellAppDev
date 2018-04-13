<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

date_default_timezone_set('UTC');

class Common_model extends CI_Model
{

    /*function for make sure the  student id exists*/
    public function user_check($userId)
    {
        $sql1 = "SELECT userId FROM users WHERE userId ='".$userId."'";
        $record = $this->db->query($sql1);
        if ($record->num_rows()>0) {
            return true;
        }   
    }  

    

    /* Get Profile details */
    public function profile_detail($post_data){
        $userId = $post_data['userId'];

        $sql = "SELECT userId,firstName,lastName,email,password,profile_picture,age,gender,country,countryTravelledTo,city,cityTravelledTo,deviceToken,facebookId,token,tokenExpiry,refreshToken,refreshTokenExpiry,latitude,longitude FROM users WHERE userId = $userId";
        $record = $this->db->query($sql);
        if($record->num_rows()>0){
            return $record->result_array();
        }
    }

   /* Update Profile details */
     public function updateProfile($post_data)
    {

        $userId = $post_data['userId'];        
        $password = array_key_exists("password",$post_data);
        if($password){
            $post_data['password'] = $post_data['password'];
        }
        $profile_picture = array_key_exists("profile_picture",$post_data);
        if($profile_picture){
            $post_data['profile_picture'] = $_POST['profile_picture1'];    
        } 
        $post_data['updated_at'] = date('Y-m-d H:i:s');
        $query = $this->db->where('userId',$userId)->update('users', $post_data);    
        if($query){
            $sql = "SELECT userId,firstName,lastName,email,password,profile_picture,age,gender,country,countryTravelledTo,city,cityTravelledTo,deviceToken,facebookId,token,tokenExpiry,refreshToken,refreshTokenExpiry,latitude,longitude FROM users WHERE userId = $userId";
            $record1 = $this->db->query($sql);
            if($record1->num_rows()>0){                    
                 return $record1->result_array();
            }else{
                return false;
            }  
        }else{
                return false;
            }  
    }

    /* Check the email for user is exist */
    public function update_email_check($email,$userId)
    {
        $sql1 = "SELECT userId FROM users WHERE userId = $userId AND email='".$email."'";
        $record1 = $this->db->query($sql1);
        if ($record1->num_rows()==1) {
            return false;
        } else {
            return true;
        }
    }

    /*******PRATIK FUNCTIONS***********/

    /***********CHECK***********/

    public function flight_check($flightId)
    {
        $this->db->select('flightId');
        $this->db->from('flight');
        $this->db->where('flightId',$flightId);
        $this->db->where('deleteFlag',0);

        $data = $this->db->get()->row();

        if(empty($data))
        {
            return false;
        }

        else
        {
            return true;
        }
    }
    
    public function car_rent_check($carRentId)
    {
        $this->db->select('carRentId');
        $this->db->from('carrental');
        $this->db->where('carRentId',$carRentId);
        $this->db->where('deleteFlag',0);

        $data = $this->db->get()->row();

        if(empty($data))
        {
            return false;
        }

        else
        {
            return true;
        }
    }

    public function hotel_check($hotelId)
    {
        $this->db->select('hotelId');
        $this->db->from('hotel');
        $this->db->where('hotelId',$hotelId);
        $this->db->where('deleteFlag',0);

        $data = $this->db->get()->row();

        if(empty($data))
        {
            return false;
        }

        else
        {
            return true;
        }
    }

    public function notes_check($notesId)
    {
        $this->db->select('notesId');
        $this->db->from('notes');
        $this->db->where('notesId',$notesId);
        $this->db->where('deleteFlag',0);

        $data = $this->db->get()->row();

        if(empty($data))
        {
            return false;
        }

        else
        {
            return true;
        }
    }
    /************INSERT*******************/
   public function insert_flight($post_data)
    {
        $userId = $post_data['userId'];
        $itineraryId = $post_data['itineraryId'];
        $flightId = array_key_exists("flightId",$post_data);
        if($flightId){
            $flightId = $post_data['flightId'];
        }

        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        //$post_data['inserted_at'] = $now->format('Y-m-d H:i:s');
        if($flightId){
            $post_data['dateModified'] = date("Y-m-d H:i:s");
            $update = $this->db->where('flightId',$flightId)->update('flight', $post_data);
            if($update){ 
                $this->db->select('fl.flightId,fl.userId,fl.origin,fl.destination,fl.departureDate,fl.departureTime,fl.departureTimeZone,fl.arrivalDate,fl.arrivalTime,fl.arrivalTimeZone,fl.pnrNo,fl.phoneNo,fl.itineraryId,fl.dateCreated,fl.dateModified,i.itineraryName,i.itineraryDate');
                $this->db->from('flight fl');
                $this->db->join('itinerary i','i.itineraryId = fl.itineraryId');
                $this->db->where('fl.flightId',$flightId);
                $result = $this->db->get()->result();
                
                $_POST['message'] = "Flight updated successfully";
                return $result;
            }
        }else{
             $query = $this->db->insert('flight',$post_data);
             if($query){
                $last_id = $this->db->insert_id();
                $this->db->select('fl.flightId,fl.userId,fl.origin,fl.destination,fl.departureDate,fl.departureTime,fl.departureTimeZone,fl.arrivalDate,fl.arrivalTime,fl.arrivalTimeZone,fl.pnrNo,fl.phoneNo,fl.itineraryId,fl.dateCreated,fl.dateModified,i.itineraryName,i.itineraryDate');
                $this->db->from('flight fl');
                $this->db->join('itinerary i','i.itineraryId = fl.itineraryId');
                $this->db->where('fl.flightId',$last_id);
                $result = $this->db->get()->result();
                $_POST['message'] = "Flight save successfully";
                return $result;
             }
        }
    }
    
	public function insert_hotel($post_data)
    {
        $userId = $post_data['userId'];
        $itineraryId = $post_data['itineraryId'];
        $hotelId = array_key_exists("hotelId",$post_data);
        if($hotelId){
            $hotelId = $post_data['hotelId'];
        }

       // $now = new DateTime();
       // $now->setTimezone(new DateTimezone('Asia/Kolkata'));
        //$post_data['inserted_at'] = $now->format('Y-m-d H:i:s');
        if($hotelId){
            $post_data['dateModified'] = date("Y-m-d H:i:s");
            $update = $this->db->where('hotelId',$hotelId)->update('hotel', $post_data);
            if($update){ 
                $this->db->select('hl.hotelId,hl.userId,hl.hotelName,hl.checkInDate,hl.checkInTime,hl.checkOutDate,hl.checkOutTime,hl.timeZone,hl.address,hl.latitude,hl.longitude,hl.phoneNo,hl.itineraryId,hl.dateCreated,hl.dateModified,i.itineraryName,i.itineraryDate');
                $this->db->from('hotel hl');
                $this->db->join('itinerary i','i.itineraryId = hl.itineraryId');
                $this->db->where('hl.hotelId',$hotelId);
                $result = $this->db->get()->result();
                
                $_POST['message'] = "Hotel updated successfully";
                return $result;
            }
        }else{
             $query = $this->db->insert('hotel',$post_data);
             if($query){
                $last_id = $this->db->insert_id();
                $this->db->select('hl.hotelId,hl.userId,hl.hotelName,hl.checkInDate,hl.checkInTime,hl.checkOutDate,hl.checkOutTime,hl.timeZone,hl.address,hl.latitude,hl.longitude,hl.phoneNo,hl.itineraryId,hl.dateCreated,hl.dateModified,i.itineraryName,i.itineraryDate');
                $this->db->from('hotel hl');
                $this->db->join('itinerary i','i.itineraryId = hl.itineraryId');
                $this->db->where('hl.hotelId',$last_id);
                $result = $this->db->get()->result();
                $_POST['message'] = "Hotel save successfully";
                return $result;
             }
        }
    }

    public function insert_car_rental($post_data)
    {
        $userId = $post_data['userId'];
        $itineraryId = $post_data['itineraryId'];
        $carRentId = array_key_exists("carRentId",$post_data);
        if($carRentId){
            $carRentId = $post_data['carRentId'];
        }

        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        //$post_data['inserted_at'] = $now->format('Y-m-d H:i:s');
        if($carRentId){
            $post_data['dateModified'] = date("Y-m-d H:i:s");
            $update = $this->db->where('carRentId',$carRentId)->update('carrental', $post_data);
            if($update){ 
                $this->db->select('cr.carRentId,cr.userId,cr.pickupAddress,cr.dropAddress,cr.pickupDate,cr.pickupTime,cr.returnDate,cr.returnTime,cr.timeZone,cr.phoneNo,cr.companyName,cr.itineraryId,cr.dateCreated,cr.dateModified,i.itineraryName,i.itineraryDate');
                $this->db->from('carrental cr');
                $this->db->join('itinerary i','i.itineraryId = cr.itineraryId');
                $this->db->where('cr.carRentId',$carRentId);
                $result = $this->db->get()->result();
                
                $_POST['message'] = "Car Rental Data updated successfully";
                return $result; 
            }
        }else{
             $query = $this->db->insert('carrental',$post_data);
             if($query){
                $last_id = $this->db->insert_id();
                $this->db->select('cr.carRentId,cr.userId,cr.pickupAddress,cr.dropAddress,cr.pickupDate,cr.pickupTime,cr.returnDate,cr.returnTime,cr.timeZone,cr.phoneNo,cr.companyName,cr.itineraryId,cr.dateCreated,cr.dateModified,i.itineraryName,i.itineraryDate');
                $this->db->from('carrental cr');
                $this->db->join('itinerary i','i.itineraryId = cr.itineraryId');
                $this->db->where('cr.carRentId',$last_id);
                $result = $this->db->get()->result();
                $_POST['message'] = "Car Rental Data save successfully";
                return $result;
             }
        }
    }

    public function insert_notes($post_data)
    {
        $userId = $post_data['userId'];
        $itineraryId = $post_data['itineraryId'];
        $notesId = array_key_exists("notesId",$post_data);
        if($notesId){
            $notesId = $post_data['notesId'];
        }

        //$now = new DateTime();
       // $now->setTimezone(new DateTimezone('Asia/Kolkata'));
        //$post_data['inserted_at'] = $now->format('Y-m-d H:i:s');
        if($notesId){
            $post_data['dateModified'] = date("Y-m-d H:i:s");
            $update = $this->db->where('notesId',$notesId)->update('notes', $post_data);
            if($update){ 
                $this->db->select('ns.notesId,ns.userId,ns.itineraryId,ns.notesTitle,ns.notesData,ns.dateCreated,ns.dateModified,i.itineraryName,i.itineraryDate');
                $this->db->from('notes ns');
                $this->db->join('itinerary i','i.itineraryId = ns.itineraryId');
                $this->db->where('ns.notesId',$notesId);
                $result = $this->db->get()->result();
                
                $_POST['message'] = "Notes updated successfully";
                return $result; 
            }
        }else{
             $query = $this->db->insert('notes',$post_data);
             if($query){
                $last_id = $this->db->insert_id();
                $this->db->select('ns.notesId,ns.userId,ns.itineraryId,ns.notesTitle,ns.notesData,ns.dateCreated,ns.dateModified,i.itineraryName,i.itineraryDate');
                $this->db->from('notes ns');
                $this->db->join('itinerary i','i.itineraryId = ns.itineraryId');
                $this->db->where('ns.notesId',$last_id);
                $result = $this->db->get()->result();
                $_POST['message'] = "Notes save successfully";
                return $result;
             }
        }
    }

    public function insert_itinerary($post_data)
    {
        $userId = $post_data['userId'];
        $itineraryId = array_key_exists("itineraryId",$post_data);
        if($itineraryId){
            $itineraryId = $post_data['itineraryId'];
        }

        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        //$post_data['inserted_at'] = $now->format('Y-m-d H:i:s');
        if($itineraryId){
            $post_data['dateModified'] = date("Y-m-d H:i:s");
            $update = $this->db->where('itineraryId',$itineraryId)->update('itinerary', $post_data);
            if($update){ 
                $sql = "SELECT * FROM itinerary WHERE itineraryId = $itineraryId";
                $record = $this->db->query($sql);           
                if($record->num_rows() > 0)
                {
                    $_POST['message'] = "Itinerary updated successfully";
                    return $record->result_array();
                }   
            }
        }
        else
        {
             $query = $this->db->insert('itinerary',$post_data);
             if($query){
                $last_id = $this->db->insert_id(); 
                $sql = "SELECT * FROM itinerary WHERE itineraryId = $last_id";
                $record = $this->db->query($sql);           
                if($record->num_rows() > 0)
                {
                    $_POST['message'] = "Itinerary added successfully";
                    return $record->result_array();
                }   
             }
        }
    }

    /*SAVE MOMENTS OR LUGGAGE AGAINST ITINERARY*/
    public function insert_moment($post_data)
    {
        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        $id = array_key_exists("id",$post_data);        
        if($id){ 
            $id = $post_data["id"];
            unset($post_data["id"]);
            $type = array_key_exists("type",$post_data);        
            if($type){ 
                $type = $post_data['type'];
                unset($post_data['type']);
            }
        }
        
        $userId = $post_data['userId'];       

        $momentImages = array_key_exists("momentImages",$post_data);        
        if($momentImages){  
            $momentImages =  $post_data['momentImages'];
            unset($post_data['momentImages']);          
            $data = array();
            for($i=0;$i<sizeof($momentImages);$i++)
            {
                if($type == 'moment'){
                    $post_data['itineraryId'] = $id;
                    $_POST['msg'] = "Moments added successfully";
                    
                }else{
                    $post_data['flightId'] = $id;
                    $_POST['msg'] = "Luggage added successfully";
                }
                
                $post_data['userId'] = $userId;               
                $post_data['momentImage'] = $_POST['momentImages1'.$i];                
                $data[] = $post_data;
            }  
        }

        $is_insert = $this->db->insert_batch('moments',$data);
        if($is_insert){
            return true;
        } else {
            return false;
        }        
    }
    /************READ*******************/

    public function count_fetch_flight($data)
    {
        $this->db->select('fl.flightId,fl.userId,fl.origin,fl.destination,fl.departureDate,fl.departureTime,fl.departureTimeZone,fl.arrivalDate,fl.arrivalTime,fl.arrivalTimeZone,fl.pnrNo,fl.phoneNo,fl.itineraryId,il.itineraryName,il.itineraryDate');
        $this->db->from('flight fl');
        $this->db->join('itinerary il','il.itineraryId = fl.itineraryId');
        $this->db->where('fl.userId',$data['userId']);
        $this->db->where('fl.itineraryId',$data['itineraryId']);
        $this->db->where('fl.deleteFlag',0);
        $data = $this->db->get();
        if($data->num_rows() > 0){
            return $data->num_rows();
        }
    }
    public function fetch_flight($data)
    {
        $limit = array_key_exists("limit",$data);
        if($limit){
            $limit = $data['limit'];
        }
        $offset = array_key_exists("offset",$data);
        if($offset){
            $offset = $data['offset'];
        }

        $this->db->select('fl.flightId,fl.userId,fl.origin,fl.destination,fl.departureDate,fl.departureTime,fl.departureTimeZone,fl.arrivalDate,fl.arrivalTime,fl.arrivalTimeZone,fl.pnrNo,fl.phoneNo,fl.itineraryId,il.itineraryName,il.itineraryDate');
        $this->db->from('flight fl');
        $this->db->join('itinerary il','il.itineraryId = fl.itineraryId');
        $this->db->where('fl.userId',$data['userId']);
        $this->db->where('fl.itineraryId',$data['itineraryId']);
        $this->db->where('fl.deleteFlag',0);
        $this->db->order_by("fl.dateCreated", "desc");
        if ($limit && $offset==0 || $offset>0)
        {
            $this->db->limit($limit,$offset);  
        }
        
         $data = $this->db->get();
        if($data->num_rows() > 0){
            return $data->result_array();
        }
    }
	

    public function count_fetch_car_rental($data)
    {
        $this->db->select('cr.carRentId,cr.userId,cr.pickupAddress,cr.dropAddress,cr.pickupDate,cr.pickupTime,cr.returnDate,cr.returnTime,cr.timeZone,cr.phoneNo,cr.companyName,cr.itineraryId,il.itineraryName,il.itineraryDate');
        $this->db->from('carrental cr');
        $this->db->join('itinerary il','il.itineraryId = cr.itineraryId');
        $this->db->where('cr.userId',$data['userId']);
        $this->db->where('cr.itineraryId',$data['itineraryId']);
        $this->db->where('cr.deleteFlag',0);
        $data = $this->db->get();
        if($data->num_rows() > 0){
            return $data->num_rows();
        }
    }

    public function fetch_car_rental($data)
    {
        $limit = array_key_exists("limit",$data);
        if($limit){
            $limit = $data['limit'];
        }
        $offset = array_key_exists("offset",$data);
        if($offset){
            $offset = $data['offset'];
        }

        $this->db->select('cr.carRentId,cr.userId,cr.pickupAddress,cr.dropAddress,cr.pickupDate,cr.pickupTime,cr.returnDate,cr.returnTime,cr.timeZone,cr.phoneNo,cr.companyName,cr.itineraryId,il.itineraryName,il.itineraryDate');
        $this->db->from('carrental cr');
        $this->db->join('itinerary il','il.itineraryId = cr.itineraryId');
        $this->db->where('cr.userId',$data['userId']);
        $this->db->where('cr.itineraryId',$data['itineraryId']);
        $this->db->where('cr.deleteFlag',0);
        $this->db->order_by("cr.dateCreated", "desc");
        if ($limit && $offset==0 || $offset>0)
        {
            $this->db->limit($limit,$offset);  
        }
        $data = $this->db->get()->result();
        return $data;
    }


    public function count_fetch_hotel($data)
    {
        $this->db->select('hl.hotelId,hl.userId,hl.hotelName,hl.checkInDate,hl.checkinTime,hl.checkOutDate,hl.checkOutTime,hl.timeZone,hl.address,hl.latitude,hl.longitude,hl.phoneNo,hl.itineraryId,il.itineraryName,il.itineraryDate');
        $this->db->from('hotel hl');
        $this->db->join('itinerary il','il.itineraryId = hl.itineraryId');
        $this->db->where('hl.userId',$data['userId']);
        $this->db->where('hl.itineraryId',$data['itineraryId']);
        $this->db->where('hl.deleteFlag',0);
        $data = $this->db->get();
        if($data->num_rows() > 0){
            return $data->num_rows();
        }
    }

    public function fetch_hotel($data)
    {
        $limit = array_key_exists("limit",$data);
        if($limit){
            $limit = $data['limit'];
        }
        $offset = array_key_exists("offset",$data);
        if($offset){
            $offset = $data['offset'];
        }

        $this->db->select('hl.hotelId,hl.userId,hl.hotelName,hl.checkInDate,hl.checkInTime,hl.checkOutDate,hl.checkOutTime,hl.timeZone,hl.address,hl.latitude,hl.longitude,hl.phoneNo,hl.itineraryId,il.itineraryName,il.itineraryDate');
        $this->db->from('hotel hl');
        $this->db->join('itinerary il','il.itineraryId = hl.itineraryId');
        $this->db->where('hl.userId',$data['userId']);
        $this->db->where('hl.itineraryId',$data['itineraryId']);
        $this->db->where('hl.deleteFlag',0);
        $this->db->order_by("hl.dateCreated", "desc");
        if ($limit && $offset==0 || $offset>0)
        {
            $this->db->limit($limit,$offset);  
        }
        $data = $this->db->get()->result();
        return $data;
    }

    /* Get the count of itinerary moments */
   public function count_fetch_itinerary($post_data){
        $userId = $post_data['userId'];    
       
       /* $sql= "SELECT i.itineraryId,i.itineraryName,i.itineraryDate,i.userId,
              CONCAT('".site_url()."',(SELECT m.momentImage FROM (SELECT m1.* FROM moments AS m1 LEFT JOIN moments AS m2 ON (m1.itineraryId = m2.itineraryId AND m1.momentsId < m2.momentsId)
WHERE m2.momentsId IS NULL) m WHERE m.deleteFlag !=1 AND m.userId=i.userId AND  m.itineraryId = i.itineraryId  GROUP BY m.userId)) as momentImage,
              ((SELECT n.notesId FROM notes n WHERE  n.userId= $userId  AND i.itineraryId = n.itineraryId AND n.deleteFlag !=1 LIMIT 1)IS NOT NULL) AS note_flag
                FROM  itinerary i
                LEFT JOIN users u ON i.userId = u.userId                 
                WHERE i.userId = $userId AND i.deleteFlag !=1 AND u.deleteFlag !=1
                GROUP BY i.itineraryId  ORDER BY i.dateCreated DESC";*/

        $sql = "SELECT i.itineraryId,i.itineraryName,i.itineraryDate,i.userId,
              CONCAT('".site_url()."',(SELECT m.momentImage FROM (SELECT m1.* FROM moments AS m1 LEFT JOIN moments AS m2 ON (m1.itineraryId = m2.itineraryId AND m1.momentsId < m2.momentsId)
WHERE m2.momentsId IS NULL) m WHERE m.deleteFlag !=1 AND m.userId=i.userId AND  m.itineraryId = i.itineraryId  GROUP BY m.userId)) as momentImage,
              ((SELECT n.notesId FROM notes n WHERE  n.userId= $userId  AND i.itineraryId = n.itineraryId AND n.deleteFlag !=1 LIMIT 1)IS NOT NULL) AS note_flag
                FROM  itinerary i
                LEFT JOIN users u ON i.userId = u.userId                 
                WHERE i.userId = $userId AND i.deleteFlag !=1 AND u.deleteFlag !=1
                GROUP BY i.itineraryId  ORDER BY i.dateCreated DESC";
       
        //echo $sql;exit();
        $record = $this->db->query($sql);
        if ($record->num_rows() > 0)
        {
            return $record->num_rows();
        }
   }

   /* Get the itinerary list with  momentImage */
    public function fetch_itinerary($post_data)
    {
        $userId = $post_data['userId']; 

        $limit = array_key_exists("limit",$post_data);
        if($limit){
            $limit = $post_data['limit'];
        }
        $offset = array_key_exists("offset",$post_data);
        if($offset){
            $offset = $post_data['offset'];
        }

        if($limit && $offset== 0 || $offset > 0){            
            $LIMIT = "LIMIT  $offset,$limit";
        }else{            
            $LIMIT = "";
        }   
       
        /*$sql= "SELECT i.itineraryId,i.itineraryName,i.itineraryDate,i.userId,
              CONCAT('".site_url()."',(
                          SELECT m.momentImage FROM moments m
                  JOIN (SELECT itineraryId,momentsId,userId, MAX(dateCreated) dateCreated FROM moments WHERE moments.deleteFlag !=1 GROUP BY userId) m1
                    ON m.userId = m1.userId AND m.dateCreated = m1.dateCreated AND m.itineraryId=m1.itineraryId where i.itineraryId = m.itineraryId AND i.userId = m.userId 
                       )
                      ) as momentImage,
              ((SELECT n.notesId FROM notes n WHERE  n.userId= $userId  AND i.itineraryId = n.itineraryId LIMIT 1)IS NOT NULL) AS note_flag
                FROM  itinerary i
                LEFT JOIN users u ON i.userId = u.userId                 
                WHERE i.userId = $userId AND i.deleteFlag !=1 AND u.deleteFlag !=1
                GROUP BY i.itineraryId  ORDER BY i.dateCreated DESC $LIMIT";
            */
        $sql = "SELECT i.itineraryId,i.itineraryName,i.itineraryDate,i.userId,
              CONCAT('".site_url()."',(SELECT m.momentImage FROM (SELECT m1.* FROM moments AS m1 LEFT JOIN moments AS m2 ON (m1.itineraryId = m2.itineraryId AND m1.momentsId < m2.momentsId)
WHERE m2.momentsId IS NULL) m WHERE m.deleteFlag !=1 AND m.userId=i.userId AND  m.itineraryId = i.itineraryId  GROUP BY m.userId)) as momentImage,
              ((SELECT n.notesId FROM notes n WHERE  n.userId= $userId  AND i.itineraryId = n.itineraryId AND n.deleteFlag !=1 LIMIT 1)IS NOT NULL) AS note_flag
                FROM  itinerary i
                LEFT JOIN users u ON i.userId = u.userId                 
                WHERE i.userId = $userId AND i.deleteFlag !=1 AND u.deleteFlag !=1
                GROUP BY i.itineraryId  ORDER BY i.dateCreated DESC $LIMIT";
        //echo $sql;exit();
        $record = $this->db->query($sql);
        if ($record->num_rows() > 0)
        {
            return $record->result_array();
        }
    }

   /* Get the count of itinerary moments */
   public function count_fetch_moment($post_data){
        $userId = $post_data['userId'];

        $id = array_key_exists("id",$post_data);        
        if($id){ 
            $id = $post_data["id"];
            unset($post_data["id"]);
            $type = array_key_exists("type",$post_data);        
            if($type){ 
                $type = $post_data['type'];
                unset($post_data['type']);
            }
        }

        if($type == 'moment'){
            $itineraryId = $id;
            $_POST['fetch_msg'] = "Moments fetched successfully";
            $sql= "SELECT i.itineraryId,i.itineraryName,i.itineraryDate,m.userId,m.momentsId,
               CASE WHEN m.momentImage IS NOT NULL THEN CONCAT('".site_url()."', m.momentImage)
            ELSE '' END AS momentImage                   
                FROM  itinerary i
                LEFT JOIN moments m ON i.itineraryId = m.itineraryId 
                LEFT JOIN users u ON i.userId = u.userId 
                WHERE m.itineraryId = $itineraryId AND m.userId = $userId AND i.deleteFlag !=1 AND m.deleteFlag !=1
                GROUP BY m.momentsId  ORDER BY m.dateCreated DESC";
       
                    
        }else{
            $flightId = $id;
            $_POST['fetch_msg'] = "Luggage fetched successfully";
            
            $sql= "SELECT fl.flightId,fl.userId,fl.origin,fl.destination,fl.departureDate,fl.departureTime,fl.departureTimeZone,fl.arrivalDate,fl.arrivalTime,fl.arrivalTimeZone,fl.pnrNo,fl.phoneNo,fl.itineraryId,il.itineraryName,il.itineraryDate,m.momentsId,
               CASE WHEN m.momentImage IS NOT NULL THEN CONCAT('".site_url()."', m.momentImage)
            ELSE '' END AS momentImage                   
                FROM  flight fl
                LEFT JOIN itinerary il ON il.itineraryId = fl.itineraryId 
                LEFT JOIN moments m ON fl.flightId = m.flightId 
                LEFT JOIN users u ON fl.userId = u.userId 
                WHERE m.flightId = $flightId AND m.userId = $userId AND il.deleteFlag !=1 AND fl.deleteFlag !=1 AND m.deleteFlag !=1
                GROUP BY m.momentsId  ORDER BY m.dateCreated DESC";
        }

       // $itineraryId = $post_data['itineraryId'];
    
       
        
       // echo $sql;exit();
        $record = $this->db->query($sql);
        if ($record->num_rows() > 0)
        {
            return $record->num_rows();
        }
   }

   /* Get the list of itinerary moments */
   public function fetch_moment($post_data){
        $userId = $post_data['userId'];
        $id = array_key_exists("id",$post_data);        
        if($id){ 
            $id = $post_data["id"];
            unset($post_data["id"]);
            $type = array_key_exists("type",$post_data);        
            if($type){ 
                $type = $post_data['type'];
                unset($post_data['type']);
            }
        }
       
        
        $limit = array_key_exists("limit",$post_data);
        if($limit){
            $limit = $post_data['limit'];
        }
        $offset = array_key_exists("offset",$post_data);
        if($offset){
            $offset = $post_data['offset'];
        }

        if($limit && $offset== 0 || $offset > 0){            
            $LIMIT = "LIMIT  $offset,$limit";
        }else{            
            $LIMIT = "";
        }
    
        if($type == 'moment'){
            $itineraryId = $id;
            $_POST['fetch_msg'] = "Moments fetched successfully";
            $sql= "SELECT i.itineraryId,i.itineraryName,i.itineraryDate,m.userId,m.momentsId,
               CASE WHEN m.momentImage IS NOT NULL THEN CONCAT('".site_url()."', m.momentImage)
            ELSE '' END AS momentImage                   
                FROM  itinerary i
                LEFT JOIN moments m ON i.itineraryId = m.itineraryId 
                LEFT JOIN users u ON i.userId = u.userId 
                WHERE m.itineraryId = $itineraryId AND m.userId = $userId AND i.deleteFlag !=1 AND m.deleteFlag !=1
                GROUP BY m.momentsId  ORDER BY m.dateCreated DESC $LIMIT";
       
                    
        }else{
            $flightId = $id;
            $_POST['fetch_msg'] = "Luggage fetched successfully";
            
            $sql= "SELECT fl.flightId,fl.userId,fl.origin,fl.destination,fl.departureDate,fl.departureTime,fl.departureTimeZone,fl.arrivalDate,fl.arrivalTime,fl.arrivalTimeZone,fl.pnrNo,fl.phoneNo,fl.itineraryId,il.itineraryName,il.itineraryDate,m.momentsId,
               CASE WHEN m.momentImage IS NOT NULL THEN CONCAT('".site_url()."', m.momentImage)
            ELSE '' END AS momentImage                   
                FROM  flight fl
                LEFT JOIN itinerary il ON il.itineraryId = fl.itineraryId 
                LEFT JOIN moments m ON fl.flightId = m.flightId 
                LEFT JOIN users u ON fl.userId = u.userId 
                WHERE m.flightId = $flightId AND m.userId = $userId AND il.deleteFlag !=1 AND fl.deleteFlag !=1 AND m.deleteFlag !=1
                GROUP BY m.momentsId  ORDER BY m.dateCreated DESC $LIMIT";
        }
       
        
       
        //echo $sql;exit();
        $record = $this->db->query($sql);
        if ($record->num_rows() > 0)
        {
            return $record->result_array();
        }
   }

   /*Get the count of itinerary notes */
    public function count_fetch_itinerary_notes($data)
    {
        $this->db->distinct();
        $this->db->select('ns.itineraryId,il.itineraryName,il.itineraryDate');
        $this->db->from('notes ns');
        $this->db->join('itinerary il','il.itineraryId = ns.itineraryId');
        $this->db->where('ns.userId',$data['userId']);
        $this->db->where('ns.deleteFlag',0);
        $data = $this->db->get();
        if($data->num_rows() > 0){
            return $data->num_rows();
        }
    }

    public function fetch_itinerary_notes($data)
    {
        $limit = array_key_exists("limit",$data);
        if($limit){
            $limit = $data['limit'];
        }
        $offset = array_key_exists("offset",$data);
        if($offset){
            $offset = $data['offset'];
        }
        $this->db->distinct();
        $this->db->select('ns.itineraryId,il.itineraryName,il.itineraryDate');
        $this->db->from('notes ns');
        $this->db->join('itinerary il','il.itineraryId = ns.itineraryId');
        $this->db->where('ns.userId',$data['userId']);
        $this->db->where('ns.deleteFlag',0);
        if ($limit && $offset==0 || $offset>0)
        {
            $this->db->limit($limit,$offset);  
        }
        $data = $this->db->get();
        if($data->num_rows() > 0){
            return $data->result_array();
        }
    }


    public function count_fetch_notes($data)
    {
        $this->db->select('ns.itineraryId,il.itineraryName,il.itineraryDate');
        $this->db->from('notes ns');
        $this->db->join('itinerary il','il.itineraryId = ns.itineraryId');
        $this->db->where('ns.userId',$data['userId']);
        $this->db->where('ns.itineraryId',$data['itineraryId']);
        $this->db->where('ns.deleteFlag',0);
        $data = $this->db->get();
        if($data->num_rows() > 0){
            return $data->num_rows();
        }
    }

    public function fetch_notes($data)
    {
       $limit = array_key_exists("limit",$data);
        if($limit){
            $limit = $data['limit'];
        }
        $offset = array_key_exists("offset",$data);
        if($offset){
            $offset = $data['offset'];
        }
        $this->db->select('ns.notesId,ns.userId,ns.itineraryId,ns.notesTitle,ns.notesData,il.itineraryName,il.itineraryDate');
        $this->db->from('notes ns');
        $this->db->join('itinerary il','il.itineraryId = ns.itineraryId');
        $this->db->where('ns.userId',$data['userId']);
        $this->db->where('ns.itineraryId',$data['itineraryId']);
        $this->db->where('ns.deleteFlag',0);
        $this->db->order_by('ns.dateCreated', 'desc');
        if ($limit && $offset==0 || $offset>0)
        {
            $this->db->limit($limit,$offset);  
        }
        $data1 = $this->db->get();
        if($data1->num_rows() > 0){
            return $data1->result_array();
        }
    }
    /************DELETE*******************/
   

    public function delete_flight($post_data)
    {
        $data['deleteFlag']= '1';
        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        $data['dateModified'] = date("Y-m-d H:i:s");

        $user_id = $post_data['userId'];
        if($post_data['deleteItem'])
        {
            foreach($post_data['deleteItem'] as $pd)
            {
                $this->db->where('userId',$user_id);
                $this->db->where('flightId',$pd['flightId']);  
                $this->db->update('flight',$data);        
            }
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function delete_car_rental($post_data)
    {
        $data['deleteFlag']= '1';
        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        $data['dateModified'] = date("Y-m-d H:i:s");


        $user_id = $post_data['userId'];
        if($post_data['deleteItem'])
        {
            foreach($post_data['deleteItem'] as $pd)
            {
                $this->db->where('userId',$user_id);
                $this->db->where('carRentId',$pd['carRentId']);  
                $this->db->update('carrental',$data);        
            }
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function delete_hotel($post_data)
    {
       $data['deleteFlag']= '1';
        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        $data['dateModified'] = date("Y-m-d H:i:s");


        $user_id = $post_data['userId'];
        if($post_data['deleteItem'])
        {
            foreach($post_data['deleteItem'] as $pd)
            {
                $this->db->where('userId',$user_id);
                $this->db->where('hotelId',$pd['hotelId']);  
                $this->db->update('hotel',$data);        
            }
            return true;
        }
        else
        {
            return false;
        }
    }


    public function delete_notes($post_data)
    {
        $data['deleteFlag']= '1';

        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        $data['dateModified'] = date("Y-m-d H:i:s");

        $user_id = $post_data['userId'];
        if($post_data['deleteItem'])
        {
            foreach($post_data['deleteItem'] as $pd)
            {
                $this->db->where('userId',$user_id);
                $this->db->where('notesId',$pd['notesId']);  
                $this->db->update('notes',$data);        
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    public function delete_itinerary_notes($post_data)
    {
        $data['deleteFlag']= '1';

        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        $data['dateModified'] = date("Y-m-d H:i:s");

        $this->db->where('userId',$post_data['userId']);
        $this->db->where('itineraryId',$post_data['itineraryId']);  
        $this->db->update('notes',$data);        
        
        return true;
    }

    public function itinerary_check($itineraryId)
    {
        $this->db->select('itineraryId');
        $this->db->from('itinerary');
        $this->db->where('itineraryId',$itineraryId);
        $this->db->where('deleteFlag',0);

        $data = $this->db->get()->row();

        if(empty($data))
        {
            return false;
        }

        else
        {
            return true;
        }
    }
    
    public function delete_itinerary($post_data)
    {
        $data['deleteFlag']= '1';

       // $now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        $data['dateModified'] = date("Y-m-d H:i:s");

        $this->db->select('itineraryId');
        $this->db->from('itinerary');
        $this->db->where('itineraryId',$post_data['itineraryId']);
        $this->db->where('userId',$post_data['userId']);

        $data1 = $this->db->get()->row();

        if(!empty($data1))
        {
            $this->db->where('itineraryId',$post_data['itineraryId']);
            $this->db->where('userId',$post_data['userId']);
            $this->db->update('flight',$data);

            $this->db->where('itineraryId',$post_data['itineraryId']);
            $this->db->where('userId',$post_data['userId']);
            $this->db->update('hotel',$data);

            $this->db->where('itineraryId',$post_data['itineraryId']);
            $this->db->where('userId',$post_data['userId']);
            $this->db->update('carrental',$data);

            $this->db->where('itineraryId',$post_data['itineraryId']);
            $this->db->where('userId',$post_data['userId']);
            $this->db->update('notes',$data);


            $this->db->where('itineraryId',$post_data['itineraryId']);
            $this->db->where('userId',$post_data['userId']);
            $this->db->update('moments',$data);

            $this->db->where('itineraryId',$post_data['itineraryId']);
            $this->db->where('userId',$post_data['userId']);
            $this->db->update('itinerary',$data);

            return true;
        }

        else
        {
            return false;
        }
    }

    public function itinerary_check_delete($post_data)
    {
        $this->db->select('flightId');
        $this->db->from('flight');
        $this->db->where('itineraryId',$post_data['itineraryId']);
        $this->db->where('deleteFlag',0);
        $data1 = $this->db->get()->result();

        $this->db->select('carRentId');
        $this->db->from('carrental');
        $this->db->where('itineraryId',$post_data['itineraryId']);
        $this->db->where('deleteFlag',0);
        $data2 = $this->db->get()->result();

        $this->db->select('hotelId');
        $this->db->from('hotel');
        $this->db->where('itineraryId',$post_data['itineraryId']);
        $this->db->where('deleteFlag',0);
        $data3 = $this->db->get()->result();

        $data = array_merge($data1,$data2,$data3);

        if(empty($data))
        {
             return true;
        }
        else
        {
            return false;
        } 
    }

    public function delete_moments($post_data)
    {
        $data['deleteFlag']= '1';

        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        $data['dateModified'] = date("Y-m-d H:i:s");

        $user_id = $post_data['userId'];
        if($post_data['deleteItem'])
        {
            foreach($post_data['deleteItem'] as $pd)
            {
                $this->db->where('userId',$user_id);
                $this->db->where('momentsId',$pd['momentsId']);  
                $this->db->update('moments',$data);        
            }
            return true;
        }
        else
        {
            return false;
        }
    }
/**************EDIT**************/
    public function edit_flight($post_data)
    {
        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
            //$post_data['dateModified'] = $now->format('Y-m-d H:i:s');

        $this->db->select('flightId');
        $this->db->from('flight');
        $this->db->where('flightId',$post_data['flightId']);
        $this->db->where('userId',$post_data['userId']);
        $data1 = $this->db->get()->row();

        if(!empty($data1))
        {
            $flightId = $post_data['flightId'];
            $userId = $post_data['userId'];

            unset($post_data['flightId']);
            unset($post_data['userId']);


            $this->db->where('flightId',$flightId);
            $this->db->where('userId',$userId);
            $this->db->update('flight',$post_data);

            $this->db->select('fl.userId,fl.flightId,fl.origin,fl.destination,fl.departure,fl.arrival,fl.pnrNo,fl.phoneNo,fl.itineraryId,il.itineraryName');
            $this->db->from('flight fl');
            $this->db->join('itinerary il','il.itineraryId = fl.itineraryId');
            $this->db->where('fl.flightId',$flightId);
            $this->db->where('fl.userId',$userId);
            $data2 = $this->db->get()->result();
            return $data2;
        }

        else
        {
            return false;
        }
    }

    public function check_itinerary($post_data)
    {
        $this->db->select('itineraryId');
        $this->db->from('itinerary');
        $this->db->where('itineraryId',$post_data['itineraryId']);
        $this->db->where('userId',$post_data['userId']);
        $data = $this->db->get()->row();
        return $data;
    }

    public function edit_car_rent($post_data)
    {
        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        $post_data['dateModified'] = date("Y-m-d H:i:s");

        $this->db->select('carRentId');
        $this->db->from('carrental');
        $this->db->where('carRentId',$post_data['carRentId']);
        $this->db->where('userId',$post_data['userId']);
        $data1 = $this->db->get()->row();

        if(!empty($data1))
        {
            $carRentId = $post_data['carRentId'];
            $userId = $post_data['userId'];

            unset($post_data['carRentId']);
            unset($post_data['userId']);

            $this->db->where('carRentId',$carRentId);
            $this->db->where('userId',$userId);
            $this->db->update('carrental',$post_data);

            $this->db->select('cr.userId,cr.carRentId,cr.pickupAddress,cr.dropAddress,cr.pickupDate,cr.companyName,cr.itineraryId,il.itineraryName');
            $this->db->from('carrental cr');
            $this->db->join('itinerary il','il.itineraryId = cr.itineraryId');
            $this->db->where('cr.carRentId',$carRentId);
            $this->db->where('cr.userId',$userId);
            $data2 = $this->db->get()->result();
            return $data2;
        }

        else
        {
            return false;
        }
    }   

    public function edit_hotel($post_data)
    {
        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        $post_data['dateModified'] = date("Y-m-d H:i:s");

        $this->db->select('hotelId');
        $this->db->from('hotel');
        $this->db->where('hotelId',$post_data['hotelId']);
        $this->db->where('userId',$post_data['userId']);
        $data1 = $this->db->get()->row();

        if(!empty($data1))
        {
            $hotelId = $post_data['hotelId'];
            $userId = $post_data['userId'];

            unset($post_data['hotelId']);
            unset($post_data['userId']);

            $this->db->where('hotelId',$hotelId);
            $this->db->where('userId',$userId);
            $this->db->update('hotel',$post_data);

            $this->db->select('hl.userId,hl.hotelId,hl.hotelName,hl.checkIn,hl.checkOut,hl.address,hl.phoneNo,hl.itineraryId,il.itineraryName');
            $this->db->from('hotel hl');
            $this->db->join('itinerary il','il.itineraryId = hl.itineraryId');
            $this->db->where('hl.hotelId',$hotelId);
            $this->db->where('hl.userId',$userId);
            $data2 = $this->db->get()->result();
            return $data2;
        }

        else
        {
            return false;
        }
    }

    public function edit_itinerary($post_data)
    {
        //$now = new DateTime();
       // $now->setTimezone(new DateTimezone('Asia/Kolkata'));
        $post_data['dateModified'] = date("Y-m-d H:i:s");

        $this->db->select('itineraryId');
        $this->db->from('itinerary');
        $this->db->where('itineraryId',$post_data['itineraryId']);
        $this->db->where('userId',$post_data['userId']);
        $data1 = $this->db->get()->row();

        if(!empty($data1))
        {
            $itineraryId = $post_data['itineraryId'];
            $userId = $post_data['userId'];

            unset($post_data['itineraryId']);
            unset($post_data['userId']);

            $this->db->where('itineraryId',$itineraryId);
            $this->db->where('userId',$userId);
            $this->db->update('itinerary',$post_data);

            $this->db->select('userId,itineraryId,itineraryName,itineraryDate');
            $this->db->from('itinerary');
            $this->db->where('itineraryId',$itineraryId);
            $this->db->where('userId',$userId);
            $data2 = $this->db->get()->result();
            return $data2;
        }

        else
        {
            return false;
        }
    }

    public function edit_notes($post_data)
    {
        //$now = new DateTime();
        //$now->setTimezone(new DateTimezone('Asia/Kolkata'));
        $post_data['dateModified'] = date("Y-m-d H:i:s");

        $this->db->select('notesId');
        $this->db->from('notes');
        $this->db->where('notesId',$post_data['notesId']);
        $this->db->where('userId',$post_data['userId']);
        $data1 = $this->db->get()->row();


        if(!empty($data1))
        {
            $notesId = $post_data['notesId'];
            $userId = $post_data['userId'];

            unset($post_data['notesId']);
            unset($post_data['userId']);

            $this->db->where('notesId',$notesId);
            $this->db->where('userId',$userId);
            $this->db->update('notes',$post_data);

            $this->db->select('ns.notesId,ns.userId,ns.itineraryId,ns.notesTitle,ns.notesData,il.itineraryName,il.itineraryDate');
            $this->db->from('notes ns');
            $this->db->join('itinerary il','il.itineraryId = ns.itineraryId');
            $this->db->where('ns.notesId',$notesId);
            $this->db->where('ns.userId',$userId);
            $data2 = $this->db->get()->result();
            return $data2;
        }

        else
        {
            return false;
        }
    }
    
    /* Store the users current locations */
   public function insert_locations($post_data){
        $userId = $post_data['userId'];
        $latitude = $post_data['latitude'];
        $longitude = $post_data['longitude'];
        unset($post_data['token']);
        $insertData = $this->db->insert('locationshistory', $post_data);
        return $insertData;
   }

   /* Get the count of nearby users with same country */
   public function count_nearby_users_list($post_data){
        $userId = $post_data['userId'];
        $latitude = $post_data['latitude'];
        $longitude = $post_data['longitude'];

        $radius = array_key_exists("radius",$post_data);
        if($radius){
            $min_radius= 0;
            $max_radius = $post_data['radius'];            
            $radius = " HAVING distance_in_km BETWEEN $min_radius AND $max_radius";
        }else{
            $min_radius= 0;
            $max_radius = 50;
            $radius = " HAVING distance_in_km BETWEEN $min_radius AND $max_radius";
        }        
       

        if(!empty($latitude) && !empty($longitude))
        {
            $distance_in_km = "( 6371 * acos( cos( radians($latitude) ) * cos( radians( loc.latitude) ) 
                * cos( radians( loc.longitude ) - radians($longitude) ) + 
                    sin( radians($latitude) ) * sin( radians( loc.latitude ) ) ) ) 
                AS distance_in_km";
        }

        if (!empty($latitude) && !empty($longitude)) {
            $sql= "SELECT u.userId,u.firstName,u.lastName,u.email,u.profile_picture,u.gender,u.country,u.city,
                    $distance_in_km,loc.latitude,loc.longitude
                FROM (select * from users where country = (select country from users where userId = $userId) AND userId <> $userId) u
                LEFT JOIN (SELECT l1.* FROM locationshistory l1
                            JOIN (SELECT userId, MAX(createdAt) createdAt FROM locationshistory GROUP BY userId) l2
                            ON l1.userId = l2.userId AND l1.createdAt = l2.createdAt) loc ON u.userId = loc.userId 

                 GROUP BY loc.userId $radius ORDER BY distance_in_km ASC";
        }
        //echo $sql;exit();
        $record = $this->db->query($sql);
        if ($record->num_rows() > 0)
        {
            return $record->num_rows();
        }
   }

   /* Get the nearby users list*/
   public function nearby_users_list($post_data){
        $userId = $post_data['userId'];
        $latitude = $post_data['latitude'];
        $longitude = $post_data['longitude'];
        $limit = $post_data['limit'];
        $offset = $post_data['offset'];

        $radius = array_key_exists("radius",$post_data);
        if($radius){
            $min_radius= 0;
            $max_radius = $post_data['radius'];            
            $radius = " HAVING distance_in_km BETWEEN $min_radius AND $max_radius";
        }else{
            $min_radius= 0;
            $max_radius = 50;
            $radius = " HAVING distance_in_km BETWEEN $min_radius AND $max_radius";
        }  

        if(!empty($latitude) && !empty($longitude))
        {
            $distance_in_km = "( 6371 * acos( cos( radians($latitude) ) * cos( radians( loc.latitude) ) 
                * cos( radians( loc.longitude ) - radians($longitude) ) + 
                    sin( radians($latitude) ) * sin( radians( loc.latitude ) ) ) ) 
                AS distance_in_km";
        }

        if (!empty($latitude) && !empty($longitude)) {
            $sql= "SELECT u.userId,u.firstName,u.lastName,u.email,u.profile_picture,u.gender,u.country,u.city,
                    $distance_in_km,loc.latitude,loc.longitude
                FROM (select * from users where country = (select country from users where userId = $userId) AND userId <> $userId) u
                LEFT JOIN (SELECT l1.* FROM locationshistory l1
                            JOIN (SELECT userId, MAX(createdAt) createdAt FROM locationshistory GROUP BY userId) l2
                            ON l1.userId = l2.userId AND l1.createdAt = l2.createdAt) loc ON u.userId = loc.userId 

                 GROUP BY loc.userId $radius ORDER BY distance_in_km ASC LIMIT $offset, $limit";
        }
        //echo $sql;exit();
        $record = $this->db->query($sql);
        if ($record->num_rows() > 0)
        {
            return $record->result_array();
        }
   }

   

}?>