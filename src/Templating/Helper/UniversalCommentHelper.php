<?php
namespace MercuryKojo\Bundle\UniversalCommentBundle\Templating\Helper;

use Pimcore\File;
use Pimcore\Model\DataObject\UniversalComment;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Helper\Helper;
use Zend\Paginator\Paginator;

class UniversalCommentHelper extends Helper
{
    public function getName()
    {
        return 'Universal Comment Helper';
    }

    private $engine;
    private $request;

    public function __construct(EngineInterface $engine, RequestStack $requestStack)
    {
        $this->engine = $engine;
        $this->request = $requestStack->getMasterRequest();
    }

    /**
     * @param string $id
     * @param array $config
     * - template (string) [UniversalCommentBundle::container.html.php]
     * - itemCountPerPage (int) [6]
     * - pageParam (string) [page]
     * - page (int) [optional] overrides automatic page from request
     * - pageRange (int) [optional] overrides zend pagination default
     * - replyable (bool) [false]
     * - validatedOnly (bool) [false] only validated comments will be shown
     *
     * @throws \Exception
     * @return string
     */
    public function __invoke(string $id, array $config = [])
    {
        $id = File::getValidFilename($id);
        $page = $config['page'];
        if ($page == '') {
            $page = $this->request->get(isset($config['pageParam']) ? $config['pageParam'] : 'page', 1);
        }

        $comments = new UniversalComment\Listing();
        $comments->addConditionParam('sourceId = :id AND parentComment__id IS NULL', ['id' => $id]);
        if (isset($config['validatedOnly']) && $config['validatedOnly']) {
            $comments->addConditionParam('validated = 1');
        }

        $comments->setOrderKey('o_creationDate');
        $comments->setOrder('DESC');

        $paginator = new Paginator($comments);
        $paginator->setItemCountPerPage(isset($config['itemCountPerPage']) ? $config['itemCountPerPage'] : 6);
        $paginator->setCurrentPageNumber($page);
        if (isset($config['pageRange'])) {
            $paginator->setPageRange($config['pageRange']);
        }

        $template = 'UniversalCommentBundle::container.html.twig';
        if ($config['template'] != '') {
            $template = $config['template'];
        }

        // get subcomments if replyable
        $subCommentsArray = [];
        if (isset($config['replyable']) && $config['replyable']) {
            /**
             * @var UniversalComment $comment
             */
            foreach ($paginator as $comment) {
                $subComments = new UniversalComment\Listing();
                $subComments->addConditionParam('parentComment__id = :id', ['id' => $comment->getId()]);
                if (isset($config['validatedOnly']) && $config['validatedOnly'] == true) {
                    $subComments->addConditionParam('validated = 1');
                }
                $subComments->setOrderKey('o_creationDate');
                $subComments->setOrder('DESC');

                if ($subComments->getCount()) {
                    $subCommentsArray[$comment->getId()] = $subComments->load();
                }
            }
        }

        return $this->engine->render($template, [
            'replyable' => ($config['replyable'] == true),
            'comments' => $paginator,
            'pagingVariables' => get_object_vars($paginator->getPages('Sliding')),
            'pageParam' => isset($config['pageParam']) ? $config['pageParam'] : 'page',
            'id' => $id,
            'validatedOnly' => ($config['validatedOnly'] == true),
            'subComments' => $subCommentsArray
        ]);
    }


}