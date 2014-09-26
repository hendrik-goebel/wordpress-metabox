wordpress-metabox
=================

a class for easy creation of metaboxes  in wordpress

<h3>Initializion:</h3>

Create a new instance of the SMC_Metabox Class:
<pre>$metabox = new SMC_Metabox($id, $label, $post_type, $metabox_configuration, $context, $priority);</pre>

- $id: 'id' attribute of the edit screen section
- $label: Title of the edit screen section, visible to user
- $post_type: The type of Write screen on which to show the edit screen section ('post', 'page', 'dashboard', 'link', 'attachment' or 'custom_post_type' where custom_post_type is the custom post type slug
- $metabox_configuration: Array of Configuration settings (see below)
- $context: (optional) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side'). 
- $priority:  (optional) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low').


<h3>The Metabox_Configuration array</h3>

A Metabox is an array which holds several metafields. A single metafield is again an numerical array, which has the followong settings:

  0 = ID (string) The unique id of the field
  1 = Label (string) The label of the field, visible to the user
  2 = Type (string) The type of the html component. Possible values are:  "input" | "select" | "check" | "radio"
  3 = Validation (string) Currently only possible values: "required" | empty
  4 = Options (array) Defines options for select, check and radio types: array( array( "option-key" => "option-value"))

 <h3>Example initialization:</h3>
<pre>
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

        $metabox = new SMC_Metabox('SMC_metabox', __('Product Attributes'), 'post', $metabox_config, 'advanced', 'high');
</pre>


