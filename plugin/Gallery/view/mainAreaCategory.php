<?php
// if there is no section display only the dateAdded row for the selected category
if (!empty($currentCat) && empty($_GET['showOnly'])) {
    if (empty($_GET['page'])) {
        $_GET['page'] = 1;
    }
    $_REQUEST['current'] = $_GET['page'];
    
    echo '<!-- '.basename(__FILE__).' -->';

    include $global['systemRootPath'] . 'plugin/Gallery/view/modeGalleryCategoryLive.php';
    unset($_POST['sort']);
    $_POST['sort']['v.created'] = "DESC";
    $_POST['sort']['likes'] = "DESC";
    $_GET['catName'] = $currentCat['clean_name'];
    $_REQUEST['rowCount'] = $obj->CategoriesRowCount * 3;
    $videos = Video::getAllVideos("viewableNotUnlisted", false, !$obj->hidePrivateVideos);
    global $contentSearchFound;
    if(empty($contentSearchFound)){
        $contentSearchFound = !empty($videos);
    }
    $currPage = getCurrentPage();
    global $categoryLiveVideos;
    if(empty($categoryLiveVideos) && $currPage<2){
        $categoryLiveVideos = getLiveVideosFromCategory($currentCat['id']);
        if(!empty($categoryLiveVideos)){
            $videos = array_merge($categoryLiveVideos, $videos);
        }
    }
    if (!empty($videos)) {
        ?>
        <div class="clear clearfix" id="Div<?php echo $currentCat['clean_name']; ?>">
            <?php
            if (canPrintCategoryTitle($currentCat['name'])) {
                ?>
                <h3 class="galleryTitle">
                    <a class="btn-default" href="<?php echo $global['webSiteRootURL']; ?>cat/<?php echo $currentCat['clean_name']; ?>">
                        <i class="<?php echo $currentCat['iconClass']; ?>"></i> <?php echo $currentCat['name']; ?>
                    </a>
                    <?php
                    if (!isHTMLEmpty($currentCat['description'])) {
                        $duid = uniqid();
                        $titleAlert = str_replace(array('"', "'"), array('``', "`"), $currentCat['name']);
                        ?>
                        <a href="#" class="pull-right" onclick='avideoAlert("<?php echo $titleAlert; ?>", "<div style=\"max-height: 300px; overflow-y: scroll;overflow-x: hidden;\" id=\"categoryDescriptionAlertContent<?php echo $duid; ?>\" ></div>", "");$("#categoryDescriptionAlertContent<?php echo $duid; ?>").html($("#categoryDescription<?php echo $duid; ?>").html());return false;' ><i class="far fa-file-alt"></i> <?php echo __("Description"); ?></a>
                        <div id="categoryDescription<?php echo $duid; ?>" style="display: none;"><?php echo $currentCat['description_html']; ?></div>
                        <?php
                    }
                    ?>
                </h3>
                <?php
            }
            ?>
            <div class="Div<?php echo $currentCat['clean_name']; ?>Section">
                <?php
                createGallerySection($videos, "", array(), true);
                ?>
            </div>
        </div>
        <!-- mainAreaCategory -->
        <div class="col-sm-12" style="z-index: 1;">
            <?php
            $_REQUEST['rowCount'] = $obj->CategoriesRowCount * 3;
            $_GET['catName'] = $currentCat['clean_name'];
            $total = Video::getTotalVideos("viewableNotUnlisted", false, !$obj->hidePrivateVideos);
            $totalPages = ceil($total / getRowCount());
            $page = getCurrentPage();
            if ($totalPages < $page) {
                $page = $totalPages;
            }
            $categoryURL = "{$global['webSiteRootURL']}cat/{$currentCat['clean_name']}/page/";
            //getPagination($total, $page = 0, $link = "", $maxVisible = 10, $infinityScrollGetFromSelector="", $infinityScrollAppendIntoSelector="")
            echo getPagination($totalPages, $page, "{$categoryURL}{page}{$args}", 10, ".Div{$currentCat['clean_name']}Section", ".Div{$currentCat['clean_name']}Section");
            ?>
        </div>
        <?php
    }
}