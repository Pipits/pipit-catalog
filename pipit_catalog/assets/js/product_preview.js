var pageUrl = window.location.href;


if(pageUrl.includes('apps/perch_shop_products/product/edit')) {
    var url = new URL(window.location.href);
    var id = url.searchParams.get("id");
    

    // add button
    var smartbar = document.querySelector('.smartbar ul');
    var previewUrl = '/perch/addons/apps/pipit_catalog/product_view.php?id='+id;

    let html = `<li class="smartbar-end smartbar-util">
        <a href="${previewUrl}" title="View" class="viewext viewext" target="_blank" rel="noopener">
            <svg role="img" width="14" height="14" class="icon icon-document" title="View" aria-label="Preview Draft"> <use xlink:href="/perch/core/assets/svg/core.svg#document"></use></svg> 
            <span>View</span>
        </a>
    </li>`

    smartbar.insertAdjacentHTML('beforeend', html);

} else {
    
}