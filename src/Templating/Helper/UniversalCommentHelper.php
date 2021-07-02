<?php
namespace MercuryKojo\Bundle\UniversalCommentBundle\Templating\Helper;

use Knp\Component\Pager\PaginatorInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\UniversalComment;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Helper\Helper;
use Zend\Paginator\Paginator;

class UniversalCommentHelper extends Helper
{
    private PaginatorInterface $paginator;
    private RequestStack $requestStack;

    public function getName()
    {
        return 'Universal Comment Helper';
    }

    private $engine;
    private $request;

    public function __construct(EngineInterface $engine, RequestStack $requestStack, PaginatorInterface $paginator)
    {
        $this->engine = $engine;
        $this->paginator = $paginator;
        $this->requestStack = $requestStack;
    }

    /**
     * @param string|null $id
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
     * @return string|UniversalCommentHelper
     */
    public function __invoke(string $id = null, array $config = [])
    {
        $this->request = $this->requestStack->getMainRequest();

        if ($id === null) {
            return $this;
        }
        $id = File::getValidFilename($id);
        $page = array_key_exists('page', $config) ? $config['page'] : '';
        if ($page == '') {
            $page = $this->request->get($config['pageParam'] ?? 'page', 1);
        }

        $comments = $this->getCommentListing($id, $config);

        $paginator = $this->paginator->paginate($comments, $page, $config['itemCountPerPage'] ?? 6);
        if (isset($config['pageRange'])) {
            $paginator->setPageRange($config['pageRange']);
        }

        $template = '@UniversalComment/container.html.twig';
        if (array_key_exists('template', $config)) {
            $template = $config['template'];
        }

        // get subcomments if replyable
        $subCommentsArray = [];
        if (array_key_exists('replyable', $config) && $config['replyable']) {
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
            'replyable' => (array_key_exists('replyable', $config) && $config['replyable']),
            'comments' => $paginator,
            'pagingVariables' => $paginator->getPaginationData(),
            'pageParam' => isset($config['pageParam']) ? $config['pageParam'] : 'page',
            'id' => $id,
            'validatedOnly' => (array_key_exists('validatedOnly', $config) && $config['validatedOnly'] == true),
            'subComments' => $subCommentsArray
        ]);
    }

    /**
     * @param $id
     * @param array $config
     * @return int
     */
    public function getCommentCount($id, array $config = []) {
        $id = File::getValidFilename($id);
        $comments = $this->getCommentListing($id, $config);
        return $comments->count();
    }

    /**
     * @param $id
     * @param array $config
     * @return UniversalComment
     */
    public function getLatestComment($id, array $config = []) {
        $id = File::getValidFilename($id);
        $comments = $this->getCommentListing($id, $config);
        return $comments->current();
    }

    /**
     * @param string $id
     * @param array $config
     * @return UniversalComment\Listing
     */
    public function getCommentListing(string $id, array $config = []) {
        $comments = new UniversalComment\Listing();
        $comments->addConditionParam('sourceId = :id AND parentComment__id IS NULL', ['id' => $id]);
        if (isset($config['validatedOnly']) && $config['validatedOnly']) {
            $comments->addConditionParam('validated = 1');
        }

        $comments->setOrderKey('o_creationDate');
        $comments->setOrder('DESC');
        return $comments;
    }
}
