<?php

final class Pagination {

    public $total = 0;
    public $page = 1;
    public $limit = 20;
    public $num_links = 5;
    public $url = '';
    public $text = 'Desde {start} hasta {end} de {total} ({pages} P&aacute;ginas)';
    public $text_first = '|&lt;';
    public $text_last = '&gt;|';
    public $text_next = '&gt;';
    public $text_prev = '&lt;';
    public $style_links = 'links';
    public $style_results = 'results';
    // load items with ajax
    public $ajax = false;
    public $ajaxTarget = "products";

    public function render() {
        $total = $this->total;

        if ($this->page < 1) {
            $page = 1;
        } else {
            $page = $this->page;
        }

        if (!$this->limit) {
            $limit = 10;
        } else {
            $limit = $this->limit;
        }

        $num_links = $this->num_links;
        $num_pages = ceil($total / $limit);

        $output = '';

        if ($page > 1) {
            if ($this->ajax) {
                $output .= ' <a onclick="paginate(\'' . str_replace('{page}', 1, $this->url) . '\')">' . $this->text_first . '</a> <a onclick="paginate(\'' . str_replace('{page}', $page - 1, $this->url) . '\')">' . $this->text_prev . '</a> ';
            } else {
                $output .= ' <a href="' . str_replace('{page}', 1, $this->url) . '">' . $this->text_first . '</a> <a href="' . str_replace('{page}', $page - 1, $this->url) . '">' . $this->text_prev . '</a> ';
            }
        }


        if ($num_pages > 1) {
            if ($num_pages <= $num_links) {
                $start = 1;
                $end = $num_pages;
            } else {
                $start = $page - floor($num_links / 2);
                $end = $page + floor($num_links / 2);

                if ($start < 1) {
                    $end += abs($start) + 1;
                    $start = 1;
                }

                if ($end > $num_pages) {
                    $start -= ($end - $num_pages);
                    $end = $num_pages;
                }
            }

            if ($start > 1) {
                $output .= ' .... ';
            }

            for ($i = $start; $i <= $end; $i++) {
                if ($page == $i) {
                    $output .= ' <b>' . $i . '</b> ';
                } else {
                    if ($this->ajax) {
                        $output .= ' <a onclick="paginate(\'' . str_replace('{page}', $i, $this->url) . '\')">' . $i . '</a> ';
                    } else {
                        $output .= ' <a href="' . str_replace('{page}', $i, $this->url) . '">' . $i . '</a> ';
                    }
                }
            }

            if ($end < $num_pages) {
                $output .= ' .... ';
            }
        }

        if ($page < $num_pages) {
            if ($this->ajax) {
                $output .= ' <a onclick="paginate(\'' . str_replace('{page}', $page + 1, $this->url) . '\')">' . $this->text_next . '</a> <a onclick="paginate(\'' . str_replace('{page}', $num_pages, $this->url) . '\')">' . $this->text_last . '</a> ';
            } else {
                $output .= ' <a href="' . str_replace('{page}', $page + 1, $this->url) . '">' . $this->text_next . '</a> <a href="' . str_replace('{page}', $num_pages, $this->url) . '">' . $this->text_last . '</a> ';
            }
        }

        if ($this->ajax) {
            /*
              $output .= "<script>
              function paginate(e,a) {
              jQuery.ajax ({
              'type':'get',
              'dataType':'html',
              'url':a,
              beforeSend:function(){},
              success:function(data){
              if (data) {
              jQuery('#' + e).append(data);
              }
              }
              });
              }
              </script>";
             */
            $output .= "<script>
            function paginate(a) {
                jQuery('#" . $this->ajaxTarget . "').animate({marginLeft:'-200%',opacity:0},500,function(){
                    jQuery('#" . $this->ajaxTarget . "').load(a,function(data){
                        jQuery('#" . $this->ajaxTarget . "').css({marginLeft:'200%'}).animate({marginLeft:'10px',opacity:1},500);
                    });
                });
                
            }
            </script>";
        }

        $find = array(
            '{start}',
            '{end}',
            '{total}',
            '{pages}'
        );

        $replace = array(
            ($total) ? (($page - 1) * $limit) + 1 : 0,
            ((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit),
            $total,
            $num_pages
        );

        return ($output ? '<div class="' . $this->style_links . '">' . $output . '</div>' : '') . '<div class="' . $this->style_results . '">' . str_replace($find, $replace, $this->text) . '</div>';
    }

}
