<?php

/**
 * A class for easy creation of metaboxes in wordpress
 * @author Hendrik Göbel
 * @mail mail@hendrikgoebel.de
 * 
 *  
 * the $metabox_config array:
 *  0 = ID // string
 *  1 = Label //  string
 *  2 = Type // string: "input" | "select" | "check" | "radio"
 *  3 = Validation  // string: "required" | empty
 *  4 = Options: defines  options for select, check and radio types:
 *
 *          array( array( "option-key" => "option-value"))
 * 
 */

class SMC_Metabox {

    private $metabox_config;
    private $post_type;
    private $label;
    private $id;
    private $context;
    private $priority;
    private $error_message;

    public function __construct($id, $title = 'Attributes', $post_type, $metabox_config, $context = 'advanced', $priority = 'high')
    {

		
        $this->post_type = $post_type;
        $this->id = $id;
        $this->metabox_config = $metabox_config;
        $this->title = $title;
        $this->context = $context;
        $this->priority = $priority;


        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_metabox_data'));
        add_filter('post_updated_messages', array($this, 'generate_messages'));
    }

    public function add_meta_boxes()
    {
	
        add_meta_box($this->id, $this->title, array($this, 'display_meta_boxes'), $this->post_type, $this->context, $this->priority);
		
    }

    public function display_meta_boxes()
    {
        global $post;
			
		
        $data = array();
        $data[$this->id . '_meta_nonce'] = wp_create_nonce(wp_create_nonce($this->id . "-meta"));
		
        $data = array();
        // Get the existing values from database
	
        $output = '<table class="form-table">';
        foreach ($this->metabox_config as $item)
        {
            $value = get_post_meta($post->ID, $item[0], true);

            $output .= '<tr>';

            $req = '';
            if (strpos($item[3], 'required') !== false)
            {
                $req .= '*';
            }
            $output .= '<th><label for="' . $item[1] . '">' . $item[1] . $req . '</label></th>';

            switch ($item[2])
            {
                case 'input':
                    
                    $output .= '<td><input class="widefat" name="' . $item[0] . '" id="' . $item[0] . '" type="text" value="' . $value . '" /></td>';
                    break;
                
                case 'select':
                    
                    $output .= '<td><select class="widefat" name="' . $item[0] . '" id="' . $item[0] . '">';

                    foreach ($item[4] as $option_key => $option_value)
                    {
                        $option_key == $value ? $select = 'selected="selected"' : $select = '';
                        $output .= '<option ' . $select . ' value="' . $option_key . '">' . $option_value . '</opton>';
                    }
                    $output .= '</td>';
                    break;
                    
                case 'check':
                    
                    $output .= '<td>';
                    
                    foreach ($item[4] as $option_key => $option_value)
                    {
                        if (strpos($value, $option_key) !== false)
                        {
                            $checked = 'checked="checked""';
                        }
                        else
                            $checked = '';

                        $output .= '<input class="selectit widefat" type="checkbox" ' . $checked . ' name="' . $item[0] . '[]" value="' . $option_key . '">' . $option_value . '</br>';
                    }
                    $output .= '</td>';
                    break;

                case 'radio':
                    
                    $output .= '<td>';

                    $i = 0;
                    foreach ($item[4] as $option_key => $option_value)
                    {
                        if ($value === $option_key)
                        {
                            $checked = 'checked="checked""';
                        }
                        else
                            $checked = '';
                        $output .= '<input  ' . $checked . ' type="radio" class="widefat" name="' . $item[0] . '" value="' . $option_key . '">' . $option_value . '</br>';
                        $i++;
                    }
                    
                    $output .= '</td>';
                    break;
            }
            $output .= '</tr>';
        }
        
        $output .= '</table>';
        

        echo $output;
    }

    public function generate_messages($messages)
    {
        global $post, $post_ID;
        $this->error_message = get_transient("product_error_message_$post->ID");
        $message_no = isset($_GET['message']) ? $_GET['message'] : '0';
        delete_transient("product_error_message_$post->ID");
        
        if (!empty($this->error_message))
        {
            $messages[$this->post_type] = array("$message_no" => $this->error_message);
        }

        return $messages;
    }

    public function save_metabox_data()
    {
        global $post;

        if ($this->post_type == $_POST['post_type'] && current_user_can('edit_post', $post->ID))
        {
            $this->error_message = '';
            $data = array();

            foreach ($this->metabox_config as $item)
            {
                if (is_array($_POST[$item[0]]))
                    $post_data = addslashes(htmlentities(trim(implode(',', $_POST[$item[0]]))));
                else
                    $post_data = (string) addslashes(htmlentities(trim($_POST[$item[0]])));


                if (empty($post_data) || $post_data == '-')
                {
                    if (strpos($item[3], 'required') !== false)
                    {
                        $this->error_message .= $item[1] . __(' cannot be empty') . '</br>';
                    }
                }

                $data[$item[0]] = $post_data;
            }

            foreach ($data as $item_key => $item_value)
            {
                update_post_meta($post->ID, $item_key, $item_value);
            }
            
            if (!empty($this->error_message))
            {

                remove_action('save_post', array($this, 'save_metabox_data'));
                $post->post_status = "draft";
                wp_update_post($post);
                add_action('save_post', array($this, 'save_metabox_data'));
                $this->error_message = __('Saving failed.<br/>') . $this->error_message;
                set_transient("product_error_message_$post->ID", $this->error_message, 60 * 10);
            }
        }
    }
}
