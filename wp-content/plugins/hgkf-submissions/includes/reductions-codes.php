<?php

class reduction_codes extends WP_List_Table
{
    function __construct()
    {
        global $status, $page;
        parent::__construct(array(
            'singular' => __('kortingscode', 'reductioncodes'),
            'plural' => __('kortingscodes', 'reductioncodes'),
            'ajax' => false
        ));

        add_action('admin_head', array($this, 'admin_header'));
    }

    function prepare_reduction_codes()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->items = self::get_reduction_codes();
    }

    /**
     * Retrieve submission data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_reduction_codes($per_page = 100, $page_number = 1)
    {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}submission_reduction_codes";
        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;

        $result = $wpdb->get_results($sql, 'ARRAY_A');
        return $result;
    }

    function column_code($item) {
        $path = 'admin.php?page=edit_reduction_code';
        $editUrl = admin_url($path);

        $actions = array(
            'edit'      => sprintf('<a href="%s&action=%s&id=%s">Edit</a>',$editUrl,'edit_reduction_code',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
        );

        return sprintf('%1$s %2$s', $item['code'], $this->row_actions($actions) );
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'code':
            case 'ticket_price':
            case 'description':
                return $item[ $column_name ];
            case 'free_parking_ticket':
                return $item[ $column_name ] == 1 ? 'Ja' : 'Nee';
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }


    function get_columns()
    {
        $columns = array(
            'code' => __('Code', 'reductioncodes'),
            'ticket_price' => __('Ticket prijs', 'reductioncodes'),
            'free_parking_ticket' => __('Gratis parkeerticket', 'reductioncodes'),
            'description' => __('Beschrijving', 'reductioncodes')
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'code' => array('code', false),
            'ticket_price' => array('ticket_price', false),
            'free_parking_ticket' => array('free_parking_ticket', false),
            'description' => array('description', false)
        );
        return $sortable_columns;
    }
}

?>