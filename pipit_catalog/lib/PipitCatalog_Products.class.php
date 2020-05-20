<?php

class PipitCatalog_Products {

    /**
     * Get product category sets from product.html
     * @return array
     */
    static function get_category_sets($return_instances = true) {
        $API = new PerchAPI(1.0, 'perch_shop');
        $PerchSets = new PerchCategories_Sets($API);
        $Template = $API->get('Template');
        $Template->set('shop/products/product.html', 'shop');

        $tags = $Template->find_all_tags('categories');
        $sets = [];

        if(PerchUtil::count($tags)) {
            foreach($tags as $Tag) {
                if($Tag->is_set('set')) $sets[] = $Tag->set();
            }
        }

        
        $sets = array_values( array_unique($sets) );
        if(!$return_instances) return $sets;

        
        $set_instances = [];
        foreach($sets as $set) {
            $Set = $PerchSets->get_one_by('setSlug', $set);
            if(is_object($Set)) $set_instances[] = $Set;
        }

        return $set_instances;
    }



    /**
     * 
     */
    static function get_resource_dir() {
        $dir = PERCH_PATH . '/pipit_catalog';
        if(defined('PIPIT_CATALOG_RESPATH')) $dir = PIPIT_CATALOG_RESPATH . '/pipit_catalog';

        $dir = PerchUtil::file_path($dir);
        if(!is_dir($dir)) mkdir($dir);
        
        return $dir;
    }




