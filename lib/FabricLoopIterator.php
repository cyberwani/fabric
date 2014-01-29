<?php

class FabricLoopIterator implements Iterator 
{

    private $position = 0;
    private $full_query;
    private $array;
    private $paginate;

    public function __construct( $loop, $paginate )
    {
        $this->position = 0;
        $this->full_query = $loop;
        $this->array = $loop->posts;
        $this->paginate = $paginate;
    }

    function rewind()
    {
        $this->position = 0;
        do_action_ref_array('loop_start', array($this->full_query));
    }

    function current()
    {
        setup_postdata( $this->array[$this->position] );
        return $this->array[$this->position];
    }

    function key()
    {
        return $this->position;
    }

    function next()
    {
        ++$this->position;
    }

    function valid()
    {
        $valid = isset($this->array[$this->position]);

        if (!$valid) {
        	do_action_ref_array('loop_end', array($this->full_query));

            if( $this->paginate )
                $this->doPagination($this->full_query);

        	wp_reset_postdata();
        }
        return $valid;
    }

    function doPagination($loop)
    {
		echo paginate_links( array(
			'base' => str_replace( 999999999, '%#%', get_pagenum_link( 999999999 ) ),
			'format' => '?page=%#%',
			'current' => max( 1, get_query_var('paged') ),
			'total' => $loop->max_num_pages
		) );
    }
}