<div class="f-16 f-r mt-10 mr-20">
    <?php
        $totalcount = $component -> get_totalcount();
        import("ORG.Util.Page");
        $page = new Page($totalcount, SYS_PAGE_SIZE);
        foreach($_REQUEST as $key=>$val) {
            $page->parameter .= "$key=".urlencode($val).'&';
        }
        echo $page->show();
    ?>
</div>