    /**
     * 
     */
    public function get_ordered_products_for($type, $id, $opts=[], $active_only=true, $return_instances=true) {
        $ShopAPI    = new PerchAPI(1, 'perch_shop');
        $API    = new PerchAPI(1, 'pipit_catalog');
        $Products     = new PerchShop_Products($ShopAPI);

        $DB = $API->get('DB');
        $Template = $ShopAPI->get('Template');
        $Brand = $Category = false;

        $Paging = new PerchPaging();
        $Paging->disable();
        $sql_limit = '';
        
        $dir = PipitCatalog_Products::get_resource_dir();
        $file = '';


        $opts = array_merge([
            'template'          => 'products/list.html',
            'skip-template'	    => false,
            'return-html'	    => false,
            'variants'		    => false,
            'paginate'          => false,
            'sub-categories'    => true,
        ], $opts);

        $Template->set('shop/'.$opts['template'], 'shop');



        // get_filtered_listing()
        $instance_opts = [
            'return-objects' => true,
            'sort'           => 'productOrder',
            'sort-type'      => 'numeric',
            'sort-order'     => 'ASC',
        ];

        $where_callback = function (PerchQuery $Query) {
            $Query->where[] =  'productDeleted IS NULL';
            $Query->where[] = 'parentID IS NULL';
            return $Query;
        };
        


        

        // pagination
        if ($opts['paginate']) {
            if (isset($opts['pagination-var'])) {
                $Paging = new PerchPaging($opts['pagination-var']);
            }

            $Paging->enable();
            $count = isset($opts['count']) ? (int)$opts['count'] : 10;
            $Paging->set_per_page($count);

            $sql_limit = ' ' . $Paging->limit_sql();

        } elseif (isset($opts['count'])) {

            $count = (int) $opts['count'];

            if (isset($opts['start'])) {
                $start = (((int) $opts['start'])-1). ',';
            }else{
                $start = '';
            }

            $sql_limit = ' LIMIT ' . $start.$count;
        }



        
        
        
        // generate file path and sql WHERE
        $where = [];
        if($id) {
            switch($type) {
                case 'category':
                    $Categories = new PerchCategories_Categories();
                    $Category = (is_numeric($id)) ? $Categories->find($id) : $Categories->get_one_by('catPath', $id);
                    if(!is_object($Category)) break;
    
                    $id = $Category->id();
                    $catPath = $Category->catPath();
                    $file = "$dir/cat_$id.json";


                    $sub_category = '';
                    if($opts['sub-categories']) {
                        $sub_category = "idx.indexKey='_category' AND idx.indexValue LIKE " . $DB->pdb($catPath . '%') . " OR ";
                    }


                    $where[] = " ( $sub_category idx.indexKey='_category' AND idx.indexValue=" . $DB->pdb($catPath) . ")";

                break;
    
    
    
    
                case 'brand':
                    $Brands = new PerchShop_Brands();
                    $Brand = (is_numeric($id)) ? $Brands->find($id) : $Brands->get_one_by('brandSlug', $id);
                    if(!is_object($Brand)) break;
    
                    $id = $Brand->id();
                    $file = "$dir/brand_$id.json";
                    $where[] = " (idx.indexKey='brand' AND idx.indexValue=". $DB->pdb($id) .")";
                break;
            }
        }



        




        $rows = $items = $ordered_products = [];
        
        if(is_file($file)) {
            $ordered_products = json_decode(file_get_contents($file), true);
        }


        if(PerchUtil::count($ordered_products)) {

            $table = PERCH_DB_PREFIX . 'shop_products';
            $index_table = PERCH_DB_PREFIX . 'shop_index';
            $productIDs = array_column($ordered_products, 'id');
            $glued_productIDs = implode(',', $productIDs);

            

            $sql_active = $sql_having = '';
            if($active_only) {
                $sql_active = ' AND `productStatus`=' . $DB->pdb(1) . ' ';
            }


            $Query = $this->_get_sql_filters($opts);
            if(PerchUtil::count($Query->where)) {
                $where = array_merge($where, $Query->where);

                $sql_where = implode(' OR ', $where);
                $sql_having = 'HAVING COUNT(*)=' . (PerchUtil::count($where));
            } else {
                $sql_where = $where[0];
            }

            $sql_alt = "SELECT  idx.itemID, main.*, idx2.indexValue as sortval FROM $index_table idx
                        JOIN $table main ON idx.itemID=main.productID AND idx.itemKey='productID'
                        JOIN $index_table idx2 ON idx.itemID=idx2.itemID AND idx.itemKey='productID' AND idx.itemKey='productID' AND idx2.indexKey='title' 
                        WHERE 1=1 AND ( $sql_where ) AND idx.itemID=idx2.itemID AND idx.itemKey=idx2.itemKey  ";
                    

            


            $rows = $DB->get_rows($Paging->select_sql() . " tbl.* FROM ($sql_alt) as tbl
                        WHERE (`productDeleted` IS NULL AND `parentID` IS NULL $sql_active)
                        GROUP BY itemID, sortval $sql_having
                        ORDER BY FIELD(productID, $glued_productIDs) ASC" . $sql_limit);


            if ($Paging->enabled()) {
                $Paging->set_total($DB->get_count($Paging->total_count_sql()));
            }

            
            $rows = $Products->runtime_pretemplate_callback($rows, $opts);
            $rows = $this->_merge_dynamic_fields($rows);
            $rows = $this->_apply_each_callback($rows, $opts);

            $items = $Products->return_instances($rows);
            if($return_instances) return $items;


        } else {

            switch($type) {
                case 'category':
                    if(is_object($Category)) {
                        $opts['category'] = $instance_opts['category'] = [$Category->catPath()];

                        if($return_instances) {
                            return $Products->get_filtered_listing($instance_opts, $where_callback, true);
                        } else {

                            if(!$opts['sub-categories']) {
                                unset($opts['category']);
                                $cat_filter = ['filter' => '_category', 'value' => $Category->catPath()];
                                
                                $opts = $this->_filters_to_array($opts);
                                $opts['filter'][] = $cat_filter;

                                if(count($opts['filter']) > 1) {
                                    $opts['filter-mode'] = 'ungrouped';
                                }
                            }


                            return perch_shop_products($opts);
                        }

                    }
                break;

                case 'brand':
                    if(is_object($Brand)) {
                        $opts = $this->_filters_to_array($opts);
                        $instance_opts = $this->_filters_to_array($instance_opts);
                        $opts['filter'][] = $instance_opts['filter'][] = ['filter' => 'brand', 'value' => $Brand->id()];

                        if(count($opts['filter']) > 1) {
                            $opts['filter-mode'] = 'ungrouped';
                            $instance_opts['filter-mode'] = 'ungrouped';
                        }
                        
                        if($return_instances) {
                            return $Products->get_filtered_listing($instance_opts, $where_callback, true);
                        } else {
                            return perch_shop_products($opts);
                        }
                    }
                break;
            }

        }

        


        $processed_vars = [];
        if($opts['skip-template']) {
            
            if (PerchUtil::count($items)) {
                foreach($items as $Item) {
                    $Item->prefix_vars  = false;
                    $processed_vars[] = $Item->to_array();
                }
            }


            // process complex fields
            // $category_field_ids = $Template->find_all_tag_ids('categories');

            foreach($processed_vars as &$item) {
                if (PerchUtil::count($item)) {
                    foreach($item as $key => &$field) {

                        // TODO: convert catIDs to catPaths

                        if (is_array($field) && isset($field['processed'])) {
                            $field = $field['processed'];
                        }
                        if (is_array($field) && isset($field['_default'])) {
                            $field = $field['_default'];
                        }
                    }
                }
            }
        }




        $html = '';
        if(!$opts['skip-template'] || $opts['return-html']) {
            

            if(PerchUtil::count($items)) {
                if ($Paging->enabled()) {
                    $paging_array = $Paging->to_array($opts);

                    // merge in paging vars
                    if (PerchUtil::count($items)) {
                        foreach($items as &$Item) {
                            foreach($paging_array as $key => $val) {
                                $Item->squirrel($key, $val);
                            }
                        }
                    }


                }


                
                $html = $Template->render_group($items);

            } else {
                $Template->use_noresults();
                $html = $Template->render([]);
            }
        }



        
        if ( strpos($html, '<perch:') !== false ) {
            $Template = new PerchTemplate();
            $html = $Template->apply_runtime_post_processing($html);
        }
        

        $processed_vars['html'] = $html;




        return $processed_vars;
    }








    

    /**
     * 
     */
    private function _merge_dynamic_fields($rows) {
        if(PerchUtil::count($rows)) {
            foreach($rows as $key => $item) {
                if (isset($item['productDynamicFields']) && trim($item['productDynamicFields']) != '') {
                    $tmp = PerchUtil::json_safe_decode($item['productDynamicFields'], true);
                    $rows[$key] = array_merge($item, $tmp);
                }
            }
        }


        return $rows;
    }


    /**
     * 
     */    
    private function _apply_each_callback($rows, $opts) {
        if (PerchUtil::count($rows) && isset($opts['each']) && is_callable($opts['each'])) {
            $content = array();
            foreach($rows as $item) {
                $tmp = $opts['each']($item);
                $content[] = $tmp;
            }
            $rows = $content;
        }

        return $rows;
    }



    /**
     * 
     */
    private function _filters_to_array($opts) {
        if (isset($opts['filter']) && (isset($opts['value']) || is_array($opts['filter']))) {
            if (!is_array($opts['filter']) && isset($opts['value'])) {
                $opts['filter'] = [
                    [
                        'filter'     => $opts['filter'],
                        'value'      => $opts['value'],
                        'match'      => (isset($opts['match']) ? $opts['match'] : 'eq'),
                    ]
                ];


                unset($opts['match'], $opts['value']);
            }
        }



        return $opts;
    }


    /**
     * 
     */
    private function _get_sql_filters($opts) {
        $API    = new PerchAPI(1.0, 'pipit_catalog');
        $DB     = $API->get('DB');
        $Query  = new PerchQuery();
        $filters = [];

        $opts = $this->_filters_to_array($opts);
        if(isset($opts['filter'])) $filters = $opts['filter'];


        foreach($filters as $filter) {
            $key = $filter['filter'];
            $val = $filter['value'];
            $match = isset($filter['match']) ? $filter['match'] : 'eq';

            if (is_numeric($val)) $val = (float) $val;

            switch ($match) {
                case 'eq':
                case 'is':
                case 'exact':
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue='.$DB->pdb($val).')';
                    break;

                case 'neq':
                case 'ne':
                case 'not':
                case '!eq':
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue != '.$DB->pdb($val).')';
                    break;

                case 'gt':
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue > '.$DB->pdb($val).')';
                    break;

                case '!gt':
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue !> '.$DB->pdb($val).')';
                    break;
                    
