<?php

class PipitCatalog_Helper
{
    function get_product_image($Item, $API=false, $Settings=false)
    {
        
        if (!$API)
        {
            $API  = new PerchAPI(1.0, 'pipit_catalog');
        }
        
        if(!$Settings)
        {
            $Settings = $API->get('Settings');
        }
        
        /*
        *	default values in template
        *	$thumb_w = 'w80';
        *	$thumb_h = 'h80';
        *	$thumb_crop = 'c1';
        *	$thumb_density = '@1.6x';
        */
        
        $thumb_w = $thumb_h = $thumb_density = '';
        $thumb_crop = 'c0';
        
        if($Settings->get('pipit_catalog_thumbW')->val())
        {
            $thumb_w = 'w'.$Settings->get('pipit_catalog_thumbW')->val();
        }
        
        if($Settings->get('pipit_catalog_thumbH')->val())
        {
            $thumb_h = 'h'.$Settings->get('pipit_catalog_thumbH')->val();
        }
        
        if($Settings->get('pipit_catalog_thumbCrop')->val())
        {
            $thumb_crop = 'c1';
        }
        
        if($Settings->get('pipit_catalog_thumbDensity')->val())
        {
            $thumb_density = '@'.$Settings->get('pipit_catalog_thumbDensity')->val().'x';
        }
            		
        $thumb_size = $thumb_w.$thumb_h.$thumb_crop.$thumb_density;
			
            
        
        $dynamic_fields = PerchUtil::json_safe_decode($Item->productDynamicFields(), true);
        $noImg = '<img class="listing__thumb" src="'.$API->app_path().'/assets/images/no-image.png'.'" />';
        
        // check if product image exists
        if(isset($dynamic_fields['image']))
        {
            $image = $dynamic_fields['image'];
            
            $default_path = $image['_default'];
            $default_name = basename($default_path);
            
            //global $thumb_size;

            // check if thumb size in Settings exists
            // otherwise fallback to default Assets thumb
            if(array_key_exists($thumb_size, $image['sizes']))
            {
                $thumb_name = $image['sizes'][$thumb_size]['path'];							
                $thumb_path = str_replace($default_name, $thumb_name, $default_path);
                return '<img class="listing__thumb" src="'.$thumb_path.'" />';
            }
            else if(array_key_exists('thumb', $image['sizes']))
            {
                $thumb_name = $image['sizes']['thumb']['path'];							
                $thumb_path = str_replace($default_name, $thumb_name, $default_path);
                return '<img class="listing__thumb" src="'.$thumb_path.'" />';
            }
            else
            {
                return $noImg;
            }
        }
        else
        {
            return $noImg;
        }
    }



}
