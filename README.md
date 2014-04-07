wordpress-metabox
=================

a class for easy creation of metaboxes  in wordpress

Initializion:

new SMC_Metabox(Metabox_ID, Metabox_LABEL, Metabox_Configuration, Post_Label )


 - Metabox_ID // string

 - Metabox_LABEL // string

 - Post_Type // string: "Blog", "Page", or Custom Post Type

 - Metabox_Configuration // array, see blow

 - Post_Label // String (will be used in error messages)




the Metabox_Configuration array:

  0 = ID // string

  1 = Label //  string

  2 = Type // string: "input" | "select" | "check" | "radio"

  3 = Validation  // string: "required" | empty

  4 = Options: defines  options for select, check and radio types:

           array( array( "option-key" => "option-value"))



 Example initialization:

      $metabox_config= array(

            array('smc-name', __('Name'), 'input'),
            array('smc-lastname', __('Last Name'), 'input', 'required'),
            array('smc-street', __('Street'), 'input', 'required'),
            array('smc-color', __('Color'), 'select', 'required', array(
                    '-' => '-',
                    'red' => 'Red',
                    'blue' => 'Blue',
                    'green' => 'green'
                )),
            array('smc-size', __('Size'), 'radio', 'required', array(
                    '-' => '-',
                    'l' => 'L',
                    'm' => 'M',
                    'xl' => 'XL'
                )),
            array('smc-check', __('Check'), 'check', '', array(
                    'check1' => 'Check 1',
                    'check2' => 'Check 2',
                    'check3' => 'Check 3'
                ))
        );

        $metabox = new SMC_Metabox('SMC_metabox', __('Product Attributes'), $this->post_type, $metabox_config 'Product');


