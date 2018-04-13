<?php 
	
$config['notification_add_user_in_tribe'] = array("firstName  has shared a searchName with you","add_tribes");
$config['notification_for_accept_invitation'] = array("name is accept invitation for searchName","accept_invitation");

$config['notification_for_decline_invitation'] = array("name has declined invitation for searchName","decline_invitation");

$config['notification_add_comment_on_shortlisted_property'] = array("firstName  has comment on propertyName","shortlisted_property_list");


$config['notification_add_chat_on_search'] = array("firstName  has chat on searchName","search_detail_chats");


$config['add_flight'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
               array(
                     'field'   => 'origin',
                     'label'   => 'Orign',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'destination',
                     'label'   => 'Destination',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'departureDate',
                     'label'   => 'Departure Date',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'departureTime',
                     'label'   => 'Departure Time',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'arrivalDate',
                     'label'   => 'Arrival Date',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'arrivalTime',
                     'label'   => 'Arrival Time',
                     'rules'   => 'required'
                  ),               
               array(
                     'field'   => 'pnrNo',
                     'label'   => 'PNR No',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'phoneNo',
                     'label'   => 'Phone Number',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'itineraryId',
                     'label'   => 'Itinerary Id',
                     'rules'   => 'required|callback_itinerary_check'
                  )
         );

$config['add_hotel'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
               array(
                     'field'   => 'hotelName',
                     'label'   => 'Hotel Name',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'checkInDate',
                     'label'   => 'Check In Date',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'checkInTime',
                     'label'   => 'Check In Time',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'checkOutDate',
                     'label'   => 'Check Out Date',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'checkOutTime',
                     'label'   => 'Check Out Time',
                     'rules'   => 'required'
                  ),              
               array(
                     'field'   => 'address',
                     'label'   => 'Address',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'phoneNo',
                     'label'   => 'Phone Number',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'itineraryId',
                     'label'   => 'Itinerary Id',
                     'rules'   => 'required|integer|callback_itinerary_check'
                  )
         );

$config['add_car_rental'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
               array(
                     'field'   => 'pickupAddress',
                     'label'   => 'Pickup Address',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'dropAddress',
                     'label'   => 'Drop Address',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'pickupDate',
                     'label'   => 'Pickup Date',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'pickupTime',
                     'label'   => 'Pickup Time',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'returnDate',
                     'label'   => 'Return Date',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'returnTime',
                     'label'   => 'Return Time',
                     'rules'   => 'required'
                  ),               
               array(
                     'field'   => 'phoneNo',
                     'label'   => 'Address',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'phoneNo',
                     'label'   => 'Phone Number',
                     'rules'   => 'required'
                  ),

               array(
                     'field'   => 'companyName',
                     'label'   => 'Company Name',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'itineraryId',
                     'label'   => 'Itinerary Id',
                     'rules'   => 'required|integer|callback_itinerary_check'
                  )
         );

$config['add_notes'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
			  array(
                     'field'   => 'notesTitle',
                     'label'   => 'Notes Title',
                     'rules'   => 'required'
                  ),
			  array(
                     'field'   => 'notesData',
                     'label'   => 'Notes Data',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'itineraryId',
                     'label'   => 'Itinerary Id',
                     'rules'   => 'required|integer|callback_itinerary_check'
                  )
         );


$config['add_moment'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
               array(
                     'field'   => 'momentImage',
                     'label'   => 'Image',
                     'rules'   => 'required|callback_handle_moment_pic_upload'
                  ),
               array(
                     'field'   => 'itineraryId',
                     'label'   => 'Itinerary Id',
                     'rules'   => 'required|integer|callback_itinerary_check'
                  )
         );

$config['add_itinerary'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
               array(
                     'field'   => 'itineraryName',
                     'label'   => 'Itinerary Name',
                     'rules'   => 'required'
                  ),
               array(
                     'field'   => 'itineraryDate',
                     'label'   => 'Itinerary Date',
                     'rules'   => 'required'
                  )
         );

$config['fetch'] = array(
			    array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
             array(
                     'field'   => 'itineraryId',
                     'label'   => 'Itinerary Id',
                     'rules'   => 'required|integer'
                  )
         );

$config['fetchItinerary'] = array(
             array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  )
         );

$config['fetch_notes'] = array(
			  		array(
                     	'field'   => 'userId',
                     	'label'   => 'User Id',
                     	'rules'   => 'required|integer|callback_user_check'
                  	)
         		);
$config['fetch_notes1'] = array(
               array(
                        'field'   => 'userId',
                        'label'   => 'User Id',
                        'rules'   => 'required|integer|callback_user_check'
                     ),
               array(
                        'field'   => 'itineraryId',
                        'label'   => 'Itinerary Id',
                        'rules'   => 'required|integer'
                     ),
               );

/*******DELETE**********/
$config['deleteFlight'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  )
         );

$config['deleteCarRental'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  )
         );

$config['deleteHotel'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  )
         );

$config['deleteItinerary'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
               array(
                     'field'   => 'itineraryId',
                     'label'   => 'Itinerary Id',
                     'rules'   => 'required|integer|callback_itinerary_check'
                  )
         );

$config['deleteMoments'] = array(
			   array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  )
         );

$config['deleteNotes'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  )
         );
$config['deleteItineraryNotes'] = array(
           array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
           array(
                     'field'   => 'itineraryId',
                     'label'   => 'Itinerary Id',
                     'rules'   => 'required|integer'
                  )
         );
/**********EDIT*************/
$config['editFlight'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
               array(
                     'field'   => 'flightId',
                     'label'   => 'Flight Id',
                     'rules'   => 'required|integer|callback_flight_check'
                  )
         );

$config['editCarRental'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
               array(
                     'field'   => 'carRentId',
                     'label'   => 'Car Rent Id',
                     'rules'   => 'required|integer|callback_car_rent_check'
                  )
         );

$config['editHotel'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
               array(
                     'field'   => 'hotelId',
                     'label'   => 'Hotel Id',
                     'rules'   => 'required|integer|callback_hotel_check'
                  )
         );

$config['editItinerary'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
               array(
                     'field'   => 'itineraryId',
                     'label'   => 'Itinerary Id',
                     'rules'   => 'required|integer|callback_itinerary_check'
                  )
         );
$config['editNotes'] = array(
			  array(
                     'field'   => 'userId',
                     'label'   => 'User Id',
                     'rules'   => 'required|integer|callback_user_check'
                  ),
               array(
                     'field'   => 'notesId',
                     'label'   => 'Notes Id',
                     'rules'   => 'required|integer|callback_notes_check'
                  )
         );
?>