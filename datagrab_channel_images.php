<?php

/**
 * DataGrab Channel Images fieldtype class
 *
 * @package   Datagrab
 * @author    Doug Black <webteam@americanbible.org>
 * MUST GET images to upload folder
 * Must get the basename
 * Write to the table
 */

class Datagrab_channel_images extends Datagrab_fieldtype {

  function register_setting( $field_name ) {
    return array(
      $field_name . "_filedir", 
      $field_name . "_fetch" 
    );
  }

  function display_configuration( $field_name, $field_label, $field_type, $data ) {

    // Get current saved setting
    if( isset( $data["default_settings"]["cf"] ) ) {
      $default = $data["default_settings"]["cf"];
    } else {
      $default = array();
    }

    // Get upload folders
    $this->EE->db->select( "id, name" );
    $this->EE->db->from( "exp_upload_prefs" );
    $this->EE->db->order_by( "id" );
    $query = $this->EE->db->get();
    $folders = array();
    foreach( $query->result_array() as $row ) {
      $folders[ $row["id"] ] = $row["name"];
    }

    // Build config form
    $config = array();
    $config["label"] = form_label($field_label);
    $config["value"] = "<p>" . 
      form_dropdown( 
        $field_name, 
        $data["data_fields"], 
        isset( $default[$field_name] ) ? $default[$field_name] : '' 
      ) . 
      "</p><p>Upload folder: " . NBS .
      form_dropdown( 
        $field_name . "_filedir", 
        $folders, 
        isset( $default[ $field_name . "_filedir" ] ) ? $default[ $field_name . "_filedir" ] : ''
        ) . 
      "</p><p>Fetch files from urls: " . NBS .
      form_dropdown( 
        $field_name . "_fetch", 
        array("No" , "Yes"), 
        isset( $default[ $field_name . "_fetch" ] ) ? $default[ $field_name . "_fetch" ] : ''
        ) . 
      "</p>";
        
    return $config;
  }

  function prepare_post_data( $DG, $item, $field_id, $field, &$data, $update = FALSE ) {  

    $data["field_id_".$field_id] = "";

    // Fetch file from data
    // For this one, we're just going to get the name
    // IF we want to write the file, we are not going to do so until the post entry
    // This way it will follow Channel Entries directory structure
    if( $DG->datatype->get_item( $item, $DG->settings["cf"][ $field ] ) != "" ) {
      $filename = $DG->_get_file( 
        $DG->datatype->get_item( $item, $DG->settings["cf"][ $field ] ),
        $DG->settings["cf"][ $field . '_filedir' ],
        $DG->settings["cf"][ $field . '_fetch' ] == 1 ? TRUE : FALSE
      );
  
      if( $filename !== FALSE ) {
        $data[ "field_id_" . $field_id ] = 'ChannelImages';
        $data["filename"] = $filename;
      }
    }

  }

  function post_process_entry ($DG, $item, $field_id, $field, &$data, $entry_id)
  {
    
    //

    // NOW, download the picture if we need it
    // If not, no harm no foul
    $justgetthefile = $DG->_get_ci_file( 
      $DG->datatype->get_item( $item, $DG->settings["cf"][ $field ] ),
      $entry_id,
      $DG->settings["cf"][ $field . '_filedir' ],
      $DG->settings["cf"][ $field . '_fetch' ] == 1 ? TRUE : FALSE
    );

    // Get ChannelImages info
    ee()->db->where('entry_id');
    ee()->db->where('field_id');
    $query = ee()->db->get('channel_images',1);
    $filename = preg_replace('/{(.*?)}/' , '', $data["filename"]);
    $mime = ($filename == 'png' ? 'image/png' : 'image/jpeg');

    $row = array(
      "channel_id" => $data['channel_id'],
      "field_id" => $field_id,
      "upload_date" => strtotime(date("Y/m/d H:i:s")),
      "image_order" => 1,
      "filename" => $filename,
      "extension" => pathinfo($filename, PATHINFO_EXTENSION),
      "mime" => $mime,
      "title" => basename($filename),
      "url_title" => basename($filename)
      );

    if($query->num_rows() > 0) {
      ee()->db->where('entry_id', $entry_id);
      ee()->db->update('channel_images', $row);
    } else {
      ee()->db->set('entry_id', $entry_id);
      ee()->db->insert('channel_images', $row);
    }
  }

}

?>