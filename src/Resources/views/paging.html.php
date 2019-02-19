<?php
/**
 * @var \Pimcore\Templating\PhpEngine $this
 * @var \Pimcore\Templating\PhpEngine $view
 * @var \Pimcore\Templating\GlobalVariables $app
 */

/**
 * @var $addClass string
 * @var $totalItemCount
 * @var $firstPageInRange
 * @var $pagesInRange array
 * @var $lastPageInRange
 * @var $showFirstLast bool
 * @var $hideCount bool
 * @var $fist
 * @var $last
 */

$leftIcon = 'fa fa-angle-left';
$rightIcon = 'fa fa-angle-right';

$hideCount = $this->hideCount ?: false;
$addClassArray = [];
if( $hideCount ){
    $addClassArray[] = 'no-paging--count';
}
if( $addClass != '' ){
    $addClassArray[] = $addClass;
}

if ($pageParam == '' ) {
    $pageParam = 'page';
}

if($this->pageCount > 1){ ?>

<div class="row <?= implode(' ', $addClassArray) ?>">

    <?php if( !$hideCount ){ ?>
	<div class="col col-xs-12 col-sm-4 has-paging--count">
        <div class="row row--gutter-with-10">
            <div class="col-md-12 text-nowrap">
                <?= $totalItemCount ?> <?= $totalItemCount == 1 ? $this->translate('paging.ergebnis') : $this->translate('paging.ergebnisse') ?>
            </div>
        </div>
	</div>
	<?php } ?>

	<div class="col col-xs-12 <?= $hideCount ? '' : 'col-sm-8' ?> has-paging--list text-center text-md-right">
        <nav aria-label="<?= $this->translate('sr.pageination-navigation') ?>" class="d-ib">
            <ul class="pagination js-filter-form-paging">

            <?php if (isset($previous)){
                $this->placeholder('headPrev')->set('<link rel="prev" href="' . $this->pimcoreUrl([$pageParam => $previous]) . '">');
            ?>
                <li class="li-has-arrow page-item">
                    <a class="page-link" href="<?= $this->pimcoreUrl([$pageParam => $previous]); ?>" data-page="<?= $previous ?>" rel="prev">
                        <span class="icon <?= $leftIcon ?>"></span>
                    </a>
                </li>
            <?php } else { ?>
                <li class="disabled li-has-arrow page-item">
                    <a class="page-link" href="#">
                        <span class="icon <?= $leftIcon ?>"></span>
                    </a>
                </li>
            <?php }

            if( $showFirstLast ){
                // add ... if there many pages ... so you can see the link to the FIRST page ... like: < 1 ... 5 6 (7) 8 9 ... 11 >
                if( $firstPageInRange != $first ){
                ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $this->pimcoreUrl([$pageParam => $first]); ?>" data-page="<?= $first ?>">
                            <?= $first; ?>
                        </a>
                    </li>
                <?php
                }
                if( $firstPageInRange > $first+1 && $firstPageInRange < $first+3 ){
                    ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $this->pimcoreUrl([$pageParam => $first+1]); ?>" data-page="<?= $first+1 ?>">
                            <?= $first+1; ?>
                        </a>
                    </li>
                    <?php
                }
                if( $firstPageInRange > $first+2 ){
                    ?>
                    <li class="disabled page-item">
                        <a class="page-link" href="#">...</a>
                    </li>
                    <?php
                }
            }

            foreach ($pagesInRange as $page){
                if ($page != $current){
                    ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $this->pimcoreUrl([$pageParam => $page]); ?>" data-page="<?= $page ?>">
                            <?= $page; ?>
                        </a>
                    </li>
                <?php } else { ?>
                    <li class="active page-item">
                        <a class="page-link" href="" data-page="<?= $page ?>">
                            <?= $page; ?> <span class="sr-only">(<?= $this->translate('sr.page.current') ?>)</span>
                        </a>
                    </li>
                <?php
                }
            }

            if( $showFirstLast ){
                // add ... if there many pages ... so you can see the link to the LAST page ... like: < 1 ... 5 6 (7) 8 9 ... 12 >
                if( $lastPageInRange < $last-2 ){
                    ?>
                    <li class="disabled page-item">
                        <a class="page-link" href="#">...</a>
                    </li>
                    <?php
                }
                if( $lastPageInRange < $last-1 && $lastPageInRange > $last-3 ){
                    ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $this->pimcoreUrl([$pageParam => $last-1]); ?>" data-page="<?= $last-1 ?>">
                            <?= $last-1; ?>
                        </a>
                    </li>
                    <?php
                }

                if( $lastPageInRange != $last ){
                ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $this->pimcoreUrl([$pageParam => $last]); ?>" data-page="<?= $last ?>">
                            <?= $last; ?>
                        </a>
                    </li>
                <?php
                }
            }

            if (isset($next)){
                $this->placeholder('headNext')->set('<link rel="next" href="' . $this->pimcoreUrl([$pageParam => $next]) . '">');
            ?>
                <li class="li-has-arrow page-item">
                    <a class="page-link" href="<?= $this->pimcoreUrl([$pageParam => $next]); ?>" data-page="<?= $next ?>" rel="next">
                        <span class="icon <?= $rightIcon ?>"></span>
                    </a>
                </li>
            <?php } else{ ?>
                <li class="disabled li-has-arrow page-item">
                    <a class="page-link" href="#">
                        <span class="icon <?= $rightIcon ?>"></span>
                    </a>
                </li>
            <?php } ?>

            </ul>
        </nav>
	</div>
</div>
<?php } ?>
