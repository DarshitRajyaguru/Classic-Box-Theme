<form method="get" id="searchform" role="search" action="<?php echo home_url('/'); ?>">
    <div>
        <label for="s" class="screen-reader-text">Search for: </label>
        <input type="text" name="s" id="s" value="" placeholder="<?php the_search_query(); ?>"/>
        <input type="submit" id="searchsubmit" value="search"/>
    </div>
</form>