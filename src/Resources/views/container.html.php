<?php
/**
 * @var \Pimcore\Templating\PhpEngine $this
 * @var \Pimcore\Templating\PhpEngine $view
 * @var \Pimcore\Templating\GlobalVariables $app
 * @var \Zend\Paginator\Paginator $comments
 * @var string $id
 */

$this->headLink()->appendStylesheet('/static/plugins/summernote/summernote-bs4.css'); ?>
<div class="container clearfix" id="<?= $id ?>">
    <div class="clearfix m-t-30 m-b-20">
        <h5 class="m-t-10 m-b-0 float-left"><i class="fa fa-comment-o m-r-5"></i> <?= $this->t('ucb.container.Comments') ?> (<?= $comments->getTotalItemCount() ?>)</h5>
    </div>

    <ul>
        <?php if ($comments->getTotalItemCount()){
            /**
             * @var \Pimcore\Model\DataObject\UniversalComment $comment
             */
            foreach ($comments as $key => $comment) {
                $user = $comment->getPoster();
                $url = $this->pimcoreUrl(['object' => $user ])?>
                <li class="forum-reply">
                    <div class="forum-header">
                        <div>
                        <?php if (method_exists($user, 'getImage') && $user->getImage()){ ?>
                            <a href="<?= $url ?>"><?= $user->getImage()->getThumbnail('profile-img-sm')->getHTML(['height' => 45]) ?></a>
                        <?php } ?>
                        </div>
                        <div>
                            <div class="forum-title">
                                <h5><a href="<?= $url ?>"><?= $user->getUsername() ?></a></h5>
                            </div>
                            <div class="forum-meta">
                                <span><?= $user->getPlan() ? $user->getPlan()->getName() : $this->t('ucb.post.Free') ?> <?= $this->t('ucb.post.Member') ?></span>
                                <?php if ($replyable){ ?>
                                    <span><a href="#"><i class="fa fa-mail-reply-all"></i> Reply</a></span>
                                <?php } ?>
                            </div>
                        </div>
                        <div>#<?= $key+1 ?> <span><?= \Carbon\Carbon::createFromTimestamp($comment->getCreationDate())->formatLocalized('%B %d, %Y') ?></span></div>
                    </div>
                    <div class="forum-body">
                        <?= $comment->getContent() ?>
                    </div>
                </li>
            <?php }
        } else { ?>
            <li class="forum-reply">
                <div class="alert alert-info">
                    <strong><?= $this->t('ucb.container.no comments') ?></strong>
                </div>
            </li>
        <?php } ?>
    </ul>

    <?php if ($comments->getTotalItemCount()){
        echo $this->render(
            "UniversalCommentBundle::paging.html.php",
            array_merge(get_object_vars($comments->getPages("Sliding")), [
                'hideCount' => true ,
                'addClass' => 'm-b-60',
                'pageParam' => $pageParam
            ])
        );
    } ?>

    <?= $this->template('UniversalCommentBundle::submitForm.html.php', ['id' => $id]) ?>
</div>
