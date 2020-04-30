<?php
    
    if(PerchUtil::get('success')) {
        $Alert->set('success', $Lang->get('Product orders successfully updated.'));
    }

    if(!in_array($view, ['cat_list', 'brand_list'])) {
        $Alert->set('info', $Lang->get('Drag and drop the Products to reorder them.'));
    }

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all Products'),
    ], $CurrentUser);

    include(__DIR__.'/list.smartbar.php');


    switch($view) {
        case 'cat_list':
            foreach($category_groups as $key => $category_group) {
                echo '<h2 class="divider"><div>' . $key .'</div></h2>';

                $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    
                $Listing->add_col([
                    'title'     => 'Category',
                    'value'     => 'catDisplayPath',
                    'edit_link' => $API->app_path() .'/reorder/?context=cat&catID=',
                    'icon'      => 'core/chart-pie',
                    'depth'     => function($item){
                        return (int)$item->catDepth()-1;
                    },
                ]);

                echo $Listing->render($category_group);
            }

        break;


        case 'brand_list':
            $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

            $Listing->add_col([
                'title'     => 'Brand',
                'value'     => 'title',
                'edit_link' => $API->app_path() .'/reorder/?context=brand&brandID=',
            ]);

            echo $Listing->render($brands);
        break;




        case 'cat_detail':
            include(__DIR__.'/_reorder.category.php');
        break;

        case 'brand_detail':
            include(__DIR__.'/_reorder.brand.php');
        break;

        default:
            include(__DIR__.'/_reorder.default.php');
    }