                case 'gte':
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue >= '.$DB->pdb($val).')';
                    break;
                    
                case '!gte':
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue !>= '.$DB->pdb($val).')';
                    break;
                    
                case 'lt':
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue < '.$DB->pdb($val).')';
                    break;
                    
                case '!lt':
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue !< '.$DB->pdb($val).')';
                    break;
                    
                case 'lte':
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue <= '.$DB->pdb($val).')';
                    break;
                    
                case '!lte':
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue !<= '.$DB->pdb($val).')';
                    break;
                    
                case 'contains':
                    $v = str_replace('/', '\/', $val);
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue REGEXP '.$DB->pdb('[[:<:]]'.$v.'[[:>:]]').')';
                    break;
                    
                case 'notcontains':
                case '!contains':
                    $v = str_replace('/', '\/', $val);
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue NOT REGEXP '.$DB->pdb('[[:<:]]'.$v.'[[:>:]]').')';
                    break;
                    
                case 'regex':
                case 'regexp':
                    $v = str_replace('/', '\/', $val);
                    $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue REGEXP '.$DB->pdb($v).')';
                    break;
                    
                case 'between':
                case 'betwixt':
                    $vals  = explode(',', $val);
                    if (PerchUtil::count($vals)==2) {

                        $vals[0] = trim($vals[0]);
                        $vals[1] = trim($vals[1]);

                        if (is_numeric($vals[0]) && is_numeric($vals[1])) {
                            $vals[0] = (float)$vals[0];
                            $vals[1] = (float)$vals[1];
                        }

                        $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND (idx.indexValue > '.$DB->pdb($vals[0]).' AND idx.indexValue < '.$DB->pdb($vals[1]).'))';
                    }
                    break;
                    
                case '!between':
                case '!betwixt':
                    $vals  = explode(',', $val);
                    if (PerchUtil::count($vals)==2) {

                        $vals[0] = trim($vals[0]);
                        $vals[1] = trim($vals[1]);

                        if (is_numeric($vals[0]) && is_numeric($vals[1])) {
                            $vals[0] = (float)$vals[0];
                            $vals[1] = (float)$vals[1];
                        }

                        $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND (idx.indexValue !> '.$DB->pdb($vals[0]).' AND idx.indexValue !< '.$DB->pdb($vals[1]).'))';
                    }
                    break;
                    
                case 'eqbetween':
                case 'eqbetwixt':
                    $vals  = explode(',', $val);
                    if (PerchUtil::count($vals)==2) {
                        $vals[0] = trim($vals[0]);
                        $vals[1] = trim($vals[1]);

                        if (is_numeric($vals[0]) && is_numeric($vals[1])) {
                            $vals[0] = (float)$vals[0];
                            $vals[1] = (float)$vals[1];
                        }

                        $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND (idx.indexValue >= '.$DB->pdb($vals[0]).' AND idx.indexValue <= '.$DB->pdb($vals[1]).'))';

                    }
                    break;
                    
                case '!eqbetween':
                case '!eqbetwixt':
                    $vals  = explode(',', $val);
                    if (PerchUtil::count($vals)==2) {
                        $vals[0] = trim($vals[0]);
                        $vals[1] = trim($vals[1]);

                        if (is_numeric($vals[0]) && is_numeric($vals[1])) {
                            $vals[0] = (float)$vals[0];
                            $vals[1] = (float)$vals[1];
                        }

                        $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND (idx.indexValue !>= '.$DB->pdb($vals[0]).' AND idx.indexValue !<= '.$DB->pdb($vals[1]).'))';

                    }
                    break;
                    
                case 'in':
                case 'within':
                    $vals  = explode(',', $val);
                    if (PerchUtil::count($vals)) {
                        $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue IN ('.$DB->implode_for_sql_in($vals).'))';
                    }
                    break;
                    
                case '!in':
                case '!within':
                    $vals  = explode(',', $val);
                    if (PerchUtil::count($vals)) {
                        $Query->where[] = '(idx.indexKey='.$DB->pdb($key).' AND idx.indexValue NOT IN ('.$DB->implode_for_sql_in($vals).'))';
                    }
                    break;
            }
        }


        return $Query;
    }
    
